<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Preference extends Model
{
    use HasFactory, HasUuid;
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'image',
        'type',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
