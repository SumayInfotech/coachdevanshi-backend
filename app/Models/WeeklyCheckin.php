<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class WeeklyCheckin extends Model
{
    use HasUuid;

    protected $table = 'weekly_checkins';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'last_period_start' => 'date',
            'is_baseline' => 'boolean',
            'travelled' => 'boolean',
            'extra' => 'array',
            'photos' => 'array',
            'weight' => 'float',
            'chest' => 'float',
            'abdomen' => 'float',
            'waist' => 'float',
            'hip' => 'float',
            'thigh' => 'float',
            'arm' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
