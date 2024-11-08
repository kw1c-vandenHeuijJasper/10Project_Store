<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // dump(\App\Models\Customer::factory()->create());
        return [
            'house_number' => rand(1, 80).fake()->randomElement([null, 'a', 'b', 'c']),
            'street_name' => fake()->streetName(),
            'zip_code' => fake()->postcode(),
            'city' => fake()->city(),
            'customer_id' => \App\Models\Customer::factory(),
        ];
    }
}
