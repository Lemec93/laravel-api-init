<?php

use Illuminate\Support\Facades\Route;

/**
 * Route auth
 */
Route::prefix('v1')
    ->namespace('API')
    ->group(function () {
        require __DIR__ . '/API/v1.php';
    });
