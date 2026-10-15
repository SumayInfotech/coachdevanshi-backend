<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class UserPreference extends Model
{
    use HasFactory, HasUuid;
    protected $fillable = [
        'uuid',
        'user_uuid',
        'preference_uuid',
    ];
}


