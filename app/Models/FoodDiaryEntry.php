<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class FoodDiaryEntry extends Model
{
    use HasUuid;

    protected $table = 'food_diary_entries';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'meals' => 'array',
            'sensations' => 'array',
            'day_number' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
