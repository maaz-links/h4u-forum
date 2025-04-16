<?php

namespace App\Http\Controllers\ApiAuth;
use App\Http\Controllers\Controller;

use App\Models\EuropeCountry;
use App\Models\EuropeProvince;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\UserValidation;
use Auth;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Validator;
use Propaganistas\LaravelPhone\PhoneNumber;

class ApiAuthenticationController extends Controller
{
    public function register(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255|unique:users',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => [
        //         'required',
        //         'confirmed',
        //         Password::min(8)
        //             ->letters()
        //             ->mixedCase()
        //             ->numbers()
        //             ->symbols(),
        //     ],
        //     'role' => 'string|max:255',
        //     'phone' => [
        //         'required',
        //         'string',
        //         'phone:AUTO', // Validates international phone numbers
        //     ],
        //     'dob' => [
        //         'required',
        //         'date',
        //         'after:1900-01-01',
        //         'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
        //     ],
        // ], [
        //     'password.required' => 'Password is required',
        //     'password.confirmed' => 'Passwords do not match',
        //     'password.min' => 'Password must be at least 8 characters',
        //     'password.mixed' => 'Password must contain both uppercase and lowercase letters',
        //     'password.numbers' => 'Password must contain at least one number',
        //     'password.symbols' => 'Password must contain at least one special character',
        //     //
        //     'dob.before_or_equal' => 'You must be at least 18 years old',
        //     'phone.required' => 'Phone number is required',
        //     'phone.phone' => 'Please enter a valid phone number',
        // ]);
        $validator = Validator::make(
            $request->all(),
            UserValidation::rules(['name', 'email', 'password', 'dob','role','phone','newsletter']),
            UserValidation::messages()
        );

        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }
        
        $phoneNumber = new PhoneNumber($request->phone);
        $phoneNumber = $phoneNumber->formatE164();
        //dd($phoneNumber);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $phoneNumber,
            'dob' => $request->dob,
        ]);
        if ($request->role) {
            $user->role = $request->role;
        } else {
            $user->role = 'CUSTOMER'; // Default value
        }
        //return response()->json($user);
        $user->save();

        $firstCountryId = EuropeCountry::orderBy('display_order')->value('id') ?? null;
        $firstProvinceId = EuropeProvince::where('country_id', $firstCountryId)
        ->orderBy('display_order')
        ->value('id') ?? null;

        $profile = new UserProfile([
            'user_id' => $user->id,
            'nationality' => 'Italian',
            'Description' => 'New User here',
            'country_id' => $firstCountryId,
            'province_id' => $firstProvinceId,
        ]);

        $profile->save();

        event(new Registered($user));

        //$token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully. Please check your email for verification.',
          //  'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                //'message' => 'Invalid login credentials',
                'formError' => ['email' => ['Invalid login credentials']],
                'noreload' => true
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        // Revoke all previous tokens (optional)
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
