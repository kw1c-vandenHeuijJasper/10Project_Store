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
    $customersWithActiveOrders = Customer::get()->mapWithKeys(function ($customer) {
        return [$customer->id => Customer::withActiveOrdersCount($customer)];
    });
    $filtered = $customersWithActiveOrders->map(function ($filter) {
        if ($filter < 2) {
            return null;
        } else {
            return $filter;
        }
    })->whereNotNull();

    $customersWithActiveOrders = Customer::get()->mapWithKeys(function ($customer) {
        return [$customer->id => Customer::withActiveOrders($customer)];
    })->whereNotNull();

    $filter_out = $customersWithActiveOrders->map(function ($activeOrder) {
        if ($activeOrder->count() < 2) {
            return null;
        } else {
            return $activeOrder;
        }
    })->whereNotNull();

    $cancelAllExceptLast = $filter_out->map(function ($item) {
        $toKeep = $item->last();
        $item->map(function ($order) {
            $order->update(['status' => OrderStatus::CANCELLED]);
        });
        $toKeep->update(['status' => OrderStatus::ACTIVE]);
    });

    // now set all active order that are not the most recent to cancelled
    $customerWithFaultyOrderAmount = $filtered->keys()->map(function ($i) {
        return Customer::find($i)->user->name;
    });
});

Route::get('/tinker', function () {
    dd("There's nothing here yet 😭");
});

// 
//https://marketplace.visualstudio.com/items?itemName=figma.figma-vscode-extension
// TODO LIST

// [GROUP]General
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
