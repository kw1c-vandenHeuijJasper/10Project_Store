<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;

Route::get('/', function () {
    return new HtmlString("
    " . Auth::user() . "
        <h1>
            Do you want to go log in as <a href='/loginAsAdmin'>
                admin
            </a>
            or  
            <a href='/loginAsCustomer'>
                customer
            </a>
            ?
        </h1>
    ");
});

Route::get('/loginAsAdmin', function () {
    Auth::logout();
    Auth::loginUsingId(1);
    return redirect('/panelPicker');
});

Route::get('/loginAsCustomer', function () {
    Auth::logout();
    Auth::loginUsingId(2);
    return redirect('/panelPicker');
});
Route::get('/panelPicker', function () {
    return new HtmlString("
    " . Auth::user() . "
        <h1>
            Do you want to go to the <a href='/admin'>
                admin
            </a> 
            or the 
            <a href='/customer'>
                customer panel
            </a>
            ?
        </h1>
    ");
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

// 
// TODO LIST

// [ ]Order status - Only subtract stock if status is done (make enum and db column)

// [ ]Customer panel
// [ ]Enum for type  (product)
// [ ]Maybe Invoices must be of a suitcase icon
