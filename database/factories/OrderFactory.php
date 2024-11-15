<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function configure(): static
    {
        return $this->withRandomNumberOfProducts(1, 5);
    }

    public function withRandomNumberOfProducts(int $min, int $max)
    {
        return $this->afterCreating(function (Order $order) use ($min, $max) {
            $limit = random_int($min, $max);

            $products = Product::inRandomOrder()->limit($limit)->get()->mapWithKeys(function ($item) {
                return [
                    $item->id => [
                        'amount' => random_int(1, 10),
                        'price' => $item->price,
                    ],
                ];
            });

            $order->products()->attach($products);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first();

        return [
            // 'order_number' => rand(1, 5),
            'order_number' => function () {
                $i = random_int(1, 999999999);
                $preOrder = \Illuminate\Support\Str::padLeft($i, 9, 0);

                return 'ORD#'.$preOrder;
            },

            'customer_id' => $customer->id,
            'shipping_address_id' => $customer->addresses->random()->id,
            'invoice_address_id' => $customer->addresses->random()->id,
        ];
    }
}
