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

// 

// now that my customer and user are the same, I now can use a customer edit-profile page...
// This one took to long im not going to change it over

// [ ]add a 'CANCELLED' reason for orders?

// [ ]products "show" link like in ordersrelationmanager,
// [ ]add product pictures & automatic removal of pictures
