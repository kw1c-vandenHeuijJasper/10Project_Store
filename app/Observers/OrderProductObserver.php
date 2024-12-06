<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Product;

class OrderProductObserver
{
    /**
     * Handle the OrderProduct "created" event.
     */
    public function created(OrderProduct $orderProduct): void
    {
        $product_id = $orderProduct->product_id;
        $product = Product::find($product_id);
        $stock = $product->stock;
        $amount = $orderProduct->amount;
        $left = $stock - $amount;
        $product->update(['stock' => $left]);
    }

    /**
     * Handle the OrderProduct "deleting" event.
     */
    public function deleting(OrderProduct $orderProduct): void
    {
        $query = OrderProduct::query()
            ->where('order_id', $orderProduct->order_id)
            ->where('product_id', $orderProduct->product_id);
        $OrderProduct = $query->first();
        $product = Product::whereId($OrderProduct->product_id);
        $amount = $OrderProduct->amount;
        $stock = $product->first()->stock;
        $new = $amount + $stock;
        $product->update(['stock' => $new]);
    }
}
