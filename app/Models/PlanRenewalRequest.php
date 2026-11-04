<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class PlanRenewalRequest extends Model
{
    use HasUuid;

    protected $table = 'plan_renewal_requests';

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

    public function plan()
    {
        return $this->belongsTo(UserPlan::class, 'user_plan_uuid', 'uuid');
    }
}
