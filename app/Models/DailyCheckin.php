<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class DailyCheckin extends Model
{
    use HasUuid;

    protected $table = 'daily_checkins';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'meals' => 'array',
            'photos' => 'array',
            'weight' => 'float',
            'sleep_hours' => 'float',
            'water_intake' => 'float',
            'steps' => 'integer',
            'water_goal_met' => 'boolean',
            'movement_done' => 'boolean',
            'meditation_done' => 'boolean',
            'journaling_done' => 'boolean',
            'mood' => 'integer',
            'energy' => 'integer',
            'baseline_day' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
