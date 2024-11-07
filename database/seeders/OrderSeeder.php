<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Order::factory(20)->create();

        // Order::get()->each(function ($order) {
        //     $products = Product::inRandomOrder()->limit(random_int(1, 3))->get()->map(function ($item) {
        //         return [
        //             'product_id' => $item->id,
        //             'amount' => random_int(1, 10),
        //             'price' => $item->price,
        //         ];
        //     });

        //     $order->products()->attach($products);
        // });
    }
}
