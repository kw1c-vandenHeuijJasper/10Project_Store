<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Str;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->update([
            'order_reference' => function () {
                $i = random_int(1, 999999999);
                (string) $preOrder = Str::padLeft($i, 9, 0);

                return 'ORD#' . $preOrder;
            },
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleting" event.
     */
    public function deleting(Order $order): void
    {
        $pivot = OrderProduct::where('order_id', $order->id);

        $collection = $pivot->get()->map(
            fn($data) => ['id' => $data->id, 'product_id' => $data->product_id, 'amount' => $data->amount]
        );

        $collection->map(function ($order) {
            $product = Product::find($order['product_id']);
            $product->stock = $product->stock + $order['amount'];
            $product->save();
        });
    }
}
