<?php

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

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


//TODO
// Make everything searchable/sortable... globalsearch etc.
// make all id's in resources max = highest id etc
// enum for type  (product)