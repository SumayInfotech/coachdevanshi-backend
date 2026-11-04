<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class BloodReportFile extends Model
{
    use HasUuid;

    protected $table = 'blood_report_files';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }
}
