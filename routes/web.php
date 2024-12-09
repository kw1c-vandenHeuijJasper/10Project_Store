<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

// 
// TODO LIST
// [ ]customer filter - when Has Orders filter is true show has products in order ???

// [ ]Customer panel

// [ ]Enum for type  (product)
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.

// [ ]Maybe Invoices must be of a suitcase icon
