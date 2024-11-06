<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Adress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    public function adresses(): HasMany
    {
        return $this->hasMany(Adress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
