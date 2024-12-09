<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Makes sure that when generating stock does not end up below 0...
     */
    public function saving(Product $product): void
    {
        if ($product->stock <= 0) {
            $product->stock = 0;
        }
    }
}
