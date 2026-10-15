<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
