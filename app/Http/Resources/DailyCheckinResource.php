<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyCheckinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'                => $this->uuid,
            'record_date'         => $this->record_date->toDateString(),
            'baseline_day'        => $this->baseline_day,
            'weight'              => $this->weight,
            'weight_unit'         => $this->weight_unit,
            'wake_time'           => $this->wake_time,
            'bed_time'            => $this->bed_time,
            'sleep_hours'         => $this->sleep_hours,
            'sleep_quality'       => $this->sleep_quality,
            'steps'               => $this->steps,
            'water_intake'        => $this->water_intake,
            'water_unit'          => $this->water_unit,
            'water_goal_met'      => $this->water_goal_met,
            'mood'                => $this->mood,
            'energy'              => $this->energy,
            'nutrition_adherence' => $this->nutrition_adherence,
            'movement_done'       => $this->movement_done,
            'movement_type'       => $this->movement_type,
            'meditation_done'     => $this->meditation_done,
            'meditation_notes'    => $this->meditation_notes,
            'journaling_done'     => $this->journaling_done,
            'journaling_notes'    => $this->journaling_notes,
            'meals'               => $this->meals ?? (object) [],
            'notes'               => $this->notes,
            'photos'              => filesPayload($this->photos),
            'created_at'          => dateFormat($this->created_at),
            'updated_at'          => dateFormat($this->updated_at),
        ];
    }
}
