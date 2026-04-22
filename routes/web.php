<?php

use App\Http\Controllers\MagicLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/magic-login/{user}', [MagicLoginController::class, 'login'])
    ->name('magic.login')
    ->middleware('signed');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

// Catch-all route to serve the Vue SPA.
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
