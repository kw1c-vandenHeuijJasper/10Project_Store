<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    // dd("There's nothing here yet 😭");

    // ik wil alle user.names's waar een customer de user_id van heeft
    // get all user_id's then look in that user's id and get the name

    Customer::pluck('user_id')->mapWithKeys(
        function ($id) {
            dump([$id => \App\Models\User::whereId($id)->first()->name]);
        }
    );

    // Customer::with('user')->get()->each(
    //     function (Customer $customer) {
    //         dump($customer->user->name);
    //     }
    // );
});

// TODO LIST
// [ ]Make everything searchable/sortable... globalsearch etc.
// [ ]enum for type  (product)
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.

// [ ]Invoices must be of a suitcase icon
