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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public static function usersActiveOrders(?Order $order = null, ?User $user = null): Builder
    {
        if ($order == null) {
            $user_id = Auth::id();
        } else {
            $user_id = $order->user_id;
        }
        if ($user) {
            $user_id = $user->id;
        }

        return Order::where('user_id', $user_id)->where('status', OrderStatus::ACTIVE);
    }

    public static function hasActiveOrder(?Order $order = null): bool
    {
        return self::usersActiveOrders($order)->count() > 0;
    }

    public static function hasNoActiveOrder(?Order $order = null): bool
    {
        return self::usersActiveOrders($order)->count() == 0;
    }

    public static function hasFaultyOrderAmount(?Order $order = null): bool
    {
        return self::usersActiveOrders($order)->count() > 1;
    }

    public static function shoppingCart()
    {
        return self::usersActiveOrders()->first();
    }
}
