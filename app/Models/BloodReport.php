<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class BloodReport extends Model
{
    use HasUuid;

    protected $table = 'blood_reports';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function files()
    {
        return $this->hasMany(BloodReportFile::class, 'blood_report_uuid', 'uuid');
    }
}
