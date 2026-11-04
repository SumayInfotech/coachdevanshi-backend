<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'        => $this->uuid,
            'sender_type' => $this->sender_type,
            'sender_name' => $this->sender_type === 'system' ? 'System' : $this->sender?->name,
            'is_mine'     => $this->sender_type === 'client',
            'message'     => $this->message,
            'attachments' => filesPayload($this->attachments),
            'is_read'     => !is_null($this->read_at),
            'created_at'  => $this->created_at->toIso8601String(),
        ];
    }
}
