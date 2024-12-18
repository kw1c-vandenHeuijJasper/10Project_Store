<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderProductObserver
{
    //TODO
    public function creating(OrderProduct $orderProduct): void
    {
        // dd(Auth::user());
        // $order = Order::firstOrCreate(
        //     [
        //         'customer_id' => Auth::user()->customer->id,
        //         'status' => OrderStatus::ACTIVE,
        //     ],
        //     [
        //         'shipping_address_id' => Address::inRandomOrder()->first(), //TODO
        //         'invoice_address_id' => Address::inRandomOrder()->first(),
        //     ]
        // );

        // $orderProduct->order_id = $order->id;
        // dd($order, $orderProduct);
    }

    /**
     * Handle the OrderProduct "created" event.
     */
    public function created(OrderProduct $orderProduct): void
    {
        $product = Product::find($orderProduct->product_id);
        $left = $product->stock - $orderProduct->amount;
        $product->update(['stock' => $left]);
    }

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
