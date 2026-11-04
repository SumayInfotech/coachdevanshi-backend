<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class UserConsent extends Model
{
    use HasUuid;

    protected $table = 'user_consents';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'info_accurate' => 'boolean',
            'not_medical_advice' => 'boolean',
            'data_usage' => 'boolean',
            'communication' => 'boolean',
            'marketing' => 'boolean',
            'accepted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
