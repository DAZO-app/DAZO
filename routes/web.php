<?php

use Illuminate\Support\Facades\Route;

// Catch-all route to serve the Vue SPA.
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
