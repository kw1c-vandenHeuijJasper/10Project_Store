<?php

namespace App\Filament\Customer\Resources\AddressResource\Pages;

use App\Filament\Customer\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAddress extends CreateRecord
{
    protected static string $resource = AddressResource::class;
}
