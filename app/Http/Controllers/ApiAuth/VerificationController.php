<?php

namespace App\Http\Controllers\ApiAuth;
use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:sanctum');
        //$this->middleware('signed')->only('verify');
        //$this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    // public function verify(Request $request)
    // {
    //     if ($request->route('id') != $request->user()->getKey()) {
    //         throw new AuthorizationException;
    //     }

    //     if ($request->user()->hasVerifiedEmail()) {
    //         return response()->json(['message' => 'Email already verified'], 422);
    //     }

    //     if ($request->user()->markEmailAsVerified()) {
    //         event(new Verified($request->user()));
    //     }
    public function verify(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        //dd($request->hash);
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verify the signed URL and hash
        if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        // if ($user->hasVerifiedEmail()) {
        //     return response()->json(['message' => 'Email already verified']);
        // }

        
        // if ($user->markEmailAsVerified()) {
        //     event(new Verified($user));
        // }

        //return response()->json(['message' => 'Email successfully verified']);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully',
            'access_token' => $token,
            'user' => $user
    ]);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 422);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link resent']);
    }
}