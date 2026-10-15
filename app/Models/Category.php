<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Category extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'name',
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