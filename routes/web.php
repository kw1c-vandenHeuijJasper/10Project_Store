<?php

use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Auth::loginUsingId(1);

    return redirect('/admin');
});

Route::get('/tinker', function () {
    // dd("There's nothing here yet 😭");

    $pivot = OrderProduct::where('order_id', 23);
    // $pivot->get()->toArray();

    //TODO name vars
    $collection = $pivot->get()->map(function ($data) {
        return ['id' => $data->id, 'product_id' => $data->product_id, 'amount' => $data->amount];
    });

    $collection->map(function ($order) {
        $product = Product::find($order['product_id']);
        $product->stock = $product->stock + $order['amount'];
        $product->save();
    });
    // $pivot->delete();
});

// 
// TODO LIST
// FIXME when seeding, stock can get sub 0
// when a products stock is 0 dont show in select

// [ ]Customer panel

// [ ]Enum for type  (product)
// [ ]When ordering, the selected amount of items you ordered needs to be subtracted from the stock.

// [ ]Maybe Invoices must be of a suitcase icon
