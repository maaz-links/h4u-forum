<?php

namespace App\Http\Controllers\ApiAuth;
use App\Events\SendOTP;
use App\Events\SendSMS;
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
        $validator = Validator::make(
            $request->all(),
            UserValidation::rules(['name', 'email', 'password', 'dob','role','phone','newsletter','isModel']),
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
        //Set up Role
        switch ($request->role) {
            case 'KING':
                $user->role = User::ROLE_KING;
                break;
            case 'HOSTESS':
                $user->role = User::ROLE_HOSTESS;
                break;
            default:
                $user->role = User::ROLE_KING;
                break;
        }
        //return response()->json($user);
        $user->save();

        $firstCountryId = EuropeCountry::orderBy('display_order')->value('id') ?? null;
        $firstProvinceId = EuropeProvince::where('country_id', $firstCountryId)
        ->orderBy('display_order')
        ->value('id') ?? null;

        $initialCredits = [];
        if($user->role == User::ROLE_HOSTESS){
            $initialCredits = ['credits' => 5];
        }

        $profile = new UserProfile([
            'user_id' => $user->id,
            'nationality' => 'Italian',
            'description' => 'New User here',
            'country_id' => $firstCountryId,
            'province_id' => $firstProvinceId,
            'is_user_model' => $request->isModel ?? 0,
        ] + $initialCredits);

        $profile->save();

        event(new Registered($user));
        // $otp = $this->generateOTP($user);
        // return response()->json([
        //     'message' => $otp,
        //     'phone' => $user->phone,
        // ]);
        // $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully. Please check your email for verification.',
        //    'access_token' => $token,
        //     'token_type' => 'Bearer',
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
                'formError' => ['email' => ['Invalid login credentials']],
                'noreload' => true
            ], 422);
        }

        $user = User::where('email', $request->email)->forRoleAny()->first();
        if(!$user){
            return response()->json([
                'formError' => ['email' => ['Invalid login credentials']],
                'noreload' => true
            ], 422);
        }
        
        // Revoke all previous tokens (optional)
        $user->tokens()->delete();
        
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return response()->json([
                'message' => 'User registered successfully. Please check your email for verification.',
                'mustverify' => true,
            ], 200);
        }
        
        $ban = $user->activeBan();
        if ($ban) {
            return response()->json([
                'banned' => true,
                'username' => $user->name,
            ]);
        }
        $otp = $this->generateOTP($user);
        return response()->json([
                'message' => $otp,
                'phone' => $user->phone,
            ]);
        // Create new token
        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'Bearer',
        //     'user' => $user,
        //     'email_verified' => $user->hasVerifiedEmail(),
        // ]);
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

    public static function generateOTP(User $user){
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5); // OTP valid for 5 minutes
        
        event(new SendOTP($user, $otp));
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt
        ]);

        return $otp;
    }

    public function verifyOtp(Request $request)
    {
        // dd('ok');
        // $request->validate([
        //     'email' => 'required|email',
        //     'otp' => 'required|digits:6'
        // ]);
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'otp' => 'required|integer|digits:6',
                //'otp' => 'integer|digits:6',
            ]
        );
        // dd('ok');
        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }

        // dd('ok');
        // if(!$request->otp){
        //     $user = User::where('email', $request->email)
        //     ->where('otp_expires_at', '>', now())
        //     ->first();
        // }else{
            $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', now())
            ->first();
        // }
        
        
        if (!$user) {
            return response()->json([
                //'message' => 'Invalid login credentials',
                'formError' => ['otp' => ['OTP Invalid or Expired']],
                'noreload' => true
            ], 422);
        }
        // Clear OTP after successful verification
        $user->update([
            'otp' => null,
            'otp_expires_at' => null
        ]);
        
        // Generate and return authentication token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }

    public function resendOtp(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'phone' => 'required|string',
                //'otp' => 'integer|digits:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['formError' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)
            ->where('phone', $request->phone)
            ->where('otp', '!=',null)
            ->where('otp_expires_at', '>', now())
            ->first();

            if (!$user) {
                return response()->json([
                    //'message' => 'Invalid login credentials',
                    'formError' => ['otp' => ['OTP Invalid or Expired']],
                    'noreload' => true
                ], 422);
            }
            event(new SendOTP($user, $user->otp));

            return response()->json(['message' => "OTP is resent"], 200);
    }
}
