<?php

namespace App\Filament\Customer\Resources\OrderResource\RelationManagers;

use App\Helpers\Money;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->formatStateUsing(fn ($state): string => Money::prefixFormat($state)),
                Tables\Columns\TextInputColumn::make('amount')
                    ->rules(fn ($record): array => [
                        'between:1,'.$record->stock,
                        'integer',
                    ])
                    ->type('number')
                    ->width('10%'),
                Tables\Columns\TextColumn::make('total')
                    ->formatStateUsing(fn ($state): string => Money::prefixFormat($state)),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove from cart'),
            ]);
    }
}
