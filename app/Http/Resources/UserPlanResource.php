<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $threshold = (int) setting('renewal_reminder_days', 14);

        return [
            'uuid'             => $this->uuid,
            'title'            => $this->title,
            'pathway'          => $this->pathway ? ['uuid' => $this->pathway->uuid, 'title' => $this->pathway->title] : null,
            'duration_months'  => $this->duration_months,
            'plan_type'        => $this->duration_months ? $this->duration_months . 'M' : null,
            'start_date'       => $this->start_date->toDateString(),
            'end_date'         => $this->end_date->toDateString(),
            'end_date_label'   => $this->end_date->format('d M Y'),
            'days_left'        => $this->days_left,
            'total_days'       => $this->total_days,
            'progress_percent' => $this->progress_percent,
            'status'           => $this->status,
            'show_renewal_cta' => $this->status === 'Active' && $this->days_left <= $threshold,
        ];
    }
}
