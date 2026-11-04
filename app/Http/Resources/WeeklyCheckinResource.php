<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyCheckinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'              => $this->uuid,
            'record_date'       => $this->record_date->toDateString(),
            'is_baseline'       => $this->is_baseline,
            'weight'            => $this->weight,
            'weight_unit'       => $this->weight_unit,
            'measurements'      => [
                'chest'   => $this->chest,
                'abdomen' => $this->abdomen,
                'waist'   => $this->waist,
                'hip'     => $this->hip,
                'thigh'   => $this->thigh,
                'arm'     => $this->arm,
                'unit'    => $this->measurement_unit,
            ],
            'self_confidence'   => $this->self_confidence,
            'last_period_start' => $this->last_period_start?->toDateString(),
            'cycle_day'         => $this->cycle_day,
            'cycle_notes'       => $this->cycle_notes,
            'cravings_level'    => $this->cravings_level,
            'cravings_notes'    => $this->cravings_notes,
            'travelled'         => $this->travelled,
            'travel_notes'      => $this->travel_notes,
            'wins'              => $this->wins,
            'proud_moments'     => $this->proud_moments,
            'struggles'         => $this->struggles,
            'notes'             => $this->notes,
            'extra'             => $this->extra ?? (object) [],
            'photos'            => filesPayload($this->photos),
            'created_at'        => dateFormat($this->created_at),
            'updated_at'        => dateFormat($this->updated_at),
        ];
    }
}
