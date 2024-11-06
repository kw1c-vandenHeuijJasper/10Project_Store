<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdressResource\Pages;
use App\Filament\Resources\AdressResource\RelationManagers;
use App\Models\Adress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdressResource extends Resource
{
    protected static ?string $model = Adress::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('house_number')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('street_name')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('zip_code')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('city')
                    ->required(),
                \Filament\Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->searchable()
                    ->options(\App\Models\Customer::pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('house_number'),
                \Filament\Tables\Columns\TextColumn::make('street_name'),
                \Filament\Tables\Columns\TextColumn::make('zip_code'),
                \Filament\Tables\Columns\TextColumn::make('city'),
                \Filament\Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer')
                    ->formatStateUsing(fn(\App\Models\Customer $customer) => $customer->pluck('name')[0]),
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
            'index' => Pages\ListAdresses::route('/'),
            'create' => Pages\CreateAdress::route('/create'),
            'edit' => Pages\EditAdress::route('/{record}/edit'),
        ];
    }
}
