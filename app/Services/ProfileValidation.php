<?php

namespace App\Services;

use Illuminate\Validation\Rule;

class ProfileValidation
{
    public const EYE_COLORS = ['Blue', 'Green', 'Brown', 'Hazel', 'Gray'];
    /**
     * Returns validation rules for selected fields
     * @param array $include Only these fields will be validated
     * @return array
     */
    public static function rules(array $include = []): array
    {
        $allRules = [
            'description' => 'nullable|string|min:1|max:1000', // Added min/max length
            'travel_available' => 'nullable|integer|in:0,1', // Restrict to specific values
            'notification_pref' => 'required|integer|in:0,1', // Assuming 2 notification types
            'visibility_status' => 'required|integer|in:0,1,2', // Assuming 3 visibility states
        
            'option_profile_types' => 'nullable|array',
            'option_profile_types.*' => 'integer|exists:profile_types,id|distinct',
            'option_ids' => 'nullable|array',
            'option_ids.*' => 'integer|exists:interests,id|distinct',
            'option_available_for_ids' => 'nullable|array',
            'option_available_for_ids.*' => 'integer|exists:hostess_services,id|distinct',
            'option_language_ids' => 'nullable|array',
            'option_language_ids.*' => 'integer|exists:spoken_languages,id|distinct',

            // 'other_data' => 'array|required',
            'shoeSize' => 'nullable|numeric|between:0,100',
            'height' => 'nullable|numeric|between:0,400',
            'weight' => 'nullable|numeric|between:0,400',
            'eyeColor' => 'nullable|string|max:50',
            
            'telegram' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'facebook' => 'nullable|string|max:50',
            'instagram' => 'nullable|string|max:50',
            'onlyfans' => 'nullable|string|max:50',
            'tiktok' => 'nullable|string|max:50',
            'personal_website' => 'nullable|string|max:100',
            //'dressSize' => 'nullable|string|in:S,M,L',
            //numveric validation but dress_size is stored as string in DB
            'dressSize' => 'nullable|numeric|between:0,50',

            'nationality' => 'nullable|string|max:50',
          
            'selectedCountry' => 'nullable|integer|exists:europe_countries,id',
            'selectedProvince' => 'nullable|integer|exists:europe_provinces,id'
        ];

        // If no specific fields requested, return all rules
        if (empty($include)) {
            return $allRules;
        }
        
        // Return only requested rules
        return array_intersect_key($allRules, array_flip($include));
    }

    /**
     * Custom validation messages
     */
    public static function messages(): array
    {
        return [
            
        ];
    }
}