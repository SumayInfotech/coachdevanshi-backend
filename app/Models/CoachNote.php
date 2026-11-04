<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class CoachNote extends Model
{
    use HasUuid;

    protected $table = 'coach_notes';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_uuid', 'uuid');
    }
}
