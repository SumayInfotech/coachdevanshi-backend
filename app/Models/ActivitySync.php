<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitySync extends Model
{
    protected $table = 'activity_syncs';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'steps' => 'integer',
            'distance_km' => 'float',
            'calories' => 'integer',
            'active_minutes' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
