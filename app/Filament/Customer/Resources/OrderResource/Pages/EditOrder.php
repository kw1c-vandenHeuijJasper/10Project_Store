<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\HtmlString;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Customer\Resources\OrderResource;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Widgets\OrderStatsOverview;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string|HtmlString
    {
        return new HtmlString('Viewing your order: ' . '<br />' . $this->record->reference);
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
            Actions\DeleteAction::make(),
        ];
    }
}
