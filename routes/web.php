<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tinker', function () {
    dd(
        \App\Models\Adress::first()->get(),
        \App\Models\Customer::first()->get(),
        // \App\Models\Order_Product::first()->get(),
        \App\Models\Order::first()->get(),
        \App\Models\Product::first()->get(),
    );
});
