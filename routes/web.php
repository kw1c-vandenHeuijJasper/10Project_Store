<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    // dd("There's nothing here yet 😭");

    return Customer::find(29)->orders->map(fn ($order) => $order->products->sum('pivot.total'))->sum();
});

// TODO LIST
// [ ]Make everything searchable/sortable... globalsearch etc.
// [ ]enum for type  (product)
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.

// [ ]Invoices must be of a suitcase icon
