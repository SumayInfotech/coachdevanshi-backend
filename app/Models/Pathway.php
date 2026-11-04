<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Pathway extends Model
{
    use HasUuid;

    protected $table = 'pathways';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
