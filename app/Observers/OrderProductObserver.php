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

    //TODO 'duplicate' code with other observer
    /**
     * Handle the OrderProduct "deleting" event.
     */
    public function deleting(OrderProduct $orderProduct): void
    {
        $query = OrderProduct::query()
            ->where('order_id', $orderProduct->order_id)
            ->where('product_id', $orderProduct->product_id)
            ->first();
        $product = Product::find($query->product_id);
        $amount = $query->amount;
        $stock = $product->stock;
        $new = $amount + $stock;
        $product->update(['stock' => $new]);
    }

    public function forceDeleted(OrderProduct $orderProduct): void
    {
        $this->deleting($orderProduct);
    }
}
