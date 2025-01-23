<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;

Route::get('/', function (): HtmlString {
    return new HtmlString("
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
    Auth::loginUsingId(1);

    return redirect('/panelPicker');
});

Route::get('/loginAsCustomer', function () {
    Auth::loginUsingId(2);

    return redirect('/panelPicker');
});
Route::get('/panelPicker', fn (): HtmlString => new HtmlString("
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
    User::with('activeOrders')
        ->get()
        ->filter(fn (User $user): bool => $user->activeOrders->count() > 1)
        ->each(function (User $user): void {
            $orderIds = $user->activeOrders
                ->reject(fn (Order $order): bool => $order == $user->activeOrders->last())
                ->pluck('id');

            Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::CANCELLED]);
        });
});

Route::get('/tinker', function (): void {
    dd("There's nothing here yet 😭");
});

// 

// [ ]add a 'CANCELLED' reason for orders?
// [ ]add product pictures & automatic removal of pictures

// [GROUP]Customer panel
// [ ]products "show" link like in ordersrelationmanager,

// Admin panel

// [GROUP]Invoices
// [ ]suitcase icon

// [GROUP]Mailing
// [ ]get mail when order is set to 'FINISHED' or 'CANCELLED'
