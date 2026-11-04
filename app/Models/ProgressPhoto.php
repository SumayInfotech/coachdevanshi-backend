<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ProgressPhoto extends Model
{
    use HasUuid;

    protected $table = 'progress_photos';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'extra_photos' => 'array',
            'consent' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
