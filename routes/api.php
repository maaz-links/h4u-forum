<?php

use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\ApiAuth\ApiAuthenticationController;
use App\Http\Controllers\ApiAuth\PasswordResetController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UpdateProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ApiAuth\VerificationController;
use App\Http\Resources\UserResource;
use App\Models\EuropeCountry;
use App\Models\EuropeProvince;
use App\Models\ShownService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/send-message', function () {
    $message = 'Hello from Laravel at ' . now();
    event(new App\Events\Chat\MessageSent($message));
    return response()->json(['status' => 'Message sent!']);
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::post('register', [ApiAuthenticationController::class, 'register']);
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed');
//Route::get('email/verify/{id}',[ApiAuthentication::class,'verify'])->name('verification.verify');
// Route::post('/email/resend', [VerificationController::class, 'resend'])
//     ->middleware('auth:sanctum');

Route::post('/login', [ApiAuthenticationController::class, 'login'])->name('login');
Route::post('/verify-otp', [ApiAuthenticationController::class, 'verifyOtp']);
Route::post('/resend-otp', [ApiAuthenticationController::class, 'resendOtp']);
Route::post('/logout', [ApiAuthenticationController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest:sanctum')
    ->name('password.email')->middleware('throttle:2,1');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest:sanctum')
    ->name('password.update');

Route::get('/verify-impersonation/{id}/{hash}', [ApiAuthenticationController::class, 'verifyImpersonation'])
     ->name('admin.impersonation')
     ->middleware('signed');


Route::get('/miscdata', [MiscController::class, 'miscdata']);



Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('attachments.show');

// Route::get('/countries', function() {
//     return EuropeCountry::ordered()->get(['id', 'name']);
// });
Route::get('/countries', function() {
    return EuropeCountry::with(
        ['provinces' => function($query) {
            $query->select('id','country_id','name')->ordered();
        }
    
    ])->ordered()->get(['id', 'name']);
});
Route::get('/provinces', function() {
    return EuropeProvince::orderBy('name')->get(['id', 'name']);
});
// Get provinces for a specific country ordered
Route::get('/countries/{countryId}/provinces', function($countryId) {
    return EuropeProvince::where('country_id', $countryId)
        ->ordered()
        ->get(['id', 'name']);
});

Route::post('/search-guest', [SearchController::class, 'searchByGuest']);
Route::get('/user-profile-guest/{username}',[UserProfileController::class, 'profileByGuest']);

Route::get('/ban-report/{username}',[UserProfileController::class, 'banReport']);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('check.banned')->group(function () {

        Route::post('/heartbeat', function (Request $request) {
            $user = $request->user();
            $user->update(['last_seen' => now()]);
            return true;
        });        
    // Route::middleware('reset.msglimit')->group(function () {

        // Route::get('/user', function (Request $request) {
        //     $user = User::with('profile')->where('id', '=', $request->user()->id)->first();
        //     $user->rating = $user->getRatingAttribute();
        
        //     // dd($user->profile());
        //     return response()->json($user);
        
        // });     
        Route::get('/user', function (Request $request) {
            $user = User::with('profile')->where('id', '=', $request->user()->id)->first();
            //$user->rating = $user->getRatingAttribute();
            return new UserResource($user);
            // dd($user->profile());
            // return response()->json($user);
        });        

        Route::post('/update-profile', [UpdateProfileController::class, 'updateProfile']);
        Route::post('/update-personal', [UpdateProfileController::class, 'updatePersonalInfo']);
        Route::post('/change-password', [UpdateProfileController::class, 'changePassword']);
        
        Route::get('/attachments', [AttachmentController::class, 'index']);
        Route::post('/attachments', [AttachmentController::class, 'store']);
        Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy']);
            
        Route::post('/attachments/{id}/set-profile-picture', [AttachmentController::class, 'setProfilePicture']);
        

        Route::post('/search', [SearchController::class, 'searchByUser']);
        Route::get('/user-profile/{username}',[UserProfileController::class, 'profileByUser']);
        //Route::get('/last-views',[UserProfileController::class, 'getLastViews']);

        Route::middleware('check.activated')->group(function () {
            Route::post('/chats/credits', [ChatController::class, 'create']);
            // Route::post('/chats/credits', [ChatController::class, 'createChat']);
            // Route::post('/chats/freemsg', [ChatController::class, 'freeChat']);
            // Chat routes
            Route::get('/chats', [ChatController::class, 'index']);
            //Route::get('/chats/{chat}', [ChatController::class, 'show']);
            Route::post('/chats/{chat}/archive', [ChatController::class, 'archive']);
            Route::post('/chats/{chat}/unarchive', [ChatController::class, 'unarchive']);
            Route::get('/chats-activity', [ChatController::class, 'getActivity']);
            // Message routes
            Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);
            Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);
            //Route::get('/chats/{chat}/messages/poll', [MessageController::class, 'poll']);

            Route::post('/chats/{chat}/messages/read', [MessageController::class, 'markAsRead']);

            Route::post('/report-user', [UserProfileController::class, 'reportUser']);
            Route::post('/report-chat', [UserProfileController::class, 'reportChat']);

            Route::middleware('check.males-only')->group(function () {
                Route::get('shops',[ShopController::class,'index']);
                Route::get('shop/{id}',[ShopController::class,'shop']);
                Route::post('/add/user-credits',[ShopController::class,'addCredits']);
                Route::get('/user-purchased',[ShopController::class,'userPurchased']);

                Route::post('/create-payment-intent',[PaymentController::class,'createPaymentIntent']);
                Route::post('/paypal/create-order', [PaymentController::class, 'createOrder']);
                Route::get('/paypal/success', [PaymentController::class, 'success']);
                Route::get('/paypal/cancel', [PaymentController::class, 'cancel']);
            });

        });

        Route::get('/unread-messages-count', [MessageController::class, 'unreadCount']);

        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews', [ReviewController::class, 'index']);

            // Route::post('/set-customer-credits', [UserProfileController::class, 'setCustomerCredits']);
            // Route::post('/set-customer-credits/{amount}', [UserProfileController::class, 'setCustomerCredits']);

        //Route::delete('/delete-account',[UpdateProfileController::class,'deleteAccount']);
        Route::post('/send-cancellation-request',[UpdateProfileController::class,'sendCancellationRequest'])
        ->middleware('throttle:1,60');
    //});

    });
    
});

// Route::get('/randomize-profiles', [UserProfileController::class, 'randomize']);
Route::get('/my-terms', [MiscController::class, 'ApiGetTerms']);
Route::get('/my-privacy', [MiscController::class, 'ApiGetPrivacy']);
Route::get('/my-cookies', [MiscController::class, 'ApiGetCookiesInfo']);
Route::get('/my-credits', [MiscController::class, 'apiGetPaymentsCredits']);
Route::post('/contact-form', [MiscController::class, 'apiContactForm'])->middleware('throttle:1,60');
Route::get('/my-faqs', [MiscController::class, 'apiGetFaqs']);
Route::get('my-shown-services',function () {
    $shownServices = ShownService::orderBy('display_order')->get();
    foreach ($shownServices as $s) {
        $s->title = $s->name;
        $s->image = asset('storage/'.$s->image_path);
        $s->image_path = asset('storage/'.$s->image_path);
    }
    return response()->json($shownServices);
});

Route::get('/twilio-test', function (Request $request) {
    $type = $request->query('phone', 'all');
    $twilio = new \App\Services\TwilioService();
    return $twilio->sendSms("+$type", "Its working");
});