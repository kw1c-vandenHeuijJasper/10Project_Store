<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomerResource\Pages;
use App\Filament\Admin\Resources\CustomerResource\RelationManagers\AddressRelationManager;
use App\Filament\Admin\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use App\Models\User;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->user->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'user.name',
            'user.email',
            'phone_number',
            'date_of_birth',

        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->user->email,
            'Phone Number' => $record->phone_number,
            'Date Of Birth' => $record->date_of_birth,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('user_id')
                    ->createOptionForm(function () {
                        return [
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
                                ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                                ->required(),
                        ];
                    })
                    ->createOptionUsing(function ($data) {
                        return User::create($data);
                    })
                    ->editOptionForm(function () {
                        return [
                            \Filament\Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->required(),
                            \Filament\Forms\Components\Toggle::make('is_admin')
                                ->inline(false)
                                ->onIcon('heroicon-m-star')
                                ->offIcon('heroicon-m-x-circle')
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
                                ->revealable()
                                ->password()
                                ->placeholder('Confirm password or make a new one to confirm edit')
                                ->required(),
                        ];
                    })
                    ->unique(ignoreRecord: true)
                    ->label('Connected user')
                    ->searchable()
                    ->preload()
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->required(),

                \Filament\Forms\Components\Toggle::make('isAdmin')
                    ->inline(false)
                    ->formatStateUsing(function (Get $get): bool {
                        return (bool) User::find($get('user_id'))?->is_admin == true ? true : false;
                    })
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(),
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
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label('Name'),
                \Filament\Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->label('Email'),
                \Filament\Tables\Columns\ToggleColumn::make('user.is_admin')
                    ->label('Admin')
                    ->onIcon('heroicon-s-star')
                    ->offIcon('heroicon-s-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(),
                \Filament\Tables\Columns\TextColumn::make('user.password')
                    ->label('Password')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('orders_count')
                    ->label('Amount of orders')
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
                \Filament\Tables\Filters\Filter::make('user.is_admin')
                    ->default(false)
                    ->label('Is admin')
                    ->toggle()
                    ->modifyFormFieldUsing(fn(Toggle $field) => $field->inline(false))
                    ->query(fn(Builder $query) => $query->whereRelation('user', 'is_admin', true)),

                \Filament\Tables\Filters\Filter::make('has_orders')
                    ->default(false)
                    ->label('Has Orders')
                    ->toggle()
                    ->modifyFormFieldUsing(fn(Toggle $field) => $field->inline(false))
                    ->query(fn(Builder $query) => $query->has('orders')),

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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\CustomerResource\Widgets\StatsOverview::class,
        ];
    }

    public static function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\CustomerResource\Widgets\StatsOverview::class,
        ];
    }
}
