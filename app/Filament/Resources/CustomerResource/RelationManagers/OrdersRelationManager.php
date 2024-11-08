<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return \App\Filament\Resources\OrderResource::form($form);
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\OrderResource::table($table);
    }
}
