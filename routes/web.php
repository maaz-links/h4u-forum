<?php

use App\Http\Controllers\Admin\ConfigController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group([
    'middleware' => ['admin']
], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    

    Route::get('/configs', [ConfigController::class, 'index'])->name('configs.index');
    Route::get('/configs/create', [ConfigController::class, 'create'])->name('configs.create');
    Route::post('/configs', [ConfigController::class, 'store'])->name('configs.store');
    Route::get('/configs/{mail_config}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
    Route::put('/configs/{mail_config}', [ConfigController::class, 'update'])->name('configs.update');
    Route::delete('/configs/{mail_config}', [ConfigController::class, 'destroy'])->name('configs.destroy');
});