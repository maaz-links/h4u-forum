<?php

use App\Http\Controllers\ApiAuth\ApiAuthenticationController;
use App\Http\Controllers\ApiAuth\PasswordResetController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\HostessServiceController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ApiAuth\VerificationController;
use App\Models\EuropeCountry;
use App\Models\EuropeProvince;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = User::with('profile')->where('id', '=', $request->user()->id)->first();

    // dd($user->profile());
    return response()->json($user);

})->middleware('auth:sanctum');

// Route::middleware('web')->group(function () {
//     Route::get('/sanctum/csrf-cookie', function () {
//         return response()->noContent();
//     });

// });

Route::apiResource('hostess-services', HostessServiceController::class);
Route::get('profile-info', [UserProfileController::class, 'index']);

Route::post('register', [ApiAuthenticationController::class, 'register']);
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed');
//Route::get('email/verify/{id}',[ApiAuthentication::class,'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('auth:sanctum');


Route::post('/login', [ApiAuthenticationController::class, 'login'])->name('login');
Route::post('/logout', [ApiAuthenticationController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest:sanctum')
    ->name('password.email');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest:sanctum')
    ->name('password.update');

Route::get('/hello', function (Request $request) {
    return response()->json(['data' => 'hello world']);
});//->middleware('auth:sanctum');

Route::get('/miscdata', [MiscController::class, 'miscdata']);

Route::post('/update-profile', [MiscController::class, 'updateProfile'])
    ->middleware('auth:sanctum');

Route::post('/change-password', [MiscController::class, 'changePassword'])
    ->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/attachments', [AttachmentController::class, 'index']);
    Route::post('/attachments', [AttachmentController::class, 'store']);
    Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy']);
    
    Route::post('/attachments/{id}/set-profile-picture', [AttachmentController::class, 'setProfilePicture']);
});
Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('attachments.show');

Route::get('/countries', function() {
    return EuropeCountry::ordered()->get(['id', 'name']);
});
Route::get('/provinces', function() {
    return EuropeProvince::ordered()->get(['id', 'name']);
});
// Get provinces for a specific country ordered
Route::get('/countries/{countryId}/provinces', function($countryId) {
    return EuropeProvince::where('country_id', $countryId)
        ->ordered()
        ->get(['id', 'name']);
});

Route::post('/search-guest', [SearchController::class, 'searchByGuest']);
Route::post('/search', [SearchController::class, 'searchByUser'])->middleware('auth:sanctum');

Route::get('/user-profile-guest/{username}',[UserProfileController::class, 'profileByGuest']);
Route::get('/user-profile/{username}',[UserProfileController::class, 'profileByUser'])->middleware('auth:sanctum');

Route::get('/last-views',[UserProfileController::class, 'getLastViews'])->middleware('auth:sanctum');

Route::get('/randomize-profiles', [UserProfileController::class, 'randomize']);
