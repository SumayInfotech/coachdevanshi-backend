<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\SignupOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    private const MASTER_MOBILES = ['9879923131', '9998550917'];
    private const MASTER_OTP     = '123456';

    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }
    public function signup(Request $request)
    {
        // ---------- Validation ----------
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:20',
            'last_name'  => 'nullable|string|max:20',
            'phone_code' => 'required|string|max:6',
            'mobile'     => 'required|digits_between:6,15',
            'email'      => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $mobile    = $request->mobile;
        $phoneCode = $request->phone_code;
        $email     = $request->email ?: null;

        // ---------- Existing user check (mobile is the main identifier) ----------
        $existingUser = User::where('mobile', $mobile)
            ->where('phone_code', $phoneCode)
            ->first();

        if ($existingUser && $existingUser->is_social) {
            return jsonResponse(false, 'This number is linked to a social login account. Please sign in using your social account.');
        }

        // signup already completed -> block; an unverified user can request a new OTP
        if ($existingUser && $existingUser->is_signup) {
            return jsonResponse(false, 'This mobile number is already registered. Please log in instead.');
        }

        // ---------- Optional email check ----------
        if ($email) {
            $emailOwner = User::where('email', $email)
                ->when($existingUser, fn ($q) => $q->where('id', '!=', $existingUser->id))
                ->first();

            if ($emailOwner) {
                return jsonResponse(false, 'This email address is already registered with another account.');
            }
        }

        // ---------- Master numbers (static OTP) ----------
        $masterMobiles = ['9879923131', '9998550917'];
        $masterOtp     = '123456';

        $isMaster = in_array((string) $mobile, $masterMobiles, true);
        $otp      = $isMaster ? $masterOtp : random_int(1000, 9999);

        $user = User::updateOrCreate(
            [
                'mobile'     => $mobile,
                'phone_code' => $phoneCode,
            ],
            [
                'name'            => $request->name,
                'last_name'       => $request->last_name,
                'email'           => $email,
                'identifier_type' => 'mobile',
                'sign_up_type'    => 'otp',
                'otp'             => $otp,
                'otp_expires_at'  => Carbon::now()->addMinutes(10),
                'is_signup'       => false,
            ]
        );

        // ---------- Send OTP (skipped for master numbers) ----------
        if (!$isMaster) {
            try {
                // sendOtp($otp, $user);   // SMS
            } catch (\Throwable $e) {
                Log::error('Signup SMS OTP failed: ' . $e->getMessage());
            }

            if ($email) {
                try {
                    Mail::to($email)->send(new SignupOtpMail($otp, $user));
                } catch (\Throwable $e) {
                    Log::error('Signup email OTP failed: ' . $e->getMessage());
                }
            }
        }

        // ---------- Activity log ----------
        activity_log(
            log: 'API Auth',
            event: 'signup_otp_generated',
            subject: $user,
            properties: [
                'type'  => 'mobile',
                'email' => $email ? 'provided' : 'not_provided',
            ],
            explicitCauser: $user
        );

        // ---------- Response ----------
        return jsonResponse(true, 'OTP has been sent to your mobile number. It is valid for 10 minutes.', [
            'otp'        => $otp,      // remove in production
            'expires_in' => 600,
            'uuid'       => $user->uuid,
            'mobile'     => $user->mobile,
            'email'      => $user->email,
        ]);
    }


    public function verifyForgotOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_code' => 'required|string|max:6',
            'mobile'     => 'required|digits_between:6,15',
            'otp'        => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = User::active()
            ->where('phone_code', $request->phone_code)
            ->where('mobile', $request->mobile)
            ->where('user_type', 'customer')
            ->first();

        if (!$user) {
            return jsonResponse(false, 'No account was found with this mobile number.');
        }

        if ($user->is_social) {
            return jsonResponse(false, 'This number is linked to a social login account. Please sign in using your social account.');
        }

        // OTP validation
        if (
            (string) $user->otp !== (string) $request->otp
            || !$user->otp_expires_at
            || Carbon::now()->gt($user->otp_expires_at)
        ) {
            return jsonResponse(false, 'The OTP is invalid or has expired. Please request a new one.');
        }

        // Mark signup/login completed
        $updateData = [
            'otp'            => null,
            'otp_expires_at' => null,
            'is_signup'      => true,
        ];

        // the same OTP was also sent to the email (if provided), so mark it verified
        if ($user->email && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        $user->update($updateData);

        // Generate Passport token
        $token = $user->createToken('authToken')->accessToken;
        storeUserFcmToken($user, $request);

        activity_log(
            log: 'API Auth',
            event: 'signup_otp_verified',
            subject: $user,
            properties: [
                'type' => 'mobile',
            ],
            explicitCauser: $user
        );

        return jsonResponse(true, 'OTP verified successfully.', [
            'token' => $token,
            'uuid'  => $user->uuid,
            'type'  => 'mobile',
        ]);
    }

    public function resendSignupOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_code' => 'required|string|max:6',
            'mobile'     => 'required|digits_between:6,15',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = User::active()
            ->where('phone_code', $request->phone_code)
            ->where('mobile', $request->mobile)
            ->where('user_type', 'customer')
            ->first();

        if (!$user) {
            return jsonResponse(false, 'No account was found with this mobile number.');
        }

        if ($user->is_social) {
            return jsonResponse(false, 'This number is linked to a social login account. Please sign in using your social account.');
        }

        if ($user->is_signup) {
            return jsonResponse(false, 'This mobile number is already verified. Please log in instead.');
        }

        // ---------- Generate OTP (master numbers get the static OTP) ----------
        $isMaster = in_array((string) $user->mobile, self::MASTER_MOBILES, true);
        $otp      = $isMaster ? self::MASTER_OTP : random_int(1000, 9999);

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // ---------- Send OTP (skipped for master numbers) ----------
        if (!$isMaster) {
            try {
                // sendOtp($otp, $user);   // SMS
            } catch (\Throwable $e) {
                Log::error('Resend SMS OTP failed: ' . $e->getMessage());
            }

            if ($user->email) {
                try {
                    Mail::to($user->email)->send(new SignupOtpMail($otp, $user));
                } catch (\Throwable $e) {
                    Log::error('Resend email OTP failed: ' . $e->getMessage());
                }
            }
        }

        activity_log(
            log: 'API Auth',
            event: 'signup_otp_resent',
            subject: $user,
            properties: [
                'type' => 'mobile',
            ],
            explicitCauser: $user
        );

        return jsonResponse(true, 'A new OTP has been sent to your mobile number. It is valid for 10 minutes.', [
            'otp'        => $otp,      // remove in production
            'expires_in' => 600,
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'   => 'required|in:email',
            'email'  => 'required_if:type,email|email',
            'mobile' => 'required_if:type,mobile|max:15',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $field = $request->type;
        $value = $request->$field;

        $user = User::where($field, $value)->where('user_type','customer')->first();
        if (!$user) {
            return jsonResponse(false, __('api.user_not_found'));
        }
        if($user->status !== 'Active'){
            return jsonResponse(false, __('api.user_deactivated'));
        }
        if ($user->is_social) {
            return jsonResponse(false, __('api.social_login_required'));
        }
        $otp = rand(1000, 9999);
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);
        if($request->type == 'email'){
            Mail::to($value)->send(new SignupOtpMail($otp, $user,'forgot_password'));
        }

        activity_log(
            log: 'API Auth',
            event: 'forgot_password_requested',
            subject: $user,
            properties: [
                'type' => $field
            ],
            explicitCauser: $user 
        );
        return jsonResponse(true,  __('api.otp_generated'));
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'             => 'required|email',
            'password'          => 'required|min:6',
            'confirm_password'  => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();

        if (!$user) {
            return jsonResponse(false, __('api.user_not_found'));
        }
        if($user->status !== 'Active'){
            return jsonResponse(false, __('api.user_deactivated'));
        }

        if ($user->email != $request->email) {
            return jsonResponse(false, __('api.invalid_email'));
        }
        if ($user->is_social) {
            return jsonResponse(false, __('api.social_account_exists'));
        }

        $user->update([
            'password' => Hash::make($request->password),

            // optional cleanup
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        activity_log(
            log: 'API Auth',
            event: 'password_reset_success',
            subject: $user,
            explicitCauser: $user
        );

        return jsonResponse(true, __('api.password_reset_success'));
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'       => 'required|in:email',
            'login_type' => 'required|in:password,otp',
            'email'      => 'nullable|required_if:type,email|email',
            'mobile'     => 'nullable|required_if:type,mobile|max:15',
            'password'   => 'nullable|required_if:login_type,password|min:6',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $field = $request->type;
        $value = $request->$field;

        $user = User::active()->where($field, $value)
                    ->where('user_type', 'customer')
                    ->first();

        if (!$user) {
            return jsonResponse(false, __('api.user_not_found'));
        }

        if (!$user->is_signup) {
            return jsonResponse(false, __('api.user_not_verified'));
        }
        if ($user->is_social) {
            return jsonResponse(false, __('api.social_login_required'));
        }
        if($user->status !== 'Active'){
            return jsonResponse(false, __('api.user_deactivated'));
        }

        /* ================= PASSWORD LOGIN ================= */
        if ($request->login_type === 'password') {

            if (!Hash::check($request->password, $user->password)) {
                return jsonResponse(false, __('api.invalid_credentials'));
            }

            $token = $user->createToken('authToken')->accessToken;
            storeUserFcmToken($user, $request);
            
            activity_log(
                log: 'API Auth',
                event: 'login_success_password',
                subject: $user,
                explicitCauser: $user 
            );
            return jsonResponse(true, __('api.login_success'), [
                'token' => $token,
                'user'  => new UserResource($user),
            ]);
        }

        /* ================= OTP LOGIN ================= */
        $masterMobiles = ['9879923131','9265032740','9913052036','8401482409'];
        $masterOtp     = 1234;

        if ($request->type === 'mobile' && in_array($user->mobile, $masterMobiles)) {
            $user->update([
                'otp'            => $masterOtp,
                'otp_expires_at' =>  now()->addMinutes(10),
            ]);

            activity_log(
                log: 'API Auth',
                event: 'login_otp_generated',
                subject: $user,
                properties: [
                    'type' => $field
                ],
                explicitCauser: $user 
            );
            return jsonResponse(true, __('api.otp_generated'), [
                'otp' => $masterOtp,
                'expires_in' => 600,
                'email' => $request->email
            ]);
        } else {
            $otp = rand(1000, 9999);

            $user->update([
                'otp'            => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            if ($field === 'email') {
                Mail::to($user->email)->send(new SignupOtpMail($otp, $user, 'login'));
            }

            if ($field === 'mobile') {
                echo "pending for twillio ac";die;
                $phoneNumber = '+91' . $user->mobile; // adjust country code
                $otpResponse = sendOtp($phoneNumber);

                if (!$otpResponse['success']) {
                    return jsonResponse(false, __('api.otp_failed'));
                }
            }
            activity_log(
                log: 'API Auth',
                event: 'login_otp_generated',
                subject: $user,
                properties: [
                    'type' => $field
                ],
                explicitCauser: $user 
            );

            return jsonResponse(true, __('api.otp_generated'), [
                'otp' => $otp ?? '',
                'expires_in' => 600,
                'email' => $user->email
            ]);
        }
    }

    public function verifyLoginSignupOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'   => 'required|in:email',
            'email'  => 'nullable|required_if:type,email|email',
            'mobile' => 'nullable|required_if:type,mobile|max:15',
            'otp'    => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $field = $request->type;
        $value = $request->$field;

        $user = User::active()->where($field, $value)
                    ->where('user_type', 'customer')
                    ->first();
        if(!$user){
            return jsonResponse(false, __('api.user_not_found'));
        }
        if ($user->is_social) {
            return jsonResponse(false, __('api.social_account_exists'));
        }
        if($user->status !== 'Active'){
            return jsonResponse(false, __('api.user_deactivated'));
        }

        /* ================= EMAIL OTP (DB BASED) ================= */
        if ($field === 'email') {

            if (
                !$user->otp ||
                !$user->otp_expires_at ||
                Carbon::now()->gt($user->otp_expires_at) ||
                $user->otp !== $request->otp
            ) {
                return jsonResponse(false, __('api.otp_invalid'));
            }
            $updateData = [
                'otp' => null,
                'otp_expires_at' => null,
            ];

            // Signup verification
            if (!$user->is_signup) {
                $updateData['is_signup'] = true;
                $updateData['email_verified_at'] = now();
            }

            $user->update($updateData);
        }

        /* ================= MOBILE OTP (TWILIO VERIFY) ================= */
        if ($field === 'mobile') {
            echo "pending for twillio ac";die;
            $phoneNumber = '+91' . $user->mobile;

            $verify = verifyOtp($phoneNumber, $request->otp);

            if (!$verify['success']) {
                return jsonResponse(false, __('api.otp_invalid'));
            }
        }

        $token = $user->createToken('authToken')->accessToken;
        storeUserFcmToken($user, $request);

        activity_log(
            log: 'API Auth',
            event: 'login_success_otp',
            subject: $user,
            explicitCauser: $user 
        );

        return jsonResponse(true, __('api.login_success'), [
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'fcm_token'   => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return jsonResponse(false, $validator->errors()->first());
        // }
        $user = $request->user();

        if ($request->filled('fcm_token')) {
           UserFcmToken()->where('user_uuid', $user->uuid)->where('fcm_token', $request->fcm_token)->delete();
        }
        $request->user()->token()->revoke();

        activity_log(
            log: 'API Auth',
            event: 'logout',
            subject: $request->user()
        );
        return jsonResponse(true,  __('api.logout_success'));
    }

    //Social login/signup
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang'      => 'nullable|string',
            'name'      => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email'     => 'required|email',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = User::where('email', $request->email)
                    ->where('user_type', 'customer')
                    ->first();

        /*
        |--------------------------------------------------------------------------
        | User exists with custom login
        |--------------------------------------------------------------------------
        */
        if ($user && !$user->is_social) {
            return jsonResponse(false,__('api.custom_login_required'));
        }

        /*
        |--------------------------------------------------------------------------
        | User exists with social login
        |--------------------------------------------------------------------------
        */
        if ($user && $user->is_social) {
            if($user->status !== 'Active'){
                return jsonResponse(false, __('api.user_deactivated'));
            }

            $token = $user->createToken('authToken')->accessToken;

            storeUserFcmToken($user, $request);

            activity_log(
                log: 'API Auth',
                event: 'social_login_success',
                subject: $user,
                explicitCauser: $user
            );

            return jsonResponse(true, __('api.login_success'), [
                'token' => $token,
                'user'  => new UserResource($user),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create new social user
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'user_type' => 'customer',
            'identifier_type' => 'email',
            'sign_up_type' => 'password',
            'is_social' => true,
            'is_signup' => true,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('authToken')->accessToken;

        storeUserFcmToken($user, $request);

        activity_log(
            log: 'API Auth',
            event: 'social_signup_success',
            subject: $user,
            explicitCauser: $user
        );

        return jsonResponse(true, __('api.signup_success'), [
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }
}
