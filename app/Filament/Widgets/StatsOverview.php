<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Helpers\Money;
use App\Models\Customer;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\HtmlString;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

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
                ->map(fn($pivot) => $pivot->amount * $pivot->price)
                ->sum()
        );
        (int)$totalAsInt = round(Money::toInteger($total));

        $orderCount = Order::count();
        $customerCount = Customer::count();
        $productCount = Product::count();

        return [
            Stat::make(
                'Total price',
                fn() => new HtmlString(
                    '<span style=color:lime;>' . Money::prefix() . '</span>' .
                        '<span style=color:lime;text-decoration:underline;>' . $total . '</span>'
                )
            )->description('of all orders combined'),
            Stat::make(
                'Average price',
                function () use ($total, $orderCount) {
                    (int) $divided = Money::toInteger($total) / $orderCount;
                    (int) $rounded = (int) round($divided);

                    (int) $formatted = (string) Money::format($rounded);

                    return new HtmlString(
                        '<span style=color:lime;>' . Money::prefix() . '</span>' .
                            '<span style=color:lime;text-decoration:underline;>' . $formatted . '</span>'
                    );
                }
            )->description('per order'),
            Stat::make(
                'Average spend',
                fn() => new HtmlString(
                    '<span style=color:lime;>' . Money::prefix() . '</span>' .
                        '<span style=color:lime;text-decoration:underline;>' .
                        Money::format($totalAsInt / $customerCount) . '</span>'
                )
            )->description('per customer'),
            Stat::make('Amount of products', $productCount),
            Stat::make('Amount of orders', $orderCount),
            Stat::make('Amount of customers', $customerCount),
        ];
    }
}
