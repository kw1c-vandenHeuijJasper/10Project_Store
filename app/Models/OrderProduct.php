<?php

namespace App\Models;

use App\Observers\OrderProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[ObservedBy(OrderProductObserver::class)]
class OrderProduct extends Pivot
{
    protected $table = 'order_product';
}
