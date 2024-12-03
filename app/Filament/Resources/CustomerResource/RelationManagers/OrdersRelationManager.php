<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Order;
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
        return \App\Filament\Resources\OrderResource::table($table)
            ->actions(
                \Filament\Tables\Actions\ActionGroup::make([
                    \Filament\Tables\Actions\Action::make('Order')
                        ->url(fn (Order $record) => \App\Filament\Resources\OrderResource::getUrl().'/'.$record->id.'/edit'),
                    \Filament\Tables\Actions\Action::make('Order in new tab')
                        ->url(fn (Order $record) => \App\Filament\Resources\OrderResource::getUrl().'/'.$record->id.'/edit')
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
