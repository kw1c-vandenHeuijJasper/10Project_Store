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
    protected static ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 3;
    }

    private function statMoney($label, $input): Stat
    {
        return Stat::make('Total price', Money::HtmlString($input, true));
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
        //FIXME
        if ($totalAsInt == 0 ?? $customerCount == 0) {
            $averageSpend = 0;
        } elseif ($totalAsInt == 0) {
            $totalAsInt = 0;
        } elseif ($customerCount == 0) {
            $customerCount = 0;
        } else {
            $averageSpend = $totalAsInt / $customerCount;
        }

        return [
            $this->statMoney('total price', $total)
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

            $this->statMoney('Average spend', Money::format($averageSpend))
                ->description('per customer'),

            Stat::make('Amount of products', $productCount),
            Stat::make('Amount of orders', $orderCount),
            Stat::make('Amount of customers', $customerCount),
        ];
    }
}
