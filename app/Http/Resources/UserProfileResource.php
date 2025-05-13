<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $this */
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'gender' => $this->gender,
            'description' => $this->description,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'telegram' => $this->telegram,
            'tiktok' => $this->tiktok,
            'onlyfans' => $this->onlyfans,
            'personal_website' => $this->personal_website,
            'height' => $this->height,
            'shoe_size' => $this->shoe_size,
            'eye_color' => $this->eye_color,
            'dress_size' => $this->dress_size,
            'weight' => $this->weight,
            'is_user_model' => $this->is_user_model,
            'top_profile' => $this->top_profile,
            'verified_female' => $this->verified_female,
            'verified_profile' => $this->verified_profile,
            'visibility_status' => $this->visibility_status,
            'notification_pref' => $this->notification_pref,
            'travel_available' => $this->travel_available,
            'credits' => $this->credits,
            'nationality' => $this->nationality,
            'country_id' => $this->country_id,
            'province_id' => $this->province_id,
            'available_services' => $this->getAvailableServicesAttribute(),
            'personal_interests' => $this->getPersonalInterestsAttribute(),
            'my_languages' => $this->getMyLanguagesAttribute(),
        ];
    }
}
