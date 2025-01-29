<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Functions
     */
    public static function cancelRedundantActiveOrders(): void
    {
        User::with('activeOrders')
            ->get()
            ->filter(fn (User $user): bool => $user->activeOrders->count() > 1)
            ->each(function (User $user): void {
                $orderIds = $user->activeOrders
                    ->reject(fn (Order $order): bool => $order == $user->activeOrders->last())
                    ->pluck('id');

                Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::CANCELLED]);
            });
    }
}
