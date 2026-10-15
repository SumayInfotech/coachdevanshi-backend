<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    protected $table = 'activity_log';

    protected $casts = [
        'properties' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Causer (Admin/User)
    public function causerUser()
    {
        return $this->belongsTo(User::class, 'causer_uuid', 'uuid');
    }

    // Subject model (Generic via UUID only)
    public function subjectModel()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getEventLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->event));
    }
}
