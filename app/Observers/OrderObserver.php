<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {

        $i = random_int(1, 999999999);
        (string) $preOrder = Str::padLeft($i, 9, 0);
        $foo = (string) 'ORD#'.$preOrder;

        $order->update([
            'reference' => $foo,
        ]);
    }
}
