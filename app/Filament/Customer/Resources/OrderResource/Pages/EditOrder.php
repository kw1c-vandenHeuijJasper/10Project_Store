<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Widgets\OrderStatsOverview;
use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string|HtmlString
    {
        return new HtmlString('Viewing your order: '.'<br />'.$this->record->reference);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Cancel Order')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update(['status' => OrderStatus::CANCELLED]);

                    return redirect(OrderResource::getUrl());
                }),
        ];
    }
}
