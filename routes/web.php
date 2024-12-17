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

Route::get('/activeOrders', function () {
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
// TODO LIST

// [GROUP]General
// [ ]when bulk deleting customers, orders are cascade on delete, but the products are not "refunded (not for sure)"
// [ ]only subtract stock when customer clicks order and the orderStatus is set to 'FINISHED' by an admin
// [ ]figure out what to do if person A has a product in their cart,
//      but person B bought the rest of the stock,
//      so stock is at less then what person A wanted to order
//      fix is in Admin panel's TODO!
// [ ]add a 'CANCELLED' reason?

// [GROUP]Customer panel
// [ ]products relation manager
// detaching products,
// change amount to order,
// products "show" link like in ordersrelationmanager,

// [ ]products table with "add to cart" buttons

// [GROUP]Admin panel
// [ ]manual review place for all orders that are of status 'processing'
//      with products and stock side-to-side to confirm
//      when confirmed, status gets set to 'FINISHED' & stock will get depleted

// [GROUP]Invoices
// [ ]suitcase icon

// [GROUP]Mailing
// [ ]get mail when order is set to 'FINISHED' or 'CANCELLED'
