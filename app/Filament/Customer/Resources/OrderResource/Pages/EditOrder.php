<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Widgets\OrderStatsOverview;
use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string|HtmlString
    {
        return new HtmlString('Viewing your order: <br />'.$this->record->reference);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        if (collect($this->record)->contains(null)) {
            $is_disabled = true;
        } else {
            $is_disabled = false;
        }

        return [
            $this->actionMaker('Cancel Order', OrderStatus::CANCELLED, false),
            $this->actionMaker('Submit for Review', OrderStatus::PROCESSING, $is_disabled),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('back')
            ->action(fn () => redirect(OrderResource::getUrl()));
    }

    private function actionMaker(string $label, OrderStatus $newStatus, bool $is_disabled): Action
    {
        return Action::make($label)
            ->requiresConfirmation()
            ->disabled($is_disabled)
            ->action(function ($record) use ($newStatus) {
                $record->update(['status' => $newStatus]);

                return redirect(OrderResource::getUrl());
            });
    }
}
