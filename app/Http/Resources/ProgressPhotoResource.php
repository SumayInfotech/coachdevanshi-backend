<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressPhotoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'         => $this->uuid,
            'record_date'  => $this->record_date->toDateString(),
            'front_photo'  => $this->front_photo ? ['path' => $this->front_photo, 'url' => getFileUrl($this->front_photo)] : null,
            'side_photo'   => $this->side_photo ? ['path' => $this->side_photo, 'url' => getFileUrl($this->side_photo)] : null,
            'back_photo'   => $this->back_photo ? ['path' => $this->back_photo, 'url' => getFileUrl($this->back_photo)] : null,
            'extra_photos' => filesPayload($this->extra_photos),
            'consent'      => $this->consent,
            'created_at'   => dateFormat($this->created_at),
        ];
    }
}
