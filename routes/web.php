<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;

Route::get('/', function () {
    return new HtmlString('
    '.Auth::user()."
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
    return new HtmlString('
    '.Auth::user()."
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

// [GROUP]General
// [ ]only subtract stock when customer clicks order and the orderStatus is set to 'FINISHED' by an admin
// [ ]add a 'CANCELLED' reason?

// [GROUP]Customer panel
// [ ]make custom blade page where you see all products lined up
//      and you can add them to the current/latest order.
//      Filtering and search system for products.
// [ ]order history (choose table/blade)

// [GROUP]Admin panel
// [ ]manual review place for all orders that are of status 'processing'
//      with products and stock side-to-side to confirm
//      when confirmed, status gets set to 'FINISHED' & stock will get depleted

// [GROUP]Invoices
// [ ]suitcase icon

// [GROUP]Mailing
// [ ]get mail when order is set to 'FINISHED' or 'CANCELLED'
