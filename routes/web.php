<?php

use Illuminate\Support\Facades\Route;
use Rapidez\Postcode\Http\Controllers\PostcodeController;

Route::middleware('web')->group(function () {
    // We place this in this middleware for the CSRF protection.
    Route::match(['get', 'post'], '/api/postcode', PostcodeController::class);
});
