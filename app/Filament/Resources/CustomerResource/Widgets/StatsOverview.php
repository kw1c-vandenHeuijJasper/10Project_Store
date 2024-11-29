<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Filament\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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
                $total_price = $this->record->orders->map(fn ($order) => $order->products->sum('pivot.total'))->sum();

                return '€ '.ProductsRelationManager::moneyFormat($total_price);
            })
                ->description('of all orders combined'),
        ];
    }
}
