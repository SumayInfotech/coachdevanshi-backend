<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PathwayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = auth()->user();

        return [
            'uuid'         => $this->uuid,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'group'        => $this->group,
            'description'  => $this->description,
            'tags'         => $this->tags ?? [],
            'image'        => $this->image ? getFileUrl($this->image) : null,
            'has_protocol' => !empty($this->protocol_key) && !empty(config('protocols.' . $this->protocol_key)),
            'is_selected'  => $user ? $user->pathway_uuid === $this->uuid : false,
        ];
    }
}
