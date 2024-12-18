<?php

namespace App\Filament\Admin\Resources\CustomerResource\RelationManagers;

use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return OrderResource::form($form);
    }

    public function table(Table $table): Table
    {
        return OrderResource::table($table)
            ->actions(
                \Filament\Tables\Actions\ActionGroup::make([
                    \Filament\Tables\Actions\Action::make('Order')
                        ->url(fn(Order $record) => OrderResource::getUrl() . '/' . $record->id . '/edit'),
                    \Filament\Tables\Actions\Action::make('Order in new tab')
                        ->url(fn(Order $record) => OrderResource::getUrl() . '/' . $record->id . '/edit')
                        ->openUrlInNewTab(),
                ])
                    ->label('Go to')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->size(\Filament\Support\Enums\ActionSize::Medium)
                    ->color('info')
                    ->button()
            )
            ->recordAction(null);
    }
}
