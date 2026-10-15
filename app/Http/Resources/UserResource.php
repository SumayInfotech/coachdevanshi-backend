<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'uuid'        => $this->uuid,
            'name'       => $this->name,
            'last_name'       => $this->last_name,
            'email'       => $this->email,
            'phone_code'  => $this->phone_code,
            'mobile'      => $this->mobile,
            'is_email_verified' => !is_null($this->email_verified_at),
            'email_verified_at' => $this->email_verified_at ? dateFormat($this->email_verified_at) : null,
            'profile_image' => $this->profile_image  ? getFileUrl($this->profile_image) :null,
            'is_profile_completed'   => (bool) $this->is_profile_completed,
            'sign_up_through' => $this->identifier_type,
            'sign_up_type' => $this->sign_up_type,
            'is_signup'   => (bool) $this->is_signup,
            'is_social'   => (bool) $this->is_social,
            // 'states' => [
                'state_id' => $this->state_id,
                'state_name' => $this->state?->name,
            // ],
            // 'city' => [
                'city_id' => $this->city_id,
                'city_name' => $this->city?->name,
            // ],
            'email_notification'   =>(bool) $this->email_notification,
            'sms_notification'      => (bool) $this->sms_notification,
            'whatsapp_notification' => (bool) $this->whatsapp_notification,
            'language'              => $this->language,
            'created_at' => dateFormat($this->created_at),
            'updated_at' => dateFormat($this->updated_at),
        ];
    }
}
