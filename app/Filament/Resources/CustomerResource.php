<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers\AddressRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

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
                                ->onIcon('heroicon-m-x-circle')
                                ->offIcon('heroicon-m-star')
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
                                ->dehydrateStateUsing(fn (string $state): string => \Illuminate\Support\Facades\Hash::make($state))
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
                                ->onIcon('heroicon-m-x-circle')
                                ->offIcon('heroicon-m-star')
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
                                ->placeholder('Confirm password or make a new one to confirm edit')
                                ->password()
                                ->dehydrateStateUsing(fn (string $state): string => \Illuminate\Support\Facades\Hash::make($state))
                                ->required(),
                        ];
                    })
                    ->unique()
                    ->label('Connected user')
                    ->searchable()
                    ->preload()
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->required(),

                // TODO maybe use toggle because this is the admin panel, and only admins should be allowed here
                \Filament\Forms\Components\Placeholder::make('isAdmin')
                    ->content(
                        function (\Filament\Forms\Get $get) {
                            if (! isset(User::whereId($get('user_id'))->first()->is_admin)) {
                                $isAdmin = 'False';
                            } else {
                                $isAdmin = User::whereId($get('user_id'))->first()->is_admin;
                                if ($isAdmin) {
                                    $isAdmin = 'True';
                                } else {
                                    $isAdmin = 'False';
                                }
                            }

                            return new HtmlString($isAdmin);
                        }
                    ),
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
                    ->label('Name'),
                \Filament\Tables\Columns\TextColumn::make('phone_number'),
                \Filament\Tables\Columns\TextColumn::make('user.email')
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
                \Filament\Tables\Columns\TextColumn::make('date_of_birth')
                    ->date('d-m-Y'),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('user.is_admin')
                    ->label('Is admin')
                    ->toggle()
                    ->query(function (Builder $query) {
                        return $query->whereRelation('user', 'is_admin', '=', true);
                    }),
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
            \App\Filament\Resources\CustomerResource\Widgets\StatsOverview::class,
        ];
    }

    public static function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\CustomerResource\Widgets\StatsOverview::class,
        ];
    }
}
