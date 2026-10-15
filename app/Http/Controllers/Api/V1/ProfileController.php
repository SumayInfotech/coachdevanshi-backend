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
        $states = State::select('id','name')->where('country_id',103)->orderBy('name','asc')->get();        
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
            'mobile'         => 'nullable|numeric|unique:users,mobile,'.auth()->id(),
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
        $data = [
            'name'      => $request->name,
            'last_name'      => $request->last_name,
            'mobile'    => $request->mobile,
            'profile_image' => $profileImage,
        ];
        if ($user->is_mobile_verified == 1) {
            $data['mobile'] = $user->pending_mobile;
            $data['country_code'] = $user->pending_country_code;
            $data['is_mobile_verified'] = 0;
        }

        $user->update($data);

        $user->pending_mobile = null;
        $user->pending_country_code = null;
        $user->save();

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
        $validator = Validator::make($request->all(), [
            'email_notification'    => 'nullable|in:true,false,1,0',
            'sms_notification'      => 'nullable|in:true,false,1,0',
            'whatsapp_notification' => 'nullable|in:true,false,1,0',
            'language'              => 'nullable|in:en,it',
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

            'language' => $request->language ?? $user->language,
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
                'language'              => $user->language,
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

    public function sendMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric',
            'country_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        // if($user->is_social == 1){
        //      return jsonResponse(false, __('api.social_user_not_eligible_for_mobile_otp'));
        // }

        $mobileExists = User::where('mobile', $request->mobile)
            ->where('country_code', $request->country_code)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($mobileExists) {
            return jsonResponse(false, __('api.mobile_already_exists'));
        }

        $otp = rand(1000, 9999);

        $user->update([
            'mobile_otp' => $otp,
            'mobile_otp_expires_at' => now()->addSeconds(60),
            'pending_mobile' => $request->mobile,
            'pending_country_code' => $request->country_code,
            'is_mobile_verified' => 0,
        ]);

        // Send OTP here
        // sendOtp($request->country_code.$request->mobile, $otp);

        return jsonResponse(true, __('api.mobile_otp_sent_successfully'), ['otp' => $otp,'otp_timer' => 60]);
    }

    public function verifyMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();

        if (!$user->mobile_otp || $user->mobile_otp != $request->otp) {
            return jsonResponse(false, __('api.invalid_otp'));
        }

        if (now()->gt($user->mobile_otp_expires_at)) {
            return jsonResponse(false, __('api.otp_expired'));
        }

        $user->update([
            'is_mobile_verified' => 1,
            'mobile_otp' => null,
            'mobile_otp_expires_at' => null,
        ]);

        return jsonResponse(true, __('api.mobile_otp_verified_successfully'));
    }
    
}