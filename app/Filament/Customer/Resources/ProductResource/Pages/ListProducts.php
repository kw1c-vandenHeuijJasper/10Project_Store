<?php

namespace App\Filament\Customer\Resources\ProductResource\Pages;

use App\Filament\Customer\Resources\ProductResource;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
}
