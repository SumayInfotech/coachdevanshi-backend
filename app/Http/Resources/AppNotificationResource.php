<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'type'       => $this->type,
            'title'      => $this->title,
            'body'       => $this->body,
            'badge'      => $this->badge,
            'action'     => $this->action,
            'data'       => $this->data ?? (object) [],
            'is_read'    => !is_null($this->read_at),
            'read_at'    => $this->read_at ? dateFormat($this->read_at) : null,
            'created_at' => dateFormat($this->created_at),
        ];
    }
}
