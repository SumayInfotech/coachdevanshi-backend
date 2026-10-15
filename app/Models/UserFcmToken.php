<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFcmToken extends Model
{
    use HasFactory, HasUuid;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_uuid',
        'fcm_token',
        'device_type',
        'device_name',
    ];

    
}
