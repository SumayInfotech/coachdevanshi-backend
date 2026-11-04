<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodDiaryEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'          => $this->uuid,
            'day_number'    => $this->day_number,
            'day_type'      => $this->day_type,
            'record_date'   => $this->record_date->toDateString(),
            'meals'         => $this->meals ?? (object) [],
            'sensations'    => $this->sensations ?? [],
            'context_notes' => $this->context_notes,
            'updated_at'    => dateFormat($this->updated_at),
        ];
    }
}
