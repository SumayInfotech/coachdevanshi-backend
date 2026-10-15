<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ExcludeSuperAdmin
{
    /**
     * Boot the trait and apply the global scope to the model.
     *
     * @return void
     */
    protected static function bootExcludeSuperAdmin()
    {
        static::addGlobalScope('exclude_super_admin', function (Builder $builder) {
            // Exclude the 'super_admin' role from all queries
            $builder->where('name', '!=', 'super_admin');
        });
    }
    
}
