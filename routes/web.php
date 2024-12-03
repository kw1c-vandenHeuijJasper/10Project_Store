<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

// TODO LIST
// [ ]Customer panel

// [ ]Enum for type  (product)
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.

// [ ]Maybe Invoices must be of a suitcase icon
