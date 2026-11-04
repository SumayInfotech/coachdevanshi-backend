<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class UserPlan extends Model
{
    use HasUuid;

    protected $table = 'user_plans';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_months' => 'integer',
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

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getDaysLeftAttribute(): int
    {
        return max(0, (int) now()->startOfDay()->diffInDays($this->end_date->copy()->startOfDay(), false));
    }

    public function getTotalDaysAttribute(): int
    {
        return max(1, (int) $this->start_date->diffInDays($this->end_date));
    }

    public function getProgressPercentAttribute(): int
    {
        $elapsed = (int) $this->start_date->diffInDays(now()->startOfDay(), false);
        return (int) min(100, max(0, round($elapsed / $this->total_days * 100)));
    }
}
