<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Helpers\Money;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $cluster = OrderCluster::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->reference;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'reference',
            'name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Customer' => $record->user->name,
            'Shipping Address' => $record->shippingAddress->street_name,
            'Invoice Address' => $record->invoiceAddress->street_name,
            'Total Price' => Money::prefixFormat($record->pivot->sum('total')),
            'Product Count' => $record->pivot->sum('amount'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->placeholder('Will be automatically generated')
                    ->readOnly()
                    ->columnSpan(1),

                Forms\Components\Select::make('status')
                    ->native(false)
                    ->required()
                    ->options(OrderStatus::class)
                    ->columnSpan(1),

                Forms\Components\Select::make('user_id')
                    ->label('Customer')
                    ->hint("If user has an active order, this is a number. Nothing's wrong!")
                    ->options(fn () => User::customer()->withNoWrongOrders()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('shipping_address_id', null);
                        $set('invoice_address_id', null);
                    })
                    ->suffix('Go to customer')
                    ->suffixActions([
                        Forms\Components\Actions\Action::make('here')
                            ->label('here')
                            ->icon('heroicon-o-arrow-right')
                            ->color('primary')
                            ->url(fn (Get $get) => $get('user_id') ? EditUser::getUrl([$get('user_id')]) : null),

                        Forms\Components\Actions\Action::make('new tab')
                            ->label('in new tab')
                            ->icon('heroicon-o-arrow-right-circle')
                            ->color('success')
                            ->url(fn (Get $get) => $get('user_id') ? EditUser::getUrl([$get('user_id')]) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columnSpan(2),

                Forms\Components\Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->live()
                    ->options(function (Get $get, Set $set): Collection {
                        self::getAddresses($get('user_id'), $set);

                        return $get('addresses');
                    })
                    ->searchable()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->live()
                    ->options(fn (Get $get) => $get('addresses'))
                    ->searchable()
                    ->required()
                    ->columnSpan(2),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('shippingAddress.street_name')
                    ->label('Shipping address'),
                Tables\Columns\TextColumn::make('invoiceAddress.street_name')
                    ->label('Invoice address'),
                Tables\Columns\TextColumn::make('pivot_sum_amount')
                    ->label('Amount of products')
                    ->alignCenter()
                    ->sum('pivot', 'amount')
                    ->default(0)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pivot_sum_total')
                    ->label('Total')
                    ->sum('pivot', 'total')
                    ->default(0)
                    ->formatStateUsing(fn (int $state) => Money::prefixFormat($state))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('has_products')
                    ->default(false)
                    ->label('Has product(s)')
                    ->toggle()
                    ->modifyFormFieldUsing(fn (Toggle $field): Toggle => $field->inline(false))
                    ->query(fn (Builder $query): Builder => $query->has('products')),

                Tables\Filters\Filter::make('no_products')
                    ->default(false)
                    ->label('No product(s)')
                    ->toggle()
                    ->modifyFormFieldUsing(fn (Toggle $field): Toggle => $field->inline(false))
                    ->query(fn (Builder $query): Builder => $query->doesntHave('products')),

                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::class)
                    ->native(false),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getAddresses(?int $state, Set $set): void
    {
        $addresses = Address::where('user_id', $state)->pluck('street_name', 'id') ?? collect();
        $set('addresses', $addresses);
    }
}
