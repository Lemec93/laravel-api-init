<?php

use Illuminate\Support\Facades\Route;

/**
 * Route auth
 */
Route::prefix('auth')->namespace('Auth')->name('auth.')->group(function () {
    // Login
    Route::post('login', 'LoginController')
        ->name('login');

    // Register
    Route::post('register', 'RegisterController')
        ->name('register');

    // Reset password
    Route::post('activate/account', 'ActiveAccountController')
        ->name('active.account');

    // Forgot password
    Route::post('forgot/password', 'ForgotPasswordController')
        ->name('password.forgot');

    // Reset password
    Route::post('reset/password', 'ResetPasswordController')
        ->name('password.reset');

    // Logout
    Route::post('logout', 'LogoutController')
        ->name('logout');
});
