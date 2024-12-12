<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Helpers\Money;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static ?string $label = 'Order';

    //TODO icon
    protected static ?string $navigationIcon = 'icon-log-viewer';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereCustomerId(Customer::whereUserId(Auth::id())->first()?->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $orderProducts = OrderProduct::get();
        $addresses = Address::get();

        return $table
            ->columns([
                TextColumn::make('order_reference'),
                TextColumn::make('status'),
                TextColumn::make('shipping_address_id')
                    ->label('Shipping address')
                    ->formatStateUsing(function ($record) use ($addresses) {
                        $id = $record->shipping_address_id;
                        $address = $addresses->where('id', $id)->first();

                        return $address->street_name.' '
                            .$address->house_number.', '
                            .$address->city;
                    }),
                TextColumn::make('invoice_address_id')
                    ->label('Invoice address')
                    ->formatStateUsing(function ($record) use ($addresses) {
                        $id = $record->invoice_address_id;
                        $address = $addresses->where('id', $id)->first();

                        return $address->street_name.' '
                            .$address->house_number.', '
                            .$address->city;
                    }),
                TextColumn::make('amount of products')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $orderProducts->where('order_id', $record->id)
                        ->pluck('amount')->sum()),
                TextColumn::make('total')
                    ->getStateUsing(fn ($record) => Money::prefixFormat(
                        $orderProducts->where('order_id', $record->id)->pluck('total')->sum()
                    )),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create a new order!'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
