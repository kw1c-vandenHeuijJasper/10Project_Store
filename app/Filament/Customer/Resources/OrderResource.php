<?php

namespace App\Filament\Customer\Resources;

use App\Enums\OrderStatus;
use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Helpers\Money;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static ?string $label = 'Order';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    // protected static ?string $navigationIcon = 'icon-log-viewer';

    public static function getEloquentQuery(): Builder
    {
        //TODO group by status, date updated

        return parent::getEloquentQuery()
            ->whereCustomerId(Customer::whereUserId(Auth::id())->first()?->id);
    }

    public static function form(Form $form): Form
    {
        $customer = Customer::where('user_id', Auth::id())->first()?->id;
        $addresses = Address::whereCustomerId($customer)->get();
        $urlContainsCreate = Str::contains(URL::current(), 'create');

        return $form
            ->schema([
                TextInput::make('reference')
                    ->placeholder('Will be automatically generated')
                    ->visibleOn('edit')
                    ->readOnly(),

                Select::make('status')
                    ->native(false)
                    ->required()
                    ->label(function () use ($urlContainsCreate) {
                        if ($urlContainsCreate) {
                            return '';
                        } else {
                            return 'Status';
                        }
                    })
                    ->extraAttributes(
                        function () use ($urlContainsCreate) {
                            if ($urlContainsCreate) {
                                return ['style' => 'display:none'];
                            } else {
                                return ['class' => 'foo'];
                            }
                        }
                    )
                    ->options([OrderStatus::ACTIVE->value => OrderStatus::ACTIVE->getLabel()])
                    ->default(OrderStatus::ACTIVE->value),

                TextInput::make('customer_id')
                    ->label('')
                    ->columnSpan(2)
                    ->readOnly()
                    ->extraAttributes(['style' => 'display:none'])
                    ->default($customer),

                Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->options(function () use ($addresses) {
                        return $addresses->pluck('street_name', 'id');
                    })
                    ->searchable()
                    ->required(),

                Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->options(function () use ($addresses) {
                        return $addresses->pluck('street_name', 'id');
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $orderProducts = OrderProduct::get();
        $addresses = Address::get();

        return $table
            ->columns([
                TextColumn::make('reference'),
                TextColumn::make('status'),
                TextColumn::make('shipping_address_id')
                    ->label('Shipping address')
                    ->formatStateUsing(function ($record) use ($addresses) {
                        $id = $record->shipping_address_id;
                        $address = $addresses->where('id', $id)->first();

                        return $address->street_name . ' '
                            . $address->house_number . ', '
                            . $address->city;
                    }),
                TextColumn::make('invoice_address_id')
                    ->label('Invoice address')
                    ->formatStateUsing(function ($record) use ($addresses) {
                        $id = $record->invoice_address_id;
                        $address = $addresses->where('id', $id)->first();

                        return $address->street_name . ' '
                            . $address->house_number . ', '
                            . $address->city;
                    }),
                TextColumn::make('amount of products')
                    ->alignCenter()
                    ->getStateUsing(fn($record) => $orderProducts->where('order_id', $record->id)
                        ->pluck('amount')->sum()),
                TextColumn::make('total')
                    ->getStateUsing(fn($record) => Money::prefixFormat(
                        $orderProducts->where('order_id', $record->id)->pluck('total')->sum()
                    )),
            ])
            ->defaultGroup('status')
            ->defaultSort(fn($query) => $query->orderBy('updated_at', 'desc'))
            ->filters([
                SelectFilter::make('Status')
                    ->options(OrderStatus::class),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(function ($record) {
                        if ($record?->toArray() == Order::shoppingCart()?->toArray()) {
                            return false;
                        } else {
                            return true;
                        }
                    }),
            ])
            ->recordUrl(function ($record) {
                if ($record?->toArray() == Order::shoppingCart()?->toArray()) {
                    return 'orders/' . $record->id . '/edit';
                } else {
                    return null;
                }
            })
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
