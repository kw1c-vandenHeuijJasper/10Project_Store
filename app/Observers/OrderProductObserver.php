<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Product;

class OrderProductObserver
{
    //TODO DELETE WHOLE OBSERVER
    // I can already delete this safely, because this is not longer needed,
    // but its nice to have when testing constantly
    /**
     * Handle the OrderProduct "deleting" event.
     */
    public function deleting(OrderProduct $orderProduct): void
    {
        $query = OrderProduct::query()
            ->whereOrderId($orderProduct->order_id)
            ->whereProductId($orderProduct->product_id)
            ->first();
        $product = Product::find($query->product_id);
        $stock = $query->amount + $product->stock;
        $product->update(['stock' => $stock]);
    }
}
