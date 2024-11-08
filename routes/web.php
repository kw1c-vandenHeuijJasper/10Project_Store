<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

//TODO
// Make everything searchable/sortable... globalsearch etc.
// make all id's in resources max = highest id etc
// enum for type
