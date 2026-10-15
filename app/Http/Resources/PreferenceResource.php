<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        $isSelected = false;
        if ($user) {
            $isSelected = UserPreference()->where('user_uuid', $user->uuid)->where('preference_uuid', $this->uuid)->exists();
        }
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'image' => getFileUrl($this->image),
            'is_selected' => $isSelected,
        ];
    }
}