<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use App\Helpers\Money;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->order_number;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'order_number',
            'customer.user.name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Customer' => $record->customer->user->name,
            'Shipping Address' => Address::find($record->shipping_address_id)->street_name,
            'Invoice Address' => Address::find($record->invoice_address_id)->street_name,
            //TODO add products count and total price. Find out how to get to the pivot table from $record.
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('order_number')
                    ->placeholder('Will be automatically generated')
                    ->readOnly(),

                \Filament\Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->options(fn () => Customer::with('user')->get()->mapWithKeys(
                        fn (Customer $customer) => [$customer->id => $customer->user->name]
                    ))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        function (Set $set) {
                            $set('shipping_address_id', null);
                            $set('invoice_address_id', null);
                        }
                    ),

                \Filament\Forms\Components\Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->live()
                    ->options(function (Get $get, Set $set) {
                        self::getAddresses($get('customer_id'), $set);

                        return $get('addresses');
                    })
                    ->searchable()
                    ->required(),

                \Filament\Forms\Components\Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->live()
                    ->options(function (Get $get, Set $set) {
                        self::getAddresses($get('customer_id'), $set);

                        return $get('addresses');
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $savedAddresses = Address::pluck('street_name', 'id')->toArray();

        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('shipping_address_id')
                    ->label('Shipping address')
                    ->formatStateUsing(fn ($state) => self::getAddressesTable($state, $savedAddresses)),

                \Filament\Tables\Columns\TextColumn::make('invoice_address_id')
                    ->label('Invoice address')
                    ->formatStateUsing(fn ($state) => self::getAddressesTable($state, $savedAddresses)),

                \Filament\Tables\Columns\TextColumn::make('amount of products')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        $products = $record->products;
                        foreach ($products as $product) {
                            $count[] = $product->pivot->amount;
                        }

                        if (! isset($count)) {
                            return 'NOT FOUND';
                        }

                        $count = collect($count);

                        return new HtmlString($count->sum());
                    })
                    ->toggleable(),
                \Filament\Tables\Columns\TextColumn::make('Total price')
                    ->getStateUsing(function ($record) {
                        $products = $record->products;
                        foreach ($products as $product) {
                            $total[] = ($product->pivot->price) * ($product->pivot->amount);
                        }

                        if (! isset($total)) {
                            $total = 0;
                        }

                        $total ?? 'NOT FOUND';

                        $sum = collect($total)->sum();
                        $formatted = Money::format($sum);

                        return Money::prefix($formatted);
                    })
                    ->toggleable(),
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
