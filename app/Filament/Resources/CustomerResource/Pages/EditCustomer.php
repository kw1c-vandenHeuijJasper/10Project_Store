<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\Widgets\StatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int
    {
        return 1;
    }

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
