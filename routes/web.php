<?php

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;

Route::get('/', function () {
    Auth::logout();

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

Route::get('/cancelRedundantActiveOrders', function () {
    Customer::with('activeOrders')
        ->get()
        ->filter(fn (Customer $customer) => $customer->activeOrders->count() > 1)
        ->each(function (Customer $customer) {
            $orderIds = $customer->activeOrders
                ->reject(fn (Order $order) => $order == $customer->activeOrders->last())
                ->pluck('id');

            Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::CANCELLED]);
        });
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

// 

// [GROUP]General
// [ ]add a 'CANCELLED' reason?

// [GROUP]Customer panel
// [ ]products table / page with "add to cart" buttons
// [ ]products relation manager
//      detaching products,
//      change amount to order,
//      products "show" link like in ordersrelationmanager,

// Admin panel

// [GROUP]Invoices
// [ ]suitcase icon

// [GROUP]Mailing
// [ ]get mail when order is set to 'FINISHED' or 'CANCELLED'
