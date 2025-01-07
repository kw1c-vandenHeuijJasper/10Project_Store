<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Customer\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = OrderStatus::ACTIVE;

        return $data;
    }
}
