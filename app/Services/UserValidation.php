<?php

namespace App\Services;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserValidation
{
    /**
     * Returns validation rules for selected fields
     * @param array $include Only these fields will be validated
     * @return array
     */
    public static function rules(array $include = []): array
    {
        $allRules = [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => ['string','nullable','max:255',Rule::in(['ADMIN', 'HOSTESS', 'CUSTOMER'])],
            'phone' => 'required|string|phone:AUTO',
            'dob' => [
                'required',
                'date',
                'after:1900-01-01',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            ],
            'newsletter' => 'boolean'
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
            'password.required' => 'Password is required',
            'password.confirmed' => 'Passwords do not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.mixed' => 'Password must contain both uppercase and lowercase letters',
            'password.numbers' => 'Password must contain at least one number',
            'password.symbols' => 'Password must contain at least one special character',
            'dob.before_or_equal' => 'You must be at least 18 years old',
            'phone.required' => 'Phone number is required',
            'phone.phone' => 'Please enter a valid phone number',
        ];
    }
}