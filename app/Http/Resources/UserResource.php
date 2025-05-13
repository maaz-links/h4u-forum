<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // 'password' => $this->password,
            'phone' => $this->phone,
            // 'newsletter' => $this->newsletter,
            'role' => $this->role,
            'dob' => $this->dob,
            'profile_picture_id' => $this->profile_picture_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'rating' => $this->getRatingAttribute(),
            'profile' => new UserProfileResource($this->whenLoaded('profile')),
        ];
    }
}
