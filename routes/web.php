<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    return redirect('/admin');
});

Route::get('/tinker', function () {
    dump(Order::count() . ' total count');
    foreach (Order::all() as $order) {
        if ($order->products()->get()->pluck('pivot')->toArray() !== []) {
            dump(
                $order->products()->get()->pluck('pivot')->toArray(),
                ...$order->products()->get()->pluck('pivot')->pluck('order_id')
            );
        }
    }
});
