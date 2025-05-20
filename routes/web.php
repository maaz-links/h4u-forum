<?php

use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\BanController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ReviewConfigController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\ShopTransactionController;
use App\Http\Controllers\Admin\UserChatController;
use App\Http\Controllers\Admin\UserReviewController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/login');
});

//Auth::routes();

// Login Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
// Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes
// Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
// Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Password Confirmation Route (for security-sensitive actions)
// Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
// Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Email Verification Routes
// Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
// Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
// Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::group(['middleware' => ['auth']], function () {

Route::group(['middleware' => ['admin']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/user-profile/{name}', [HomeController::class, 'profile'])->name('user-profile');
    
    Route::get('/user-profile/{name}/chat', [UserChatController::class, 'userchats'])->name('user-profile.chat');
    // Route::post('/opened-conversation', [UserChatController::class, 'conversation'])->name('open.conversation');
    Route::get('/all-chats', [UserChatController::class, 'index'])->name('chats.index');
    Route::get('/all-chats/{chat}', [UserChatController::class, 'conversation'])->name('open.conversation');
    Route::get('/all-chats/{chat}/message/{msg}', [UserChatController::class, 'editMsg'])->name('chat.messages.edit');
    Route::put('/all-chats/message/{msg}', [UserChatController::class, 'updateMsg'])->name('chat.messages.update');
    Route::delete('/all-chats/message/{msg}', [UserChatController::class, 'destroyMsg'])->name('chat.messages.destroy');


    Route::get('/user-profile/{name}/reviews', [UserReviewController::class, 'allreviews'])->name('user-profile.reviews');
    Route::get('/user-profile/{review}/reviews/edit', [UserReviewController::class, 'editReview'])->name('edit.review');
    Route::put('/update-review', [UserReviewController::class, 'updateReview'])->name('update.review');
    Route::post('/delete-review', [UserReviewController::class, 'deleteReview'])->name('delete.review');
    
    Route::get('/credits-config', [CreditController::class, 'index'])->name('credits.index');
    Route::post('/credits-config', [CreditController::class, 'store'])->name('credits.store');
    Route::get('/reviews-config', [ReviewConfigController::class, 'index'])->name('reviews-config.index');
    Route::post('/reviews-config', [ReviewConfigController::class, 'store'])->name('reviews-config.store');

    //Route::get('/users-list', [ReviewConfigController::class, 'index'])->name('main.index');

    Route::get('/configs', [ConfigController::class, 'index'])->name('configs.index');
    Route::get('/configs/create', [ConfigController::class, 'create'])->name('configs.create');
    Route::post('/configs', [ConfigController::class, 'store'])->name('configs.store');
    Route::get('/configs/{mail_config}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
    Route::put('/configs/{mail_config}', [ConfigController::class, 'update'])->name('configs.update');
    Route::delete('/configs/{mail_config}', [ConfigController::class, 'destroy'])->name('configs.destroy');

    Route::get('/admin-logs', [AdminLogController::class, 'index'])->name('admin-logs.index');
    // Shops Routes
    Route::get('/shops/all', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/shop/create', [ShopController::class, 'create'])->name('shop.create');
    Route::post('/shop/store', [ShopController::class, 'store'])->name('shop.store');
    Route::get('/shop/edit/{id}', [ShopController::class, 'edit'])->name('shop.edit');
    Route::post('/shop/update', [ShopController::class, 'update'])->name('shop.update');
    Route::post('/shop/destroy', [ShopController::class, 'destroy'])->name('shop.destroy');

     // Shops Routes
    Route::get('/shop/transactions', [ShopTransactionController::class, 'index'])->name('shops.transactions');
    Route::post('/shop/transaction/destroy', [ShopTransactionController::class, 'destroy'])->name('shop.transaction.destroy');

    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('reports.index');
        
        // Show single report (GET /admin/reports/{report})
        Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])
            ->name('reports.show');
        
        // Delete report (DELETE /admin/reports/{report})
        Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])
            ->name('reports.destroy');

    Route::get('/user-profile/{username}/ban', [BanController::class, 'showBanManagement'])->name('admin.users.ban.show');
    Route::post('/user-profile/{user}/ban', [BanController::class, 'ban'])->name('admin.users.ban');
    Route::post('/user-profile/{user}/temp-ban', [BanController::class, 'tempBan'])->name('admin.users.temp-ban');
    Route::post('/user-profile/{user}/unban', [BanController::class, 'unban'])->name('admin.users.unban');
    Route::post('/user-profile/{user}/warn', [BanController::class, 'warn'])->name('admin.users.warn');
    
    Route::get('/change-password', [AdminLogController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AdminLogController::class, 'changePassword'])->name('password.update');

    Route::get('/pages/{slug}', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{slug}/update', [PageController::class, 'update'])->name('pages.update');

    Route::resource('/contact-requests', \App\Http\Controllers\Admin\ContactRequestController::class)
    ->only(['index', 'show', 'destroy']);

    Route::prefix('admin')->group(function () {
        Route::get('/email-templates', [EmailTemplatesController::class, 'index'])
            ->name('admin.email-templates.index');
        Route::get('/email-templates/{type}/edit', [EmailTemplatesController::class, 'edit'])
            ->name('admin.email-templates.edit');
        Route::put('/email-templates/{type}', [EmailTemplatesController::class, 'update'])
            ->name('admin.email-templates.update');
    });
});

});