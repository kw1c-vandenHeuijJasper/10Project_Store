<?php

namespace App\Filament\Widgets;

use App\Helpers\Money;
use App\Models\OrderProduct;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $money = Money::format(
            OrderProduct::get()
                ->map(fn($pivot) => $pivot->amount * $pivot->price)
                ->sum()
        );
        return [
            Stat::make(
                'Total price',
                fn() => new HtmlString(
                    '<span style=color:lime;>' . Money::prefix() . '</span>' .
                        '<span style=color:lime;text-decoration:underline;>' . $money . '</span>'
                )
            )->description('of all orders combined')
        ];
    }
}
