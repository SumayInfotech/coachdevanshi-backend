<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'        => $this->uuid,
            'report_date' => $this->report_date->toDateString(),
            'notes'       => $this->notes,
            'files'       => $this->files->map(fn ($file) => [
                'uuid'          => $file->uuid,
                'url'           => getFileUrl($file->file_path),
                'original_name' => $file->original_name,
                'mime_type'     => $file->mime_type,
                'size'          => $file->size,
            ])->values(),
            'created_at'  => dateFormat($this->created_at),
        ];
    }
}
