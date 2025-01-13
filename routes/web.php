<?php

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;

Route::get('/', fn(): \Illuminate\Support\HtmlString => new HtmlString("
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
        "));

Route::get('/loginAsAdmin', function () {
    Auth::loginUsingId(1);

    return redirect('/panelPicker');
});

Route::get('/loginAsCustomer', function () {
    Auth::loginUsingId(2);

    return redirect('/panelPicker');
});
Route::get('/panelPicker', fn(): \Illuminate\Support\HtmlString => new HtmlString("
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
    "));

Route::get('/cancelRedundantActiveOrders', function (): void {
    Customer::with('activeOrders')
        ->get()
        ->filter(fn(Customer $customer): bool => $customer->activeOrders->count() > 1)
        ->each(function (Customer $customer): void {
            $orderIds = $customer->activeOrders
                ->reject(fn(Order $order): bool => $order == $customer->activeOrders->last())
                ->pluck('id');

            Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::CANCELLED]);
        });
});

Route::get('/tinker', function (): void {
    dd("There's nothing here yet 😭");
});

// 

// When using auth()->anything, intelephense says this is an error
// https://github.com/barryvdh/laravel-ide-helper might be able to resolve these fake errors!

// other cool package(s)
// https://spatie.be/docs/laravel-html/v3/introduction

// [GROUP]General
// [ ]when making user, also make customer ||
//      make custom register page ||
//      merge user and customer into 1 table!
// [ ]add a 'CANCELLED' reason for orders?
// [ ]add product pictures & automatic removal of pictures

// [GROUP]Customer panel
// [ ]products "show" link like in ordersrelationmanager,

// Admin panel

// [GROUP]Invoices
// [ ]suitcase icon

// [GROUP]Mailing
// [ ]get mail when order is set to 'FINISHED' or 'CANCELLED'
