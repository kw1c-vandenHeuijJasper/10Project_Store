<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    // dd("There's nothing here yet 😭");
    // TODO
    dd(
        $customerUserId = \App\Models\Customer::inRandomOrder()->first()->id,
        \App\Models\Customer::where('user_id', $customerUserId)->get(),
    );
});

// TODO LIST
// [ ]Make everything searchable/sortable... globalsearch etc.
// [ ]enum for type  (product)
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.

// [ ]Invoices must be of a suitcase icon
