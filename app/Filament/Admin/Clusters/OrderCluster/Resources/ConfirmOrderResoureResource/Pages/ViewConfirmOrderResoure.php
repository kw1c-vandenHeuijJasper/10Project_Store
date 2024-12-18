<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource;
use App\Helpers\Money;
use App\Models\Product;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewConfirmOrderResoure extends ViewRecord
{
    protected static string $resource = ConfirmOrderResoureResource::class;

    public function getOrderProduct()
    {
        return $this->record->products->map(function ($item) {
            return [
                'id' => $item->id,
                'total' => $item->pivot->total,
                'name' => $item->name,
                'stock' => $item->stock,
                'amount' => $item->pivot->amount,
                'price' => $item->price,
                'agreed_price' => $item->pivot->price,
            ];
        });
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('reference'),
                                TextEntry::make('status'),
                                TextEntry::make('customer.user.name'),
                                TextEntry::make('quick_confirm')
                                    ->label(
                                        fn() => $this->quickConfirm() == true ? new HtmlString(
                                            '<span style="color:red">
                                            Order is probably bad!
                                        </span>'
                                        ) : new HtmlString(
                                            '<span style="color:lime">
                                                Order is probably good!
                                            </span>'
                                        )
                                    ),
                            ])->columnSpanFull(),
                    ]),

                Section::make('Compare Products')
                    ->schema([
                        Grid::make()
                            ->schema(
                                $this->getOrderProduct()->map(function ($product) {
                                    return [
                                        Split::make([
                                            Section::make()->schema([
                                                TextEntry::make('name')
                                                    ->label('Product Name')
                                                    ->default($product['name']),

                                                TextEntry::make('stock')
                                                    ->label('Stock')
                                                    ->default($product['stock']),

                                                TextEntry::make('price')
                                                    ->label('Price')
                                                    ->default(Money::prefixFormat($product['price'])),
                                            ])->columnSpan(1),

                                            Section::make()->schema([
                                                TextEntry::make('total')
                                                    ->default(Money::prefixFormat($product['total'])),

                                                TextEntry::make('amount')
                                                    ->label('Amount')
                                                    ->default($product['amount']),

                                                TextEntry::make('agreed_price')
                                                    ->label('Agreed Price')
                                                    ->default(Money::prefixFormat($product['agreed_price'])),

                                            ])->columnSpan(1),
                                        ])->columnSpan(2),
                                    ];
                                })->flatten()->toArray()
                            ),
                    ]),
            ]);
    }

    /**
     * When something with the order is probably wrong, returns true
     * Will return false otherwise
     */
    public function quickConfirm(): bool
    {
        $orderProduct = $this->getOrderProduct();

        if ($orderProduct->toArray() == []) {
            return true;
        }

        $checked = $orderProduct->map(function ($order) {
            if ($order['stock'] < $order['amount']) {
                return true;
            } else {
                return null;
            }
        });

        if ($checked->whereNotNull()->contains(true)) {
            return true;
        } else {
            return false;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Back to processing')
                ->action(fn($record) => $record->update(['status' => OrderStatus::PROCESSING])),
            Actions\Action::make('Approve')
                ->color('success')
                ->action(function ($record) {
                    $orderProducts = $this->getOrderProduct();
                    $orderProducts->map(function ($orderProduct) {
                        $product = Product::find($orderProduct['id']);
                        $left = $product->stock - $orderProduct['amount'];
                        $product->update(['stock' => $left]);
                    });
                    $record->update(['status' => OrderStatus::FINISHED]);
                }),
            Actions\Action::make('Deny')
                ->color('info')
                ->action(fn($record) => $record->update(['status' => OrderStatus::ACTIVE])),
            Actions\Action::make('Cancel')
                ->color('danger')
                ->action(fn($record) => $record->update(['status' => OrderStatus::CANCELLED])),
        ];
    }
}
