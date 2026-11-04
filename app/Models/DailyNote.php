<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class DailyNote extends Model
{
    use HasUuid;

    protected $table = 'daily_notes';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'note_date' => 'date',
            'photos' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
