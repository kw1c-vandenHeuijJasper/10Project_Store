<?php

namespace App\Filament\Widgets;

use App\Helpers\Money;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

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

        $orderCount = Order::count();
        return [
            Stat::make(
                'Total price',
                fn() => new HtmlString(
                    '<span style=color:lime;>' . Money::prefix() . '</span>' .
                        '<span style=color:lime;text-decoration:underline;>' . $total . '</span>'
                )
            )->description('of all orders combined'),
            Stat::make('Amount of orders', $orderCount),
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
        ];
    }
}
