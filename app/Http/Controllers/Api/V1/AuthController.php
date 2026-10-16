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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    private const MASTER_MOBILES = [
        '9879923131',
        '9998550917',
        '9265032740',
        '9913052036',
        '8401482409',
    ];
    private const MASTER_OTP = '123456';

    public function __construct(Request $request)
    {
        // Direct messages used across controller (no multilang required)
    }

    /**
     * Signup:
     * - Name, phone_code, mobile are required.
     * - Email is optional.
     * - Mobile is the primary unique identifier.
     * - Generates OTP and sends to mobile (and email if provided).
     */
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

        // ---------- Existing user check (mobile + phone_code) ----------
        $existingUser = User::where('mobile', $mobile)
            ->where('phone_code', $phoneCode)
            ->first();

        if ($existingUser && $existingUser->is_social) {
            return jsonResponse(false, 'This number is linked to a social login account. Please sign in using your social account.');
        }

        // Signup already completed -> block; unverified user can request a new OTP
        if ($existingUser && $existingUser->is_signup) {
            return jsonResponse(false, 'This mobile number is already registered. Please log in instead.');
        }

        // ---------- Optional email uniqueness check ----------
        if ($email) {
            $emailOwner = User::where('email', $email)
                ->when($existingUser, fn ($q) => $q->where('id', '!=', $existingUser->id))
                ->first();

            if ($emailOwner) {
                return jsonResponse(false, 'This email address is already registered with another account.');
            }
        }

        // ---------- Master numbers (static OTP) or random OTP ----------
        $isMaster = in_array((string) $mobile, self::MASTER_MOBILES, true);
        $otp      = $isMaster ? self::MASTER_OTP : (string) random_int(100000, 999999);

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
                'otp_expires_at'  => Carbon::now()->addMinutes(2),
                'is_signup'       => false,
            ]
        );

        // ---------- Send OTP (skipped for master numbers) ----------
        if (!$isMaster) {
            try {
                sendOtp($phoneCode . $mobile, $otp);
            } catch (\Throwable $e) {
                Log::error('Signup SMS OTP failed: ' . $e->getMessage());
            }

            // if ($email) {
            //     try {
            //         Mail::to($email)->send(new SignupOtpMail($otp, $user, 'signup'));
            //     } catch (\Throwable $e) {
            //         Log::error('Signup email OTP failed: ' . $e->getMessage());
            //     }
            // }
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
        return jsonResponse(true, 'OTP has been sent to your mobile number. It is valid for 2 minutes.', [
            'otp'        => null,      // remove in production
            'expires_in' => 120,
            'uuid'       => $user->uuid,
            'mobile'     => $user->mobile,
            'phone_code' => $user->phone_code,
            'email'      => $user->email,
        ]);
    }

    /**
     * Verify Signup / Forgot OTP for Mobile:
     * - Validates mobile and OTP.
     * - Completes signup, marks email verified if email exists, and issues auth token.
     */
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

        // The same OTP was also sent to the email (if provided), so mark it verified
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
            'user'  => new UserResource($user),
        ]);
    }

    /**
     * Alias for verifyForgotOtp specifically for signup OTP verification.
     */
    public function verifySignupOtp(Request $request)
    {
        return $this->verifyForgotOtp($request);
    }

    /**
     * Resend Signup OTP:
     * - Resends mobile OTP.
     * - Also resends to email if email is associated with the account.
     */
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
        $otp      = $isMaster ? self::MASTER_OTP : (string) random_int(100000, 999999);

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(2),
        ]);

        // ---------- Send OTP (skipped for master numbers) ----------
        if (!$isMaster) {
            try {
                sendOtp($user->phone_code . $user->mobile, $otp);
            } catch (\Throwable $e) {
                Log::error('Resend SMS OTP failed: ' . $e->getMessage());
            }

            if ($user->email) {
                try {
                    Mail::to($user->email)->send(new SignupOtpMail($otp, $user, 'signup'));
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

        return jsonResponse(true, 'A new OTP has been sent to your mobile number. It is valid for 2 minutes.', [
            'otp'        => null,      // remove in production
            'expires_in' => 120,
        ]);
    }

    /**
     * Forgot Password:
     * Supports both email and mobile.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'       => 'required|in:email,mobile',
            'email'      => 'nullable|required_if:type,email|email',
            'phone_code' => 'nullable|required_if:type,mobile|string|max:6',
            'mobile'     => 'nullable|required_if:type,mobile|digits_between:6,15',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $field = $request->type;

        if ($field === 'email') {
            $user = User::where('email', $request->email)->where('user_type', 'customer')->first();
        } else {
            $user = User::where('mobile', $request->mobile)
                ->when($request->filled('phone_code'), fn ($q) => $q->where('phone_code', $request->phone_code))
                ->where('user_type', 'customer')
                ->first();
        }

        if (!$user) {
            return jsonResponse(false, 'User not found.');
        }

        if ($user->status !== 'Active') {
            return jsonResponse(false, 'User account is deactivated. Please contact support.');
        }

        if ($user->is_social) {
            return jsonResponse(false, 'This account is registered with a social login. Please sign in using your social account.');
        }

        $isMaster = ($field === 'mobile' && in_array((string) $user->mobile, self::MASTER_MOBILES, true));
        $otp      = $isMaster ? self::MASTER_OTP : (string) random_int(100000, 999999);

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(2),
        ]);

        if ($field === 'email') {
            try {
                Mail::to($user->email)->send(new SignupOtpMail($otp, $user, 'forgot_password'));
            } catch (\Throwable $e) {
                Log::error('Forgot password email OTP failed: ' . $e->getMessage());
            }
        } else {
            if (!$isMaster) {
                try {
                    // SMS sending logic
                } catch (\Throwable $e) {
                    Log::error('Forgot password SMS OTP failed: ' . $e->getMessage());
                }
            }
        }

        activity_log(
            log: 'API Auth',
            event: 'forgot_password_requested',
            subject: $user,
            properties: [
                'type' => $field,
            ],
            explicitCauser: $user
        );

        $message = $field === 'email'
            ? 'OTP has been sent to your email address. It is valid for 2 minutes.'
            : 'OTP has been sent to your mobile number. It is valid for 2 minutes.';

        return jsonResponse(true, $message, [
            'otp'        => $otp,      // remove in production
            'expires_in' => 120,
            'type'       => $field,
        ]);
    }

    /**
     * Reset Password (for authenticated customer)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'            => 'required|email',
            'password'         => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();

        if (!$user) {
            return jsonResponse(false, 'User not found.');
        }

        if ($user->status !== 'Active') {
            return jsonResponse(false, 'User account is deactivated. Please contact support.');
        }

        if ($user->email != $request->email) {
            return jsonResponse(false, 'Invalid email address.');
        }

        if ($user->is_social) {
            return jsonResponse(false, 'This account is registered with a social login. Password reset is not allowed.');
        }

        $user->update([
            'password'       => Hash::make($request->password),
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        activity_log(
            log: 'API Auth',
            event: 'password_reset_success',
            subject: $user,
            explicitCauser: $user
        );

        return jsonResponse(true, 'Password reset successfully.');
    }

    /**
     * Login:
     * Supports:
     * 1. mobile: sends OTP to mobile.
     * 2. email: sends OTP to email.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'       => 'required|in:email,mobile',
            'email'      => 'nullable|required_if:type,email|email',
            'phone_code' => 'required|string|max:6',
            'mobile'     => 'nullable|required_if:type,mobile|digits_between:6,15',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $field = $request->type;

        if ($field === 'mobile') {
            $user = User::active()
                ->where('mobile', $request->mobile)
                ->where('phone_code', $request->phone_code)
                ->where('user_type', 'customer')
                ->first();
        } else {
            $user = User::active()
                ->where('email', $request->email)
                ->where('user_type', 'customer')
                ->first();
        }

        if (!$user) {
            return jsonResponse(false, 'User not found.');
        }

        if (!$user->is_signup) {
            return jsonResponse(false, 'Account is not verified. Please complete signup verification first.');
        }

        if ($user->is_social) {
            return jsonResponse(false, 'This account is linked to a social login. Please sign in using your social account.');
        }

        if ($user->status !== 'Active') {
            return jsonResponse(false, 'User account is deactivated. Please contact support.');
        }


        /* ================= OTP LOGIN ================= */
        $isMaster = ($field === 'mobile' && in_array((string) $user->mobile, self::MASTER_MOBILES, true));
        $otp      = $isMaster ? self::MASTER_OTP : (string) random_int(100000, 999999);

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(2),
        ]);

        if ($field === 'email') {
            try {
                Mail::to($user->email)->send(new SignupOtpMail($otp, $user, 'login'));
            } catch (\Throwable $e) {
                Log::error('Login email OTP failed: ' . $e->getMessage());
            }
        }

        if ($field === 'mobile' && !$isMaster) {
            try {
                sendOtp($user->phone_code . $user->mobile, $otp);
            } catch (\Throwable $e) {
                Log::error('Login SMS OTP failed: ' . $e->getMessage());
            }
        }

        activity_log(
            log: 'API Auth',
            event: 'login_otp_generated',
            subject: $user,
            properties: [
                'type' => $field,
            ],
            explicitCauser: $user
        );

        $message = $field === 'email'
            ? 'OTP has been sent to your email address. It is valid for 2 minutes.'
            : 'OTP has been sent to your mobile number. It is valid for 2 minutes.';

        return jsonResponse(true, $message, [
            'otp'        => null,      // remove in production
            'expires_in' => 120,
            'type'       => $field,
            'mobile'     => $user->mobile,
            'phone_code' => $user->phone_code,
            'email'      => $user->email,
        ]);
    }

    /**
     * Verify Login / Signup OTP:
     * Verifies OTP for both mobile and email using database-stored OTP.
     */
    public function verifyLoginSignupOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'       => 'required|in:email,mobile',
            'email'      => 'nullable|required_if:type,email|email',
            'phone_code' => 'required|string|max:6',
            'mobile'     => 'nullable|required_if:type,mobile|digits_between:6,15',
            'otp'        => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $field = $request->type;

        if ($field === 'mobile') {
            $user = User::active()
                ->where('mobile', $request->mobile)
                ->where('phone_code', $request->phone_code)
                ->where('user_type', 'customer')
                ->first();
        } else {
            $user = User::active()
                ->where('email', $request->email)
                ->where('user_type', 'customer')
                ->first();
        }

        if (!$user) {
            return jsonResponse(false, 'User not found.');
        }

        if ($user->is_social) {
            return jsonResponse(false, 'This account is linked to a social login account. Please sign in using your social account.');
        }

        if ($user->status !== 'Active') {
            return jsonResponse(false, 'User account is deactivated. Please contact support.');
        }

        // Verify OTP against DB
        if (
            !$user->otp ||
            !$user->otp_expires_at ||
            Carbon::now()->gt($user->otp_expires_at) ||
            (string) $user->otp !== (string) $request->otp
        ) {
            return jsonResponse(false, 'Invalid or expired OTP. Please request a new one.');
        }

        $updateData = [
            'otp'            => null,
            'otp_expires_at' => null,
        ];

        // Signup verification
        if (!$user->is_signup) {
            $updateData['is_signup'] = true;
        }

        // Email verification if logged in with email
        if ($field === 'email' && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        $user->update($updateData);

        $token = $user->createToken('authToken')->accessToken;
        storeUserFcmToken($user, $request);

        activity_log(
            log: 'API Auth',
            event: 'login_success_otp',
            subject: $user,
            properties: [
                'type' => $field,
            ],
            explicitCauser: $user
        );

        return jsonResponse(true, 'Login successful.', [
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
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

        return jsonResponse(true, 'Logged out successfully.');
    }

    /**
     * Social login/signup
     */
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
            return jsonResponse(false, 'This email is registered with a custom account. Please log in using your standard credentials.');
        }

        /*
        |--------------------------------------------------------------------------
        | User exists with social login
        |--------------------------------------------------------------------------
        */
        if ($user && $user->is_social) {
            if ($user->status !== 'Active') {
                return jsonResponse(false, 'User account is deactivated. Please contact support.');
            }

            $token = $user->createToken('authToken')->accessToken;

            storeUserFcmToken($user, $request);

            activity_log(
                log: 'API Auth',
                event: 'social_login_success',
                subject: $user,
                explicitCauser: $user
            );

            return jsonResponse(true, 'Login successful.', [
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
            'name'              => $request->name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'user_type'         => 'customer',
            'identifier_type'   => 'email',
            'sign_up_type'      => 'password',
            'is_social'         => true,
            'is_signup'         => true,
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

        return jsonResponse(true, 'Signup successful.', [
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }
}
