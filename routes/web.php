<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

if (app()->isLocal()) {
    Route::get('/', [LoginController::class, 'index']);
    Route::get('/loginAsAdmin', [LoginController::class, 'loginAsAdmin']);
    Route::get('/loginAsCustomer', [LoginController::class, 'loginAsCustomer']);
} else {
    Route::get('/', fn () => to_route('filament.customer.auth.login'));
}

if (app()->isLocal()) {
    Route::get('/tinker', function () {
        dd("There's nothing here yet 😭");
    });
}
