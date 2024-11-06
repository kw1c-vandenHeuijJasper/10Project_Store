<?php

namespace Database\Seeders;

use App\Models\Adress;
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
        Customer::factory(10)->has(
            Adress::factory(3)
        );
    }
}
