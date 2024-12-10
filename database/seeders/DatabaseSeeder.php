<?php

namespace Database\Seeders;

use App\Models\Customer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'ADMIN',
            'password' => 'ADMIN',
            'email' => 'test@test.test',
            'is_admin' => true,
        ]);
        User::factory()->create([
            'name' => 'John Doe',
            'password' => 'Password',
            'email' => 'john@doe.com',
            'is_admin' => false,
        ]);
        Customer::factory()->create([
            'user_id' => 2,
        ]);

        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
