<?php

namespace App\Filament\Customer\Resources;

use App\Enums\OrderStatus;
use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Filament\Customer\Resources\OrderResource\RelationManagers\ProductsRelationManager;
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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static ?string $label = 'Order';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static ?string $navigationGroup = 'Orders';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereCustomerId(Auth::user()?->customer?->id);
    }

    public static function form(Form $form): Form
    {
        $customer = Customer::where('user_id', Auth::id())->first()?->id;
        $addresses = Address::whereCustomerId($customer)->get();

        return $form
            ->schema([
                TextInput::make('reference')
                    ->placeholder('Will be automatically generated')
                    ->visibleOn('edit')
                    ->readOnly(),

                Select::make('status')
                    ->native(false)
                    ->required()
                    ->hiddenOn('create')
                    ->options([OrderStatus::ACTIVE->value => OrderStatus::ACTIVE->getLabel()]),

                TextInput::make('customer_id')
                    ->label('')
                    ->columnSpan(2)
                    ->readOnly()
                    ->extraAttributes(['style' => 'display:none'])
                    ->default($customer),

                Select::make('shipping_address_id')
                    ->label('Shipping Address')
                    ->options(fn () => $addresses->pluck('street_name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('invoice_address_id')
                    ->label('Invoice Address')
                    ->options(fn () => $addresses->pluck('street_name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $addresses = Address::where('customer_id', Auth::user()?->customer?->id)->get();

        return $table
            ->columns([
                TextColumn::make('reference'),
                TextColumn::make('status'),
                TextColumn::make('shipping_address_id')
                    ->label('Shipping address')
                    ->limit(25)
                    ->formatStateUsing(function ($record) use ($addresses) {
                        $address = $addresses->find($record->shipping_address_id);

                        return "{$address->street_name} {$address->house_number}, 
                        {$address->city}, {$address->zip_code}";
                    }),
                TextColumn::make('invoice_address_id')
                    ->label('Invoice address')
                    ->limit(25)
                    ->formatStateUsing(function ($record) use ($addresses) {
                        $address = $addresses->find($record->invoice_address_id);

                        return "{$address->street_name} {$address->house_number}, 
                        {$address->city}, {$address->zip_code}";
                    }),
                TextColumn::make('amount of products')
                    ->alignCenter()
                    ->getStateUsing(
                        fn ($record) => OrderProduct::where('order_id', $record->id)
                            ->pluck('amount')
                            ->sum()
                    ),
                TextColumn::make('total')
                    ->getStateUsing(fn ($record) => Money::prefixFormat(
                        OrderProduct::where('order_id', $record->id)
                            ->pluck('total')
                            ->sum()
                    )),
            ])
            ->defaultGroup('status')
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('Status')
                    ->options(OrderStatus::class)
                    ->multiple()
                    ->default([OrderStatus::ACTIVE->value, OrderStatus::PROCESSING->value]),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(function ($record) {
                        if ($record?->toArray()['status'] == OrderStatus::PROCESSING->value) {
                            return true;
                        }
                        if ($record?->toArray() == Auth::user()?->customer?->shoppingCart?->toArray()) {
                            return false;
                        } else {
                            return true;
                        }
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->hidden(function ($record) {
                        if ($record?->toArray()['status'] == OrderStatus::PROCESSING->value) {
                            return false;
                        }

                        return true;
                    })
                    ->action(fn ($record) => $record->update(['status' => OrderStatus::CANCELLED]))
                    ->after(fn () => redirect(self::getUrl()))
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('heroicon-o-check')
                    ->hidden(function ($record) {
                        if ($record?->toArray()['status'] == OrderStatus::PROCESSING->value) {
                            return false;
                        }

                        return true;
                    })
                    ->action(fn ($record) => $record->update(['status' => OrderStatus::ACTIVE]))
                    ->after(fn () => redirect(self::getUrl()))
                    ->requiresConfirmation(),
            ])
            ->recordUrl(null)
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create a new order!'),
            ]);
    }

    public static function canCreate(): bool
    {
        if (Auth::user()?->customer?->canCreateOrder()) {
            return true;
        } else {
            return false;
        }
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
