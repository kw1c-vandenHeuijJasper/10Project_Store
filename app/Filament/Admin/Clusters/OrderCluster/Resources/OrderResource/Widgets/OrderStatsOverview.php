<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Widgets;

use App\Helpers\Money;
use App\Models\OrderProduct;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class OrderStatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected int|string|array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $orderProduct = OrderProduct::get();
        $thisOrder = $orderProduct->where('order_id', $this->record->id);

        return [
            Stat::make('Total Price', function () use ($thisOrder) {
                return Money::HtmlString(
                    Money::format($thisOrder->sum('total')),
                    true
                );
            }),
            Stat::make('Product Count', $thisOrder->sum('amount')),
        ];
    }
}
