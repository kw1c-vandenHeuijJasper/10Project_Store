<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory(10)
            ->has(Address::factory(3))
            ->create();
    }
}
