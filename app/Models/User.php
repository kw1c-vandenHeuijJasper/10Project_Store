<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    /**
     * Attributes
     */
    public function hasShoppingCart(): Attribute
    {
        return Attribute::get(fn(): ?bool => $this->shoppingCart != null);
    }

    public function canCreateOrder(): Attribute
    {
        return Attribute::get(fn(): bool => !$this->hasShoppingCart);
    }

    public function hasProcessingOrder(): Attribute
    {
        return Attribute::get(fn(): bool => $this->processingOrders()->get()->isNotEmpty());
    }
}
