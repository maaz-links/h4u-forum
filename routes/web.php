<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\BanController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\FakeProfileSettingController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\MailConfigController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
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
    
    Route::get('/change-password', [AdminLogController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AdminLogController::class, 'changePassword'])->name('password.update');


    Route::post('/user-profile/toggle-verified/{user}', [HomeController::class, 'toggleVerified'])
    ->name('user-profile.toggle-verified')->middleware('admin.perm:declare_badges');
    Route::post('/user-profile/toggle-top/{user}', [HomeController::class, 'toggleTop'])
        ->name('user-profile.toggle-top')->middleware('admin.perm:declare_badges');

    Route::group(['middleware' => ['admin.perm:manage_chat']], function () {
        Route::get('/user-profile/{name}/chat', [UserChatController::class, 'userchats'])->name('user-profile.chat');
        // Route::post('/opened-conversation', [UserChatController::class, 'conversation'])->name('open.conversation');
        Route::get('/all-chats', [UserChatController::class, 'index'])->name('chats.index');
        Route::get('/all-chats/{chat}', [UserChatController::class, 'conversation'])->name('open.conversation');
        Route::get('/all-chats/{chat}/message/{msg}', [UserChatController::class, 'editMsg'])->name('chat.messages.edit');
        Route::put('/all-chats/message/{msg}', [UserChatController::class, 'updateMsg'])->name('chat.messages.update');
        Route::delete('/all-chats/message/{msg}', [UserChatController::class, 'destroyMsg'])->name('chat.messages.destroy');
    });

    Route::group(['middleware' => ['admin.perm:manage_reviews']], function () {
        Route::get('/user-profile/{name}/reviews', [UserReviewController::class, 'allreviews'])->name('user-profile.reviews');
        Route::get('/user-profile/{review}/reviews/edit', [UserReviewController::class, 'editReview'])->name('edit.review');
        Route::put('/update-review', [UserReviewController::class, 'updateReview'])->name('update.review');
        Route::post('/delete-review', [UserReviewController::class, 'deleteReview'])->name('delete.review');
    });
    Route::group(['middleware' => ['admin.perm:configure_reviews']], function () {
        Route::get('/reviews-config', [ReviewConfigController::class, 'index'])->name('reviews-config.index');
        Route::post('/reviews-config', [ReviewConfigController::class, 'store'])->name('reviews-config.store');
    });
    
    //Route::get('/users-list', [ReviewConfigController::class, 'index'])->name('main.index');

    //DEV CONFIGS
    // Route::get('/configs', [ConfigController::class, 'index'])->name('configs.index');
    // Route::get('/configs/create', [ConfigController::class, 'create'])->name('configs.create');
    // Route::post('/configs', [ConfigController::class, 'store'])->name('configs.store');
    // Route::get('/configs/{mail_config}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
    // Route::put('/configs/{mail_config}', [ConfigController::class, 'update'])->name('configs.update');
    // Route::delete('/configs/{mail_config}', [ConfigController::class, 'destroy'])->name('configs.destroy');

    Route::get('/admin-logs', [AdminLogController::class, 'index'])->name('admin-logs.index');
    
    Route::group(['middleware' => ['admin.perm:manage_shop']], function () {
        // Shops Routes
        Route::get('/shops/all', [ShopController::class, 'index'])->name('shops.index');
        Route::get('/shop/create', [ShopController::class, 'create'])->name('shop.create');
        Route::post('/shop/store', [ShopController::class, 'store'])->name('shop.store');
        Route::get('/shop/edit/{id}', [ShopController::class, 'edit'])->name('shop.edit');
        Route::post('/shop/update', [ShopController::class, 'update'])->name('shop.update');
        Route::post('/shop/destroy', [ShopController::class, 'destroy'])->name('shop.destroy');

        Route::get('/credits-config', [CreditController::class, 'index'])->name('credits.index');
        Route::post('/credits-config', [CreditController::class, 'store'])->name('credits.store');
    
        // Shops Routes
        Route::get('/shop/transactions', [ShopTransactionController::class, 'index'])->name('shops.transactions');
        Route::post('/shop/transaction/destroy', [ShopTransactionController::class, 'destroy'])->name('shop.transaction.destroy');
    });

    Route::group(['middleware' => ['admin.perm:view_reports']], function () {
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('reports.index')->middleware('admin.perm:view_reports',);
        Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])
            ->name('reports.show')->middleware('admin.perm:view_reports');
        Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])
            ->name('reports.destroy');
    });

    Route::group(['middleware' => ['admin.perm:user_bans']], function () {
        Route::get('/user-profile/{username}/ban', [BanController::class, 'showBanManagement'])->name('admin.users.ban.show');
        Route::post('/user-profile/{user}/ban', [BanController::class, 'ban'])->name('admin.users.ban');
        Route::post('/user-profile/{user}/temp-ban', [BanController::class, 'tempBan'])->name('admin.users.temp-ban');
        Route::post('/user-profile/{user}/unban', [BanController::class, 'unban'])->name('admin.users.unban');
        Route::post('/user-profile/{user}/warn', [BanController::class, 'warn'])->name('admin.users.warn');
        

    });

    Route::resource('/contact-requests', \App\Http\Controllers\Admin\ContactRequestController::class)
        ->only(['index', 'show', 'destroy'])->middleware('admin.perm:read_contact_requests');
    
    Route::group(['middleware' => ['admin.perm:edit_templates']], function () {

        Route::get('/pages/{slug}', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{slug}/update', [PageController::class, 'update'])->name('pages.update');
        Route::prefix('admin')->group(function () {
            Route::get('/email-templates', [EmailTemplatesController::class, 'index'])
                ->name('admin.email-templates.index');
            Route::get('/email-templates/{type}/edit', [EmailTemplatesController::class, 'edit'])
                ->name('admin.email-templates.edit');
            Route::put('/email-templates/{type}', [EmailTemplatesController::class, 'update'])
                ->name('admin.email-templates.update');
        });

         // faq Routes
        Route::get('/faqs/all', [FaqController::class, 'index'])->name('faqs.index');
        Route::get('/faq/create', [FaqController::class, 'create'])->name('faq.create');
        Route::post('/faq/store', [FaqController::class, 'store'])->name('faq.store');
        Route::get('/faq/edit/{id}', [FaqController::class, 'edit'])->name('faq.edit');
        Route::post('/faq/update', [FaqController::class, 'update'])->name('faq.update');
        Route::post('/faq/destroy', [FaqController::class, 'destroy'])->name('faq.destroy');

    });

    Route::group(['middleware' => ['admin.perm:generate_profiles']], function () {
        Route::get('/profile-scripts', [FakeProfileSettingController::class, 'index'])->name('profile-scripts.index');
        Route::get('/profile-scripts/create', [FakeProfileSettingController::class, 'create'])->name('profile-scripts.create');
        Route::post('/profile-scripts/delete', [FakeProfileSettingController::class, 'destroy'])->name('profile-scripts.delete');
        Route::post('/profile-scripts', [FakeProfileSettingController::class, 'store'])->name('profile-scripts.store');
    });

    Route::group(['middleware' => ['admin.perm:manage_admins']], function () {
        Route::prefix('admin')->group(function () {
            // Admin Users
            Route::get('users', [AdminController::class, 'index'])->name('admin.users.index');
            Route::get('users/create', [AdminController::class, 'create'])->name('admin.users.create');
            Route::post('users', [AdminController::class, 'store'])->name('admin.users.store');
            Route::get('users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
            Route::match(['put', 'patch'], 'users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
            Route::delete('users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
            
            // Permissions
            Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');

            // GET /permissions/create - create
            Route::get('permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');

            // POST /permissions - store
            Route::post('permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');

            // GET /permissions/{permission}/edit - edit
            Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');

            // PUT/PATCH /permissions/{permission} - update
            Route::match(['put', 'patch'], 'permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');

            // DELETE /permissions/{permission} - destroy
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
        });
    });

    Route::middleware(['admin.perm:change_email_settings'])->group(function () {
    Route::get('/mail-config', [MailConfigController::class, 'edit'])
        ->name('mail-config.edit');
    Route::put('/mail-config', [MailConfigController::class, 'update'])
        ->name('mail-config.update');
});
});

});