<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tinker', function () {
    dd(
        // $collection = Product::inRandomOrder()->limit(3)->get()->map(function ($item) {
        //     return [
        //         'product_id' => $item->id,
        //         'amount' => random_int(1, 10),
        //         'price' => $item->price,
        //     ];
        // }),

        // Order::first()->products()->attach($collection),
        Order::first()->products()->get()->pluck('pivot')->where('product_id', 5)->pluck('order_id')->toArray(),
        Product::first()->orders()->get()->pluck('pivot')->whereNotIn('order_id', 1)->pluck('product_id')->toArray()
        // Order::first()->products()->get()->count(),
    );
});
