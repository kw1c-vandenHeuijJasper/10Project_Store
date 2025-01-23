<?php

namespace App\Models;

use Filament\Panel;
use App\Enums\OrderStatus;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'bool',
        ];
    }


    /**
     *  Functions
     */

    public function hasShoppingCart(): bool
    {
        return $this->shoppingCart != null;
    }

    public function canCreateOrder(): bool
    {
        return ! $this->hasShoppingCart();
    }

    public function hasProcessingOrder(): bool
    {
        return $this->processingOrders()->get()->isNotEmpty();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_admin;
        }

        if ($panel->getId() === 'customer') {
            return true;
        }
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

    public function processingOrders(): HasMany
    {
        return $this->orders()->whereStatus(OrderStatus::PROCESSING);
    }

    public function shoppingCart(): HasOne
    {
        return $this->hasOne(Order::class)
            ->where('status', OrderStatus::ACTIVE)
            ->orWhere('status', OrderStatus::PROCESSING);
    }

    /**
     * Scopes
     */

    public function scopeCustomer(Builder $builder): Builder
    {
        return $builder->where('is_admin', false);
    }

    public function scopeNonActiveOrders(Builder $builder): Builder
    {
        return $builder->whereHas('orders', function (Builder $builder): void {
            $builder->whereIn('status', [OrderStatus::ACTIVE, OrderStatus::PROCESSING]);
        });
    }

    public function scopeWithNoWrongOrders(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('orders', function (Builder $builder): void {
            $builder->whereIn('status', [OrderStatus::ACTIVE, OrderStatus::PROCESSING]);
        });
    }
}
