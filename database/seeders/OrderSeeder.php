<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(250)->create();

        $this->changeStatusToActive();
    }

    /**
     * Changes 20% of all orders created to OrderStatus::ACTIVE
     */
    public function changeStatusToActive()
    {
        Order::whereStatus(OrderStatus::FINISHED)
            ->get()
            ->map(function ($order) {
                if (random_int(0, 4) == 4) {
                    $order->update(['status' => OrderStatus::ACTIVE]);
                }
            });
    }
}
