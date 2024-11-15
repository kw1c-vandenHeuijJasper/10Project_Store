<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required(),
                // TODO
                \Filament\Forms\Components\Textarea::make('description')
                    ->required(),
                \Filament\Forms\Components\Placeholder::make('Price Guide Placeholder')
                    ->label('Price Guide')
                    ->content(new HtmlString(
                        '<div style="background-color:grey">
                            The price system is an integer, so input 7 = 0,07 in decimal. <br>
                            input 701 = 7,01 in decimal
                        </div>'
                    )),
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
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('name'),
                \Filament\Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(function ($state) {
                        (string) $input = $state;

                        $input = str($input)->remove(' ')->toString();
                        (string) $parttwo = substr($input, -2);

                        if ($input[0] === '0') {
                            $trimmed_input = ltrim($input, '0');
                        } else {
                            $trimmed_input = $input;
                        }
                        $partone = Str::of($trimmed_input)->chopEnd($parttwo);
                        if ($partone == '' || $partone == $input) {
                            $partone = '0';
                        }
                        $output = $partone.','.$parttwo;
                        if (strlen($input) == 1) {
                            $output = '0,0'.$input;
                        }

                        return $output;
                    })
                    ->prefix('€')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->limit(25),
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
