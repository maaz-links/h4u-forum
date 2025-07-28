<?php

namespace App\Services;

use App\Models\User;
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
                
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                    function ($attribute, $value, $fail) {
                        if ($value !== trim($value)) {
                            $fail('Password cannot start or end with spaces.');
                        }
                    },
                'confirmed',
            ],
            'role' => ['string','nullable','max:255',Rule::in([User::ROLE_HOSTESS, User::ROLE_KING])],
            'phone' => 'required|string|phone:AUTO',
            'dob' => [
                'required',
                'date',
                'after:1900-01-01',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            ],
            'newsletter' => 'boolean',
            'isModel' => 'boolean',
            'profileTypes' => 'nullable|array',
            'profileTypes.*' => 'integer|exists:profile_types,id'
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
        // return [
        //     'name.required' => 'Name is required',
        //     'name.string' => 'Name must be a valid string',
        //     'name.max' => 'Name cannot be longer than 255 characters',
        //     'name.unique' => 'This name is already taken',

        //     'email.required' => 'Email is required',
        //     'email.string' => 'Email must be a valid string',
        //     'email.email' => 'Please enter a valid email address',
        //     'email.max' => 'Email cannot be longer than 255 characters',
        //     'email.unique' => 'This email is already registered',

        //     'password.required' => 'Password is required',
        //     'password.confirmed' => 'Passwords do not match',
        //     'password.min' => 'Password must be at least 8 characters',
        //     'password.letters' => 'Password must contain at least one letter',
        //     'password.mixed' => 'Password must contain both uppercase and lowercase letters',
        //     'password.numbers' => 'Password must contain at least one number',
        //     'password.symbols' => 'Password must contain at least one special character',

        //     'role.string' => 'Role must be a string',
        //     'role.max' => 'Role cannot be longer than 255 characters',
        //     'role.in' => 'Selected role is invalid',

        //     'phone.required' => 'Phone number is required',
        //     'phone.string' => 'Phone number must be a valid string',
        //     'phone.phone' => 'Please enter a valid phone number',

        //     'dob.required' => 'Date of Birth is required',
        //     'dob.date' => 'Please enter a valid date',
        //     'dob.after' => 'Date of Birth must be after January 1, 1900',
        //     'dob.before_or_equal' => 'You must be at least 18 years old',

        //     'newsletter.boolean' => 'Newsletter field must be true or false',
        //     'isModel.boolean' => 'Model field must be true or false',

        //     'profileTypes.array' => 'Profile types must be an array',
        //     'profileTypes.*.integer' => 'Each profile type must be a valid number',
        //     'profileTypes.*.exists' => 'Selected profile type does not exist',
        // ];
        return [
            'name.required' => 'Il nome è obbligatorio',
            'name.string' => 'Il nome deve essere una stringa valida',
            'name.max' => 'Il nome non può superare i 255 caratteri',
            'name.unique' => 'Questo nome è già stato utilizzato',
        
            'email.required' => 'L\'email è obbligatoria',
            'email.string' => 'L\'email deve essere una stringa valida',
            'email.email' => 'Inserisci un indirizzo email valido',
            'email.max' => 'L\'email non può superare i 255 caratteri',
            'email.unique' => 'Questa email è già registrata',
        
            'password.required' => 'La password è obbligatoria',
            'password.confirmed' => 'Le password non corrispondono',
            'password.min' => 'La password deve contenere almeno 8 caratteri',
            'password.letters' => 'La password deve contenere almeno una lettera',
            'password.mixed' => 'La password deve contenere lettere maiuscole e minuscole',
            'password.numbers' => 'La password deve contenere almeno un numero',
            'password.symbols' => 'La password deve contenere almeno un carattere speciale',
        
            'role.string' => 'Il ruolo deve essere una stringa',
            'role.max' => 'Il ruolo non può superare i 255 caratteri',
            'role.in' => 'Il ruolo selezionato non è valido',
        
            'phone.required' => 'Il numero di telefono è obbligatorio',
            'phone.string' => 'Il numero di telefono deve essere una stringa valida',
            'phone.phone' => 'Inserisci un numero di telefono valido',
        
            'dob.required' => 'La data di nascita è obbligatoria',
            'dob.date' => 'Inserisci una data valida',
            'dob.after' => 'La data di nascita deve essere successiva al 1º gennaio 1900',
            'dob.before_or_equal' => 'Devi avere almeno 18 anni',
        
            'newsletter.boolean' => 'Il campo newsletter deve essere vero o falso',
            'isModel.boolean' => 'Il campo modello deve essere vero o falso',
        
            'profileTypes.array' => 'I tipi di profilo devono essere un array',
            'profileTypes.*.integer' => 'Ogni tipo di profilo deve essere un numero valido',
            'profileTypes.*.exists' => 'Il tipo di profilo selezionato non esiste',
        ];
    }
}