<?php

use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ReviewConfigController;
use App\Http\Controllers\Admin\UserChatController;
use App\Http\Controllers\Admin\UserReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/login');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['middleware' => ['auth']], function () {

Route::group(['middleware' => ['admin']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/user-profile/{name}', [HomeController::class, 'profile'])->name('user-profile');
    Route::get('/user-profile/{name}/chat', [UserChatController::class, 'allchats'])->name('user-profile.chat');

    Route::post('/opened-conversation', [UserChatController::class, 'conversation'])->name('open.conversation');

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
});

});