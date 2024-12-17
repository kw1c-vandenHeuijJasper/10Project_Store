<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class OrderObserver
{
    public function creating(Order $order): void
    {
        if (Str::contains(URL::previous(), 'admin')) {
            if (Order::hasNoActiveOrder($order) && $order->status === OrderStatus::ACTIVE) {
            } else {
                if ($order->status != OrderStatus::ACTIVE) {
                    throw new Exception('Incorrect status; Not ACTIVE');
                }

                if (Order::hasFaultyOrderAmount($order) && $order->status === OrderStatus::ACTIVE) {
                    throw new Exception('Faulty active order count!');
                }

                if (Order::hasActiveOrder($order) && $order->status === OrderStatus::ACTIVE) {
                    throw new Exception('You already have an active order! Try again.');
                } else {
                    throw new Exception(
                        'You have an active order, 
                        but you are trying to create an order with another status'
                    );
                }
                throw new Exception('Unknown error');
            }
        } else {
            // dd(
            //     $order
            //     // 'The request did not come from the admin side, uncomment this to activate again.',
            //     // 'Context: "OrderObserver.php" at line 42-45'
            // );
            // if (! isset($order->status)) {
            // dd($order->status, $order);
            // }
            if (Order::hasActiveOrder($order)) {
                throw new Exception('You already have an active order! Complete that one first!');
            }
        }
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->update([
            'reference' => function () {
                $i = random_int(1, 999999999);
                (string) $preOrder = Str::padLeft($i, 9, 0);

                return 'ORD#'.$preOrder;
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

    //TODO 'duplicate' code with other observer
    /**
     * Handle the Order "deleting" event.
     */
    public function deleting(Order $order): void
    {
        $collection = OrderProduct::whereOrderId($order->id)->get()->map(
            fn ($data) => [
                'id' => $data->id,
                'product_id' => $data->product_id,
                'amount' => $data->amount,
            ]
        );

        $collection->each(function ($order) {
            $product = Product::find($order['product_id']);
            $product->stock = $product->stock + $order['amount'];
            $product->save();
        });
    }
}
