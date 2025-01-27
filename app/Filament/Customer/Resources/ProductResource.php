<?php

namespace App\Filament\Customer\Resources;

use App\Enums\OrderStatus;
use App\Filament\Customer\Resources\ProductResource\Pages;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                self::addToCartAction(),
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->filters([
                Filter::make('in stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '>', 0))
                    ->toggle()
                    ->modifyFormFieldUsing(fn (Toggle $field): Toggle => $field->inline(false))
                    ->default(),
            ], layout: FiltersLayout::AboveContent);
    }

    private static function addToCartAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('Add to cart')
            ->button()
            ->icon('heroicon-m-shopping-cart')
            ->requiresConfirmation()
            ->modalIcon('heroicon-s-shopping-cart')
            ->modalDescription(
                fn ($record): string => 'How many '.
                    Str::ucfirst($record->name).
                    ' would you like to add to your shopping cart?'
            )->form([
                TextInput::make('amount')
                    ->integer()
                    ->required()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(fn (Product $record): int => $record->stock),
            ])
            ->label(function (): string {
                if (Auth::user()?->shoppingCart?->status == OrderStatus::PROCESSING) {
                    return 'Complete your current order first!';
                }

                return 'Add to cart';
            })
            ->hidden(function () {
                if (Auth::user()) {
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
                if (Auth::user()?->shoppingCart?->status == OrderStatus::PROCESSING) {
                    return true;
                }

                if (Auth::user()?->shoppingCart?->products) {
                    $ids = Auth::user()?->shoppingCart?->products->pluck('id');

                    return $ids->contains($record->id);
                }

                return $record->stock == 0;
            })
            ->action(function (Product $record, array $data): void {
                $user = Auth::user();
                $shoppingCart = $user?->shoppingCart;

                if ($shoppingCart) {
                    $order = $shoppingCart;
                } else {
                    $order = Order::create([
                        'status' => OrderStatus::ACTIVE,
                        'user_id' => $user->id,
                    ]);
                }

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $record->id,
                    'price' => $record->price,
                    'amount' => $data['amount'],
                ]);
            })
            ->after(
                fn (Product $record): Notification => Notification::make('added_to_cart')
                    ->title('Added '.Str::ucfirst($record->name).' to cart')
                    ->body("{$record->description}<br><br>{$record->price} <br>{$record->stock}")
                    ->success()
                    ->send()
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            // TODO product view page?
        ];
    }
}
