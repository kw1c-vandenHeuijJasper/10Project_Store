<?php

namespace App\Filament\Customer\Resources;

use App\Enums\OrderStatus;
use App\Filament\Customer\Resources\ProductResource\Pages;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->searchable()
                        ->formatStateUsing(fn ($state): string => Str::ucfirst($state)),
                    TextColumn::make('description')
                        ->limit(255),
                    TextColumn::make('stock'),
                    TextColumn::make('price'),
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('Add to cart')
                    ->button()
                    ->icon('heroicon-m-shopping-cart')
                    ->hidden(function () {
                        if (Auth::user()->customer) {
                            return false;
                        }

                        return true;
                    })
                    ->color(function ($record): string {
                        if ($record->stock == 0) {
                            return 'danger';
                        }

                        return 'info';
                    })
                    ->disabled(function ($record): bool {
                        if ($record->stock == 0) {
                            return true;
                        }

                        return false;
                    })
                    ->action(function (Product $record): void {
                        // TODO ask how many to buy in modal???
                        $customer = Auth::user()?->customer;
                        $shoppingCart = $customer?->shoppingCart;

                        if ($shoppingCart) {
                            $order = $shoppingCart;
                        } else {
                            $order = Order::create([
                                'status' => OrderStatus::ACTIVE,
                                'customer_id' => $customer->id,
                            ]);
                        }

                        OrderProduct::create([
                            'order_id' => $order->id,
                            'product_id' => $record->id,
                            'price' => $record->price,
                        ]);
                    })
                    ->after(
                        fn (Product $record): Notification => Notification::make('added_to_cart')
                            ->title('Added '.Str::ucfirst($record->name).' to cart')
                            ->body("{$record->description}<br><br>{$record->price} <br>{$record->stock}")
                            ->success()
                            ->send()
                    ),
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->filters([
                // TODO
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            // TODO view page
        ];
    }
}
