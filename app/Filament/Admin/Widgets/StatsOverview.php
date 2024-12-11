<?php

namespace App\Filament\Admin\Widgets;

use App\Helpers\Money;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 3;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $total = Money::format(
            OrderProduct::get()
                ->pluck('total')
                ->sum()
        );

        $totalAsInt = Money::toInteger($total);

        $orderCount = Order::count();
        $customerCount = Customer::count();
        $productCount = Product::count();

        return [
            Stat::make('Total price', Money::HtmlString($total, true))
                ->description('of all orders combined'),

            Stat::make(
                'Average price',
                function () use ($totalAsInt, $orderCount) {
                    if ($totalAsInt == 0 && $orderCount == 0) {
                        $formatted = Money::format(0);
                        $divided = 0;
                    } else {
                        $divided = Money::toInteger($totalAsInt) / $orderCount;
                    }
                    $formatted = (string) Money::format($divided);

                    if ($divided == 0) {
                        $formatted = Money::format(0);
                    }

                    return Money::HtmlString($formatted, true);
                }
            )->description('per order'),

            Stat::make(
                'Average spend',
                Money::HtmlString(Money::format($totalAsInt / $customerCount), true)
            )->description('per customer'),

            Stat::make('Amount of products', $productCount),
            Stat::make('Amount of orders', $orderCount),
            Stat::make('Amount of customers', $customerCount),
        ];
    }
}
