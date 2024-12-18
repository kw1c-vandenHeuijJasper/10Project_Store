<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class OrderObserver
{
    //TODO handle creating on admin side, so none of this is needed anymore!
    // public function creating(Order $order)
    // {
    //     if (Str::contains(URL::previous(), 'admin')) {
    //         if (Order::hasNoActiveOrder($order) && $order->status === OrderStatus::ACTIVE) {
    //         } else {
    //             if ($order->status != OrderStatus::ACTIVE) {
    //                 throw new Exception('Incorrect status; Not ACTIVE');
    //             }

    //             if (Order::hasFaultyOrderAmount($order) && $order->status === OrderStatus::ACTIVE) {
    //                 throw new Exception('Faulty active order count!');
    //             }

    //             if (Order::hasActiveOrder($order) && $order->status === OrderStatus::ACTIVE) {
    //                 throw new Exception('You already have an active order! Try again.');
    //             } else {
    //                 throw new Exception(
    //                     'You have an active order,
    //                     but you are trying to create an order with another status'
    //                 );
    //             }
    //             throw new Exception('Unknown error');
    //         }
    //     }
    // }

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
}
