<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
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
            'id' => 1,
            'name' => 'ADMIN',
            'password' => 'ADMIN',
            'email' => 'test@test.test',
            'is_admin' => true,
        ]);

        /**
         * Fake customer data
         */
        User::factory()
            ->has(Address::factory(rand(1, 3)))
            ->has(Order::factory(3))
            ->create([
                'id' => 2,
                'name' => 'John Doe',
                'password' => 'Password',
                'email' => 'john@doe.com',
                'is_admin' => false,
            ]);
        Product::factory(3)->create();

        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        User::with('activeOrders')
            ->get()
            ->filter(fn (User $user) => $user->activeOrders->count() > 1)
            ->each(function (User $user) {
                $orderIds = $user->activeOrders
                    ->reject(fn (Order $order) => $order == $user->activeOrders->last())
                    ->pluck('id');

                Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::CANCELLED]);
            });
    }
}
