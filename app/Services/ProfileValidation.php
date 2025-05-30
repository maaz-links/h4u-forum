<?php

namespace App\Services;

use Illuminate\Validation\Rule;

class ProfileValidation
{
    public const EYE_COLORS = ['blue', 'green', 'brown', 'hazel', 'gray'];
    /**
     * Returns validation rules for selected fields
     * @param array $include Only these fields will be validated
     * @return array
     */
    public static function rules(array $include = []): array
    {
        $allRules = [
            'description' => 'required|string|min:1|max:1000', // Added min/max length
            'travel_available' => 'required|integer|in:0,1', // Restrict to specific values
            'notification_pref' => 'required|integer|in:0,1', // Assuming 2 notification types
            'visibility_status' => 'required|integer|in:0,1,2', // Assuming 3 visibility states
        
            'option_ids' => 'nullable|array',
            'option_ids.*' => 'integer|exists:interests,id|distinct',
            'option_available_for_ids' => 'nullable|array',
            'option_available_for_ids.*' => 'integer|exists:hostess_services,id|distinct',
            'option_language_ids' => 'nullable|array',
            'option_language_ids.*' => 'integer|exists:spoken_languages,id|distinct',

            'other_data' => 'array|required',
            'other_data.shoeSize' => 'required|numeric|between:0,100',
            'other_data.height' => 'required|numeric|between:0,400',
            'other_data.weight' => 'required|numeric|between:0,400',
            'other_data.eyeColor' => 'required|string|in:brown,blue,green,hazel,gray',
            'other_data.telegram' => 'nullable|string|max:32',
            'other_data.dressSize' => 'required|string|in:S,M,L',

            'nationality' => 'required|string|max:50',
          
            'selectedCountry' => 'integer|required|exists:europe_countries,id',
            'selectedProvince' => 'integer|required|exists:europe_provinces,id'
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
            'description.required' => 'Description is required',
            'travel_available.required' => 'Travel availability is required',
            'notification_pref.required' => 'Notification preference is required',
            'visibility_status.required' => 'Visibility status is required',
            
            'option_ids.*.exists' => 'Selected interest is invalid',
            'option_available_for_ids.*.exists' => 'Selected service is invalid',
            'option_language_ids.*.exists' => 'Selected language is invalid',
            
            'other_data.required' => 'Additional information is required',
            'other_data.shoeSize.required' => 'Shoe size is required',
            'other_data.height.required' => 'Height is required',
            'other_data.weight.required' => 'Weight is required',
            'other_data.eyeColor.required' => 'Eye color is required',
            'other_data.dressSize.required' => 'Dress size is required',
            
            'nationality.required' => 'Nationality is required',
            
            'selectedCountry.required' => 'Country is required',
            'selectedCountry.exists' => 'Selected country is invalid',
            'selectedProvince.required' => 'Province is required',
            'selectedProvince.exists' => 'Selected province is invalid',
        ];
    }
}