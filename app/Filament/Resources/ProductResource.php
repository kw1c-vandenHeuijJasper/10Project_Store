<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Helpers\Money;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    //TODO choose icon
    // protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required(),
                \Filament\Forms\Components\Textarea::make('description')
                    ->required(),
                \Filament\Forms\Components\Placeholder::make('Price Guide')
                    ->label('Price Guide')
                    ->content(new HtmlString('Prices are saved as an integer, so 72 is 0,72')),
                \Filament\Forms\Components\TextInput::make('price')
                    ->integer()
                    ->prefix('€')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('stock')
                    ->minValue(0)
                    ->integer()
                    ->required(),
                //TODO select and enum
                \Filament\Forms\Components\TextInput::make('type')
                    ->placeholder('Example: Laptop, Keyboard, Shipping Container...')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->limit(25),
                \Filament\Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(
                        fn($state) => Money::prefix(Money::format($state))
                    )
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('stock'),

                //TODO enum and selectColumn
                \Filament\Tables\Columns\TextColumn::make('type'),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
