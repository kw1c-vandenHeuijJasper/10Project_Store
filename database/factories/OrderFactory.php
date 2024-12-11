<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
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

            $randomProducts = Product::inRandomOrder()
                ->limit($limit)
                ->get()
                ->map(fn($item) => $item)
                ->whereNotNull();
            $products = $randomProducts->mapWithKeys(function ($item) {
                return [
                    $item->id => [
                        'amount' => random_int(1, 10),
                        'price' => $item->price,
                    ],
                ];
            });

            $products = $products->map(fn($item) => $item ? $item : null);

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
        $customer = Customer::inRandomOrder()->where('id', '!=', 1)->first();

        return [
            'order_reference' => function () {
                $i = random_int(1, 999999999);
                $preOrder = \Illuminate\Support\Str::padLeft($i, 9, 0);

                return 'ORD#' . $preOrder;
            },
            'status' => fake()->randomElement(OrderStatus::cases()),
            'customer_id' => $customer->id,
            'shipping_address_id' => $customer->addresses->random()->id,
            'invoice_address_id' => $customer->addresses->random()->id,
        ];
    }
}
