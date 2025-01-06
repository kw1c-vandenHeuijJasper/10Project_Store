<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResource\Pages;

use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfirmOrderResources extends ListRecords
{
    protected static string $resource = ConfirmOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
