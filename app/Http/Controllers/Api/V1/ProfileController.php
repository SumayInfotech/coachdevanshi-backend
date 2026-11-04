<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\SignupOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function show(Request $request)
    {
        $user =  auth()->user();

        activity_log(
            log: 'API Profile',
            event: 'profile_viewed',
            subject: $user,
        );
        return jsonResponse(true, __('api.profile_fetched'), [
                'token' => null,
                'user'  => new UserResource($user),
            ]);
    } 

    public function getStatesOrCities(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'state_id' => 'nullable|numeric|exists:states,id',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }
        $state_id = $request->input('state_id');
        if ($state_id) {
            $cities = City::select('id','name','state_id')->where('state_id', $state_id)->orderBy('name','asc')->get();
            
            if ($cities->isEmpty()) {
                return jsonResponse(true, __('api.cities_not_found'));
            }

            return jsonResponse(true, __('api.cities_fetched'), $cities);
        }
        $states = State::select('id','name')->where('country_id',101)->orderBy('name','asc')->get();        
        if ($states->isEmpty()) {
            return jsonResponse(true, __('api.states_not_found'));
        }        
        return jsonResponse(true, __('api.states_fetched'), $states);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'nullable|max:255',
            'last_name'         => 'nullable|max:255',
            'mobile'         => 'nullable|numeric',
            'profile_image' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }
        $user = auth()->user();

        // A new mobile number must go through send-mobile-otp / verify-mobile-otp
        if ($request->filled('mobile') && (string) $request->mobile !== (string) $user->mobile) {
            return jsonResponse(false, __('api.mobile_change_requires_otp'));
        }

        $oldData = [
            'name'      => $user->name,
            'last_name'      => $user->last_name,
            'mobile'    => $user->mobile,
            'email'    => $user->email,
            'profile_image' => $user->profile_image,
        ];
        $profileImage = $user->profile_image ?? ''; 
        if($request->profile_image){
            $profileImage = uploadFile($request->file('profile_image'), 'customer',$user->profile_image);
        }
       
        // $user->update([
        //     'name' => $request->name,
        //     'city_id' => $request->city_id,
        //     'state_id' => $request->state_id,
        //     'password' => $request->password ? Hash::make($request->password) : null,
        //     'profile_image'     => $profileImage,
        //     'is_profile_completed'     => true,
        // ]);
        // Only fields that were sent are changed (a photo-only update must not clear the name)
        $data = ['profile_image' => $profileImage];
        foreach (['name', 'last_name'] as $field) {
            if ($request->exists($field)) {
                $data[$field] = $request->input($field);
            }
        }

        $user->update($data);

        activity_log(
            log: 'API Profile',
            event: 'profile_updated',
            subject: $user,
            properties: [
                'old' => $oldData,
                'new' => [
                    'name'      => $user->name,
                    'last_name'      => $user->last_name,
                    'mobile'    => $user->mobile,
                    'profile_image' => $profileImage,
                ]
            ],
        );

        // $token = UserFcmToken()->where('user_uuid', $user->uuid)->pluck('fcm_token')->toArray();
        // $title = "Test Title";
        // $body = "Lorem Epsum Title Lorem Epsum Title Lorem Epsum Title Lorem Epsum Title Lorem Epsum Title Lorem Epsum Title";
        // $endpoint = "endpoint api param";
        // $data = [
        //     'type' => 'respond-join-request',
        //     'api_endpoint' => $endpoint
        // ];
        // $notification = sendNotification($token, $title, $body,$data);
        return jsonResponse(true, __('api.profile_updated'));
    }


    public function updateSettings(Request $request)
    {
        // JSON booleans fail the `in:` rule (false is not "false"), so map them to "1" / "0" first
        foreach (['email_notification', 'sms_notification', 'whatsapp_notification', 'push_notification'] as $key) {
            if (is_bool($request->input($key))) {
                $request->merge([$key => $request->input($key) ? '1' : '0']);
            }
        }

        $validator = Validator::make($request->all(), [
            'email_notification'    => 'nullable|in:true,false,1,0',
            'sms_notification'      => 'nullable|in:true,false,1,0',
            'whatsapp_notification' => 'nullable|in:true,false,1,0',
            'push_notification'     => 'nullable|in:true,false,1,0',
            'language'              => 'nullable|in:en,it',
            'weekly_checkin_day'    => 'nullable|in:' . implode(',', \App\Support\Journey::WEEK_DAYS),
            'timezone'              => 'nullable|timezone',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();

        $oldData = [
            'email_notification'    => $user->email_notification,
            'sms_notification'      => $user->sms_notification,
            'whatsapp_notification' => $user->whatsapp_notification,
            'language'              => $user->language,
        ];

       $data = [
            'email_notification' => $request->has('email_notification')
                ? filter_var($request->email_notification, FILTER_VALIDATE_BOOLEAN)
                : $user->email_notification,

            'sms_notification' => $request->has('sms_notification')
                ? filter_var($request->sms_notification, FILTER_VALIDATE_BOOLEAN)
                : $user->sms_notification,

            'whatsapp_notification' => $request->has('whatsapp_notification')
                ? filter_var($request->whatsapp_notification, FILTER_VALIDATE_BOOLEAN)
                : $user->whatsapp_notification,

            'push_notification' => $request->has('push_notification')
                ? filter_var($request->push_notification, FILTER_VALIDATE_BOOLEAN)
                : $user->push_notification,

            'language' => $request->language ?? $user->language,
            'weekly_checkin_day' => $request->weekly_checkin_day ?? $user->weekly_checkin_day,
            'timezone' => $request->timezone ?? $user->timezone,
        ];

        $user->update($data);

        activity_log(
            log: 'API Settings',
            event: 'settings_updated',
            subject: $user,
            properties: [
                'old' => $oldData,
                'new' => [
                    'email_notification'    => $user->email_notification,
                    'sms_notification'      => $user->sms_notification,
                    'whatsapp_notification' => $user->whatsapp_notification,
                    'language'              => $user->language,
                ]
            ],
        );

        return jsonResponse(true, __('api.settings_updated'), [
            'settings' => [
                'email_notification'    => $user->email_notification,
                'sms_notification'      => $user->sms_notification,
                'whatsapp_notification' => $user->whatsapp_notification,
                'push_notification'     => (bool) $user->push_notification,
                'language'              => $user->language,
                'weekly_checkin_day'    => $user->weekly_checkin_day,
                'timezone'              => $user->timezone,
            ]
        ]);
    }
    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        $user->status = 'Inactive';
        $user->save();

        activity_log(
            log: 'API Profile',
            event: 'account_deactivated',
            subject: $user,
            explicitCauser: $user
        );
        $request->user()->token()->revoke();
        $user->fcmTokens()->delete();

        return jsonResponse(true, __('api.account_deleted'));
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        if(!$user){
            return jsonResponse(false,__('api.unauthorized'),[],401);
        }
        if($user->is_social == 1){
            return jsonResponse(false,__('api.password_change_not_allowed_for_social_login'));
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return jsonResponse(false, __('api.current_password_incorrect'));
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        activity_log(
            log: 'API Change Password',
            event: 'password_changed',
            subject: $user,
            properties: [
                'message' => 'User changed password successfully',
            ],
        );

        return jsonResponse(true, __('api.password_changed_successfully'));
    }

    /**
     * Change mobile number, step 1: send an OTP to the new number (stored as pending until verified).
     * Accepts phone_code (country_code is still accepted for older app builds).
     */
    public function sendMobileOtp(Request $request)
    {
        if (!$request->filled('phone_code') && $request->filled('country_code')) {
            $request->merge(['phone_code' => $request->country_code]);
        }

        $validator = Validator::make($request->all(), [
            'mobile'     => 'required|digits_between:6,15',
            'phone_code' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $mobile = (string) $request->mobile;
        $phoneCode = $request->phone_code;

        if ($this->mobileTaken($user, $mobile, $phoneCode)) {
            return jsonResponse(false, __('api.mobile_already_exists'));
        }

        // Master test numbers get the static OTP and no SMS (same as login / signup)
        $isMaster = in_array($mobile, AuthController::MASTER_MOBILES, true);
        $otp = $isMaster ? AuthController::MASTER_OTP : (string) random_int(100000, 999999);
        $expiresIn = 120;

        $user->update([
            'mobile_otp'            => $otp,
            'mobile_otp_expires_at' => now()->addSeconds($expiresIn),
            'pending_mobile'        => $mobile,
            'pending_phone_code'    => $phoneCode,
        ]);

        if (!$isMaster) {
            try {
                sendOtp($phoneCode . $mobile, $otp);
            } catch (\Throwable $e) {
                \Log::error('Change mobile SMS OTP failed: ' . $e->getMessage());
            }
        }

        activity_log(
            log: 'API Profile',
            event: 'mobile_change_otp_sent',
            subject: $user,
            properties: ['pending_mobile' => $mobile, 'pending_phone_code' => $phoneCode],
        );

        return jsonResponse(true, __('api.mobile_otp_sent_successfully'), [
            'otp'        => $otp, // remove in production
            'otp_timer'  => $expiresIn,
            'expires_in' => $expiresIn,
            'phone_code' => $phoneCode,
            'mobile'     => $mobile,
        ]);
    }

    /**
     * Change mobile number, step 2: verify the OTP and switch the account to the new number.
     */
    public function verifyMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();

        if (!$user->pending_mobile || !$user->mobile_otp || !hash_equals((string) $user->mobile_otp, (string) $request->otp)) {
            return jsonResponse(false, __('api.invalid_otp'));
        }

        if (!$user->mobile_otp_expires_at || now()->gt($user->mobile_otp_expires_at)) {
            return jsonResponse(false, __('api.otp_expired'));
        }

        // The number may have been registered by someone else since the OTP was sent
        if ($this->mobileTaken($user, $user->pending_mobile, $user->pending_phone_code)) {
            return jsonResponse(false, __('api.mobile_already_exists'));
        }

        $old = ['phone_code' => $user->phone_code, 'mobile' => $user->mobile];

        $user->update([
            'phone_code'            => $user->pending_phone_code,
            'mobile'                => $user->pending_mobile,
            'is_mobile_verified'    => true,
            'mobile_otp'            => null,
            'mobile_otp_expires_at' => null,
            'pending_mobile'        => null,
            'pending_phone_code'    => null,
        ]);

        activity_log(
            log: 'API Profile',
            event: 'mobile_changed',
            subject: $user,
            properties: ['old' => $old, 'new' => ['phone_code' => $user->phone_code, 'mobile' => $user->mobile]],
        );

        return jsonResponse(true, __('api.mobile_otp_verified_successfully'), [
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Same lookup as login: a customer account is identified by phone_code + mobile.
     */
    protected function mobileTaken($user, string $mobile, ?string $phoneCode): bool
    {
        return User::where('user_type', 'customer')
            ->where('mobile', $mobile)
            ->where('phone_code', $phoneCode)
            ->where('id', '!=', $user->id)
            ->exists();
    }
    
}