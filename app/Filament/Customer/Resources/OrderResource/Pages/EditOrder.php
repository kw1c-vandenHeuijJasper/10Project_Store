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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['status'] = OrderStatus::ACTIVE;

        return $data;
    }

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
            $this->actionMaker('Cancel Order', OrderStatus::CANCELLED),
            $this->actionMaker('Submit for Review', OrderStatus::PROCESSING),
        ];
    }

    private function actionMaker(string $label, OrderStatus $newStatus): Action
    {
        return Action::make($label)
            ->requiresConfirmation()
            ->action(function ($record) use ($newStatus) {
                $record->update(['status' => $newStatus]);

                return redirect(OrderResource::getUrl());
            });
    }
}
