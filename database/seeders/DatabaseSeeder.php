<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Address;
use App\Models\Customer;
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
            'name' => 'ADMIN',
            'password' => 'ADMIN',
            'email' => 'test@test.test',
            'is_admin' => true,
        ]);

        /**
         * Fake customer data
         */
        User::factory()->create([
            'name' => 'John Doe',
            'password' => 'Password',
            'email' => 'john@doe.com',
            'is_admin' => false,
        ]);
        Product::factory(3)->create();
        Customer::factory()
            ->has(Address::factory())
            ->has(Order::factory(3))
            ->create([
                'user_id' => 2,
            ]);

        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        Customer::with('activeOrders')
            ->get()
            ->filter(fn (Customer $customer) => $customer->activeOrders->count() > 1)
            ->each(function (Customer $customer) {
                $orderIds = $customer->activeOrders
                    ->reject(fn (Order $order) => $order == $customer->activeOrders->last())
                    ->pluck('id');

                Order::whereIn('id', $orderIds)->update(['status' => OrderStatus::CANCELLED]);
            });
    }
}
