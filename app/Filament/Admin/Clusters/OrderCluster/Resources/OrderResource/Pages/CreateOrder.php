<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages;

use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
