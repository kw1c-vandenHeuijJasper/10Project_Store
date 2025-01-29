<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

if (App::isLocal()) {
    Route::get('/', [LoginController::class, 'index']);
    Route::get('/loginAsAdmin', [LoginController::class, 'loginAsAdmin']);
    Route::get('/loginAsCustomer', [LoginController::class, 'loginAsCustomer']);
} else {
    Route::redirect('/', route('filament.customer.auth.login'));
}

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

// 

// [ ]add a 'CANCELLED' reason for orders?
// [ ]add product pictures & automatic removal of pictures

// Customer panel
// [ ]products "show" link like in ordersrelationmanager,
