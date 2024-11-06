<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // dd(Customer::get());

        return [
            'order_number' => rand(1, 5),
            //pick a random EXISTING customer id
            'customer_id' => Customer::inRandomOrder()->get()->first()->id,
        ];
    }
}
