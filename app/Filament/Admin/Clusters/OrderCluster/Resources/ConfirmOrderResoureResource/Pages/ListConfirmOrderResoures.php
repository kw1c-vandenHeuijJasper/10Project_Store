<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource\Pages;

use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfirmOrderResoures extends ListRecords
{
    protected static string $resource = ConfirmOrderResoureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
