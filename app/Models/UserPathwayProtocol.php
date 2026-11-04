<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class UserPathwayProtocol extends Model
{
    use HasUuid;

    protected $table = 'user_pathway_protocols';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function pathway()
    {
        return $this->belongsTo(Pathway::class, 'pathway_uuid', 'uuid');
    }
}
