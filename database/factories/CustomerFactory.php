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
        return [
            'name' => 'John Doe',
            'phone_number' => '001',
            'email' => 'john@doe.com',
            'password' => 'SecurePassword123',
            'date_of_birth' => '01-01-0001',
        ];
    }
}
