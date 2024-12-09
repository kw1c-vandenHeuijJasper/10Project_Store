<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    //TODO DELETE
    /**
     * Handle the Product "created" event.
     */
    public function saving(Product $product): void
    {
        if ($product->stock <= 0) {
            $product->stock = 0;
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
