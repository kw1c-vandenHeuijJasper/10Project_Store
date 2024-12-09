<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Helpers\Money;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class StatsOverview extends BaseWidget
{
    public ?Model $record = null;

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

                return Money::HtmlString(Money::format($total_price), true);
            })->description('of all orders combined'),
        ];
    }
}
