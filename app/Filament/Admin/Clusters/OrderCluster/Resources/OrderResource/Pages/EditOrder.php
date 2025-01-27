<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages;

use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResource\Pages\ViewConfirmOrderResource;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Widgets\OrderStatsOverview;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Go to processing order')
                ->visible(fn(Order $record): bool => $record->user->hasProcessingOrder)
                ->url(fn(Order $record): string => ViewConfirmOrderResource::getUrl([$record])),
            Actions\DeleteAction::make(),
        ];
    }
}
