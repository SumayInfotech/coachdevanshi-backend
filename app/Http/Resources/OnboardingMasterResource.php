<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnboardingMasterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'           => $this->uuid,
            'title'          => $this->title,
            'description'    => $this->description,
            'image'          => $this->image ? getFileUrl($this->image) : null,
            // 'category'       => $this->category,
            // 'category_label' => $this->category_label,
            'sort_order'     => $this->sort_order,
        ];
    }
}
