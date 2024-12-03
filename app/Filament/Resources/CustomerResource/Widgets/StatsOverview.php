<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Helpers\Money;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    public ?\Illuminate\Database\Eloquent\Model $record = null;

    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    public function getStats(): array
    {
        return [
            Stat::make('Total price', function () {
                $total_price = $this->record->orders->map(
                    fn ($order) => $order->products->sum('pivot.total')
                )->sum();

                return new HtmlString(
                    '<span style=color:lime;>'.
                        Money::prefix().
                        '</span>'.
                        '<span style=color:lime;text-decoration:underline;>'.
                        Money::format($total_price).
                        '</span>'
                );
            })
                ->description('of all orders combined'),
        ];
    }
}
