<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use App\Filament\Admin\Resources\CustomerResource;
use App\Helpers\Money;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
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
            'customer.user.name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Customer' => $record->customer->user->name,
            'Shipping Address' => Address::find($record->shipping_address_id)->street_name,
            'Invoice Address' => Address::find($record->invoice_address_id)->street_name,
            'Total Price' => Money::prefixFormat(OrderProduct::whereOrderId($record->id)->pluck('total')->sum()),
            'Product Count' => OrderProduct::whereOrderId($record->id)->pluck('amount')->sum(),
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

                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->hint('with no active/processing orders. If number customer has active order')
                    ->options(fn() => Customer::with('user')->withNoWrongOrders()->get()->mapWithKeys(
                        fn(Customer $customer) => [$customer->id => $customer->user->name]
                    ))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        function (Set $set) {
                            $set('shipping_address_id', null);
                            $set('invoice_address_id', null);
                        }
                    )
                    ->suffix('Go to customer')
                    ->suffixActions([
                        Forms\Components\Actions\Action::make('here')
                            ->label('here')
                            ->icon('heroicon-o-arrow-right')
                            ->color('primary')
                            ->url(fn(Get $get) => CustomerResource::getUrl() . '/' . $get('customer_id') . '/edit'),

                        Forms\Components\Actions\Action::make('new tab')
                            ->label('in new tab')
                            ->icon('heroicon-o-arrow-right-circle')
                            ->color('success')
                            ->url(fn(Get $get) => CustomerResource::getUrl() . '/' . $get('customer_id') . '/edit')
                            ->openUrlInNewTab(),
                    ])
                    ->columnSpan(2),

                Forms\Components\Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->live()
                    ->options(function (Get $get, Set $set) {
                        self::getAddresses($get('customer_id'), $set);

                        return $get('addresses');
                    })
                    ->searchable()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->live()
                    ->options(function (Get $get, Set $set) {
                        self::getAddresses($get('customer_id'), $set);

                        return $get('addresses');
                    })
                    ->searchable()
                    ->required()
                    ->columnSpan(2),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        $savedAddresses = Address::pluck('street_name', 'id')->toArray();
        $orderProduct = OrderProduct::get();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('shipping_address_id')
                    ->label('Shipping address')
                    ->formatStateUsing(
                        fn($state) => self::getAddressesTable($state, $savedAddresses)
                    ),
                Tables\Columns\TextColumn::make('invoice_address_id')
                    ->label('Invoice address')
                    ->formatStateUsing(
                        fn($state) => self::getAddressesTable($state, $savedAddresses)
                    ),
                Tables\Columns\TextColumn::make('amount of products')
                    ->alignCenter()
                    ->getStateUsing(fn($record) => $orderProduct
                        ->where('order_id', $record->id)->pluck('amount')->sum())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(fn($record) => Money::prefixFormat(
                        $orderProduct->where('order_id', $record->id)->pluck('total')->sum()
                    ))
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
                    ->modifyFormFieldUsing(fn(Toggle $field) => $field->inline(false))
                    ->query(fn(Builder $query) => $query->has('products')),

                Tables\Filters\Filter::make('no_products')
                    ->default(false)
                    ->label('No product(s)')
                    ->toggle()
                    ->modifyFormFieldUsing(fn(Toggle $field) => $field->inline(false))
                    ->query(fn(Builder $query) => $query->doesntHave('products')),

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

    public static function getAddresses($state, Set $set)
    {
        $addresses = Address::where('customer_id', $state)->pluck('street_name', 'id');
        $set('addresses', $addresses);
    }

    public static function getAddressesTable($state, $savedAddresses)
    {
        return $savedAddresses[$state] ?? null;
    }
}
