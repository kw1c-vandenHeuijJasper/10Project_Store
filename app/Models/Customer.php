<?php

namespace App\Models;

use App\Enums\OrderStatus;
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

    /**
     * Functions
     */
    public static function withActiveOrders(Customer $customer)
    {
        $orders = $customer->orders;
        if ($orders->isEmpty()) {
            return;
        } else {
            $activeOrders = $orders->where('status', OrderStatus::ACTIVE);
            if ($activeOrders->isEmpty()) {
                return;
            } else {
                return $activeOrders;
            }
        }
        dd('you arent supposed to come here');
    }

    public static function withActiveOrdersCount(Customer $customer)
    {
        $activeOrders = self::withActiveOrders($customer);
        if (! isset($activeOrders)) {
            return 0;
        } else {
            return $activeOrders->count();
        }
        dd('you arent supposed to come here');
    }
}
