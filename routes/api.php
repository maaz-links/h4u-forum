<?php

use App\Http\Controllers\ApiAuthentication;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\HostessServiceController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('web')->group(function () {
//     Route::get('/sanctum/csrf-cookie', function () {
//         return response()->noContent();
//     });
    
// });

Route::apiResource('hostess-services', HostessServiceController::class);
Route::get('profile-info',[UserProfileController::class,'index']);

Route::post('register',[ApiAuthentication::class,'register']);
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed');
//Route::get('email/verify/{id}',[ApiAuthentication::class,'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('auth:sanctum');


Route::post('/login', [ApiAuthentication::class, 'login'])->name('login');
Route::post('/logout', [ApiAuthentication::class, 'logout'])->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest:sanctum')
    ->name('password.email');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest:sanctum')
    ->name('password.update');

    Route::get('/hello', function (Request $request) {
        return response()->json(['data' => 'hello world']);
    });//->middleware('auth:sanctum');