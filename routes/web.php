<?php

use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    return redirect('/admin');
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

//TODO
// Make everything searchable/sortable...
// This is the best code editor out there