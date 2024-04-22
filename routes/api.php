<?php

use App\Http\Controllers\Api\v1\JWTAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::name('api.v1.')->group(function () {
        Route::prefix('auth')->name('auth.')->controller(JWTAuthController::class)->group(function () {
            Route::post('/register', 'register')->withoutMiddleware('auth:api')->name('register');
            Route::post('/login', 'login')->withoutMiddleware('auth:api')->name('login');
            Route::get('/profile', 'profile')->name('profile');
            Route::post('/logout', 'logout')->name('logout');
        });
    });
});
