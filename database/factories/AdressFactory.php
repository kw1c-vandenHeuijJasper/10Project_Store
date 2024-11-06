<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adress>
 */
class AdressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'house_number' => rand(1, 80) . fake()->randomElement([null, 'a', 'b', 'c']),
            'street_name' => fake()->streetName(),
            'zip_code' => fake()->postcode(),
            'city' => fake()->city(),
            'customer_id' => \App\Models\Customer::get()->random()->pluck('id')[0],
            'primary' => false,
        ];
    }
}
