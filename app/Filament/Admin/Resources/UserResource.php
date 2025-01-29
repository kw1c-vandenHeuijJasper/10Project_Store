<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers\AddressRelationManager;
use App\Filament\Admin\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    // protected static ?string $recordTitleAttribute = 'reference';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
            'phone_number',
            'date_of_birth',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
            'Phone Number' => $record->phone_number,
            'Date Of Birth' => $record->date_of_birth,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),
                \Filament\Forms\Components\Toggle::make('is_admin')
                    ->inline(false)
                    ->offIcon('heroicon-m-x-circle')
                    ->onIcon('heroicon-m-star')
                    ->onColor('success')
                    ->offColor('danger')
                    ->label('Is Admin')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->required(),
                \Filament\Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('date_of_birth')
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
                \Filament\Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                \Filament\Tables\Columns\ToggleColumn::make('is_admin')
                    ->label('Admin')
                    ->onIcon('heroicon-s-star')
                    ->offIcon('heroicon-s-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(),
                \Filament\Tables\Columns\TextColumn::make('password')
                    ->label('Password')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('orders_count')
                    ->label('Amount of orders')
                    // ->getStateUsing(function ($record) {
                    //     dd($record->orders);
                    // })
                    ->counts('orders')
                    ->alignCenter(),
                \Filament\Tables\Columns\TextColumn::make('date_of_birth')
                    ->date('d-m-Y')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // \Filament\Tables\Filters\Filter::make('is_admin')
                //     ->default(false)
                //     ->label('Is admin')
                //     ->toggle()
                //     ->modifyFormFieldUsing(fn(Toggle $field) => $field->inline(false))
                //     ->query(fn(Builder $query) => $query->where('is_admin', true)),

                \Filament\Tables\Filters\Filter::make('has_orders')
                    ->default(false)
                    ->label('Has Orders')
                    ->toggle()
                    ->modifyFormFieldUsing(fn (Toggle $field) => $field->inline(false))
                    ->query(fn (Builder $query) => $query->has('orders')),

            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
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
            AddressRelationManager::class,
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\UserResource\Widgets\StatsOverview::class,
        ];
    }

    public static function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\UserResource\Widgets\StatsOverview::class,
        ];
    }
}
