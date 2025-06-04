<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            "id"=> $this->id,
            'text'=> $this->message,
            'time'=> $this->created_at,
            // 'sent'=> $this->sender_id === auth()->id(),
            'sent'=> $this->sender_id,
            'is_read'=> $this->is_read,
        ];
    }
}
