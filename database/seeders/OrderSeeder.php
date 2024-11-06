<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(25)->create();


        $collection = Product::inRandomOrder()->limit(55)->get()->map(function ($item) {
            return [
                'product_id' => $item->id,
                'amount' => random_int(1, 10),
                'price' => $item->price,
            ];
        });

        Order::first()->products()->attach($collection);
    }
}
