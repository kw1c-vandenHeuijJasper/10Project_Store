<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('amount', 'price', 'total', 'created_at', 'updated_at')->using(OrderProduct::class);
    }

    public function pivot(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function invoiceAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public static function customersActiveOrders(?Order $order = null, ?Customer $customer = null): Builder
    {

        if ($order == null) {
            $customer_id = Customer::whereUserId(Auth::id())->first()?->id;
        } else {
            $customer_id = $order->customer_id;
        }
        if ($customer) {
            $customer_id = $customer->id;
        }

        return Order::whereCustomerId($customer_id)->where('status', OrderStatus::ACTIVE);
    }

    public static function hasActiveOrder(?Order $order = null): bool
    {
        return self::customersActiveOrders($order)->count() > 0;
    }

    public static function hasNoActiveOrder(?Order $order = null): bool
    {
        return self::customersActiveOrders($order)->count() == 0;
    }

    public static function hasFaultyOrderAmount(?Order $order = null): bool
    {
        return self::customersActiveOrders($order)->count() > 1;
    }

    public static function shoppingCart()
    {
        return self::customersActiveOrders()->first();
    }
}
