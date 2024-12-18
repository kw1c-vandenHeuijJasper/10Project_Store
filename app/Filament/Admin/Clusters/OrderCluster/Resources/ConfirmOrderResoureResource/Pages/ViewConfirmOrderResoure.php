<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewConfirmOrderResoure extends ViewRecord
{
    protected static string $resource = ConfirmOrderResoureResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        // Mapping the collection of products
        $orderProduct = $this->record->products->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'stock' => $item->stock,
                'amount' => $item->pivot->amount,
                'price' => $item->price,
                'agreed_price' => $item->pivot->price,
            ];
        });

        return $infolist
            ->schema([
                Section::make('Order information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('reference'),
                                TextEntry::make('status'),
                                TextEntry::make('customer.user.name'),
                            ])->columnSpanFull(),
                    ]),

                Section::make('Compare Products')
                    ->schema([
                        Grid::make()
                            ->schema(
                                $orderProduct->map(function ($product) {
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
                                                    ->default($product['price']),
                                            ])->columnSpan(1),

                                            Section::make()->schema([
                                                TextEntry::make('Order'),

                                                TextEntry::make(''), //intentionally empty

                                                TextEntry::make('amount')
                                                    ->label('Amount')
                                                    ->default($product['amount']),

                                                TextEntry::make('agreed_price')
                                                    ->label('Agreed Price')
                                                    ->default($product['agreed_price']),

                                            ])->columnSpan(1),
                                        ])->columnSpan(2),
                                    ];
                                })->flatten()->toArray()
                            ),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Back to processing')
                ->action(fn ($record) => $record->update(['status' => OrderStatus::PROCESSING])),
            Actions\Action::make('Approve')
                ->color('success')
                ->action(fn ($record) => $record->update(['status' => OrderStatus::FINISHED])),
            Actions\Action::make('Deny')
                ->color('info')
                ->action(fn ($record) => $record->update(['status' => OrderStatus::ACTIVE])),
            Actions\Action::make('Cancel')
                ->color('danger')
                ->action(fn ($record) => $record->update(['status' => OrderStatus::CANCELLED])),
        ];
    }
}
