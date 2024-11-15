<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    // dd("There's nothing here yet 😭");

    $i = random_int(1, 999999999);
    $foo = Str::padLeft($i, 9, 0);
    $foo = 'ORD#' . $foo;
    dd($foo);
});

// TODO LIST
// [ ]Make everything searchable/sortable... globalsearch etc.
// [ ]enum for type  (product)
// [ ]Invoices must be of a suitcase icon
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.
