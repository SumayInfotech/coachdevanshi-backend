<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'note_date'  => $this->note_date->toDateString(),
            'note'       => $this->note,
            'photos'     => filesPayload($this->photos),
            'updated_at' => dateFormat($this->updated_at),
        ];
    }
}
