<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //TODO password on all customer things
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'password' => \Illuminate\Support\Facades\Hash::make(fake()->password()),
            'date_of_birth' => fake()->date('Y-m-d', '31-12-2010'),
        ];
    }
}
