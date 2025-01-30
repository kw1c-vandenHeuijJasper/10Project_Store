<?php

namespace App\Filament\Customer\Resources\OrderResource\RelationManagers;

use Filament\Tables;
use App\Helpers\Money;
use Filament\Tables\Table;
use App\Filament\Customer\Resources\ProductResource;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(15),
                Tables\Columns\TextColumn::make('stock'),
                Tables\Columns\TextColumn::make('pivot.price')
                    ->label('Price')
                    ->formatStateUsing(fn($state): string => Money::prefixFormat($state)),
                Tables\Columns\TextInputColumn::make('amount')
                    ->rules(fn($record): array => [
                        'between:1,' . $record->stock,
                        'integer',
                    ])
                    ->type('number')
                    ->width('10%'),
                Tables\Columns\TextColumn::make('total')
                    ->formatStateUsing(fn($state): string => Money::prefixFormat($state)),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove from cart'),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make("no products in cart")
                    ->label("You don't seem to have any products in your cart. Click here to go to the products page.")
                    ->color('info')
                    ->outlined()
                    ->visible(fn() => $this->ownerRecord->pivot->isEmpty())
                    ->url(fn() => ProductResource::getUrl())
            ]);
    }
}
