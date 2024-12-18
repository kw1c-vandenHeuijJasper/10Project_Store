<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    /**
     * Relations
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrders(): HasMany
    {
        return $this->orders()->whereStatus(OrderStatus::ACTIVE);
    }

    /**
     * Scopes
     */
    public function scopeNonActiveOrders(Builder $query): Builder
    {
        return $query->whereHas('orders', function (Builder $query) {
            $query->whereIn('status', [OrderStatus::ACTIVE, OrderStatus::PROCESSING]);
        });
    }

    public function scopeWithNoWrongOrders(Builder $query): Builder
    {
        return $query->whereDoesntHave('orders', function (Builder $query) {
            $query->whereIn('status', [OrderStatus::ACTIVE, OrderStatus::PROCESSING]);
        });
    }

    /**
     * Functions
     */
    public function hasActiveOrders(): bool
    {
        if ($this->activeOrders()->get()->toArray() == []) {
            return false;
        } else {
            return true;
        }
    }
}
