<?php

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

// when customer has no addresses, disable those fields in order edit/create

// [ ]Order status - Only subtract stock if status is done (make enum and db column)

// [ ]Customer panel
// [ ]Enum for type  (product)
// [ ]Maybe Invoices must be of a suitcase icon
