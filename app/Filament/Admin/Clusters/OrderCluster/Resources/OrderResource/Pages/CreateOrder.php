<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
