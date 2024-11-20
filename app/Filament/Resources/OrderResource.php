<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Order;
use App\Models\Address;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ProductsRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('order_number')
                    ->readOnly(),

                \Filament\Forms\Components\Select::make('customer_id')
                    ->relationship(name: 'customer.user', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(Get $get, Set $set) => $set(
                        'addresses',
                        \App\Models\Customer::whereId($get('customer_id'))?->first()?->addresses->pluck('street_name', 'id')
                    )),

                \Filament\Forms\Components\Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->live()
                    ->options(fn(callable $get) => $get('addresses'))
                    ->searchable()
                    ->required(),

                \Filament\Forms\Components\Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->live()
                    ->options(fn(callable $get) => $get('addresses'))
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
                \Filament\Tables\Columns\TextColumn::make('order_number'),
                \Filament\Tables\Columns\TextColumn::make('customer.user.name')
                    ->label('Customer'),

                \Filament\Tables\Columns\TextColumn::make('shipping_address_id')
                    ->label('Shipping address')
                    ->formatStateUsing(fn($state) => self::getAddresses($state, $savedAddresses)),

                \Filament\Tables\Columns\TextColumn::make('invoice_address_id')
                    ->label('Invoice address')
                    ->formatStateUsing(fn($state) => self::getAddresses($state, $savedAddresses)),

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
                    ->prefix('€')
                    ->getStateUsing(function ($record) {
                        $products = $record->products;
                        foreach ($products as $product) {
                            $total[] = ($product->pivot->price) * ($product->pivot->amount);
                        }
                        if (! isset($total)) {
                            return 'NOT FOUND';
                        }
                        $total = collect($total)->sum();

                        return ProductsRelationManager::moneyFormat($total);
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

    // TODO improve query count
    public static function getAddresses($state, $savedAddresses)
    {
        return $savedAddresses[$state] ?? 'NOT FOUND';
    }
}
