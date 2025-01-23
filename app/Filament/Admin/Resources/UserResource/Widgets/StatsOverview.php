<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Helpers\Money;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total price', function (): HtmlString {
                $total_price = $this->record->orders->map(
                    fn($order) => $order->products->sum('pivot.total')
                )->sum();

                return Money::HtmlString(Money::format($total_price), true);
            })->description('of all orders combined'),
        ];
    }
}
