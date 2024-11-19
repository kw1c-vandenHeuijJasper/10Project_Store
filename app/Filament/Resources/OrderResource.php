<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

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
                    // ->options(Customer::pluck('user.name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive(),

                \Filament\Forms\Components\Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->options(function (callable $get) {
                        $customerId = $get('customer_id');
                        if ($customerId) {
                            return Address::where('customer_id', $customerId)->pluck('street_name', 'id');
                        }

                        return [];
                    })
                    ->searchable()
                    ->required(),

                \Filament\Forms\Components\Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->options(function (callable $get) {
                        $customerId = $get('customer_id');
                        if ($customerId) {
                            return Address::where('customer_id', $customerId)->pluck('street_name', 'id');
                        }

                        return [];
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('order_number'),
                \Filament\Tables\Columns\TextColumn::make('customer.user.name'),
                \Filament\Tables\Columns\TextColumn::make('shipping_address_id'),
                \Filament\Tables\Columns\TextColumn::make('invoice_address_id'),
                \Filament\Tables\Columns\TextColumn::make('amount of products')
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
}
