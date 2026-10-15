<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\HasUuid;
use App\Traits\ExcludeSuperAdmin;
use Illuminate\Database\Eloquent\Builder;


class Role extends SpatieRole
{
    use HasUuid,ExcludeSuperAdmin;  // Use the trait

    // protected $primaryKey = 'uuid';
    // public $incrementing = false;
    protected $fillable = ['uuid', 'name', 'guard_name', 'status'];

    // Define a local scope for active roles
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'Active');
    }

    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'role_uuid', 'uuid');
    }

}
