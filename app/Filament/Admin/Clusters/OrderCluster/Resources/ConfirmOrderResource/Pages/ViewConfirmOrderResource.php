<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResource;
use App\Filament\Admin\Clusters\OrderCluster\Resources\OrderResource;
use App\Filament\Admin\Resources\ProductResource;
use App\Filament\Admin\Resources\UserResource;
use App\Helpers\Money;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewConfirmOrderResource extends ViewRecord
{
    protected static string $resource = ConfirmOrderResource::class;

    public function getOrderProduct()
    {
        return $this->record->products->map(fn ($item): array => [
            'id' => $item->id,
            'total' => $item->pivot->total,
            'name' => $item->name,
            'stock' => $item->stock,
            'amount' => $item->pivot->amount,
            'price' => $item->price,
            'agreed_price' => $item->pivot->price,
        ]);
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
                                TextEntry::make('user.name'),
                                TextEntry::make('quick_confirm')
                                    ->label(
                                        fn (): HtmlString => $this->quickConfirm() == true ? new HtmlString(
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
                Actions::make([
                    InfoAction::make('Go to user')
                        ->url(fn ($record): string => UserResource::getUrl().'/'.$record->user_id.'/edit')
                        ->color('success'),
                    InfoAction::make('Go to user in new tab')
                        ->url(fn ($record): string => UserResource::getUrl().'/'.$record->user_id.'/edit')
                        ->openUrlInNewTab()
                        ->color('success'),
                ])->fullWidth(),
                Actions::make([
                    InfoAction::make('Go to order')
                        ->url(fn ($record): string => OrderResource::getUrl().'/'.$record->id.'/edit')
                        ->color('danger'),
                    InfoAction::make('Go to order in new tab')
                        ->url(fn ($record): string => OrderResource::getUrl().'/'.$record->id.'/edit')
                        ->openUrlInNewTab()
                        ->color('danger'),
                ])->fullWidth(),

                Section::make('Compare Products')
                    ->schema([
                        RepeatableEntry::make('products')
                            ->label('')
                            ->grid(2)
                            ->alignCenter()
                            ->schema([
                                Fieldset::make('Product')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(
                                                fn ($record): HtmlString => new HtmlString(
                                                    '<a href='.ProductResource::getUrl().
                                                        '/'.$record->id.'/edit'.' target="blank">
                                                            Product Name
                                                        </a>'
                                                )
                                            ),
                                        TextEntry::make('stock'),
                                        TextEntry::make('price')
                                            ->formatStateUsing(fn ($state): string => Money::prefixFormat($state)),
                                    ])
                                    ->columnSpan(3),
                                Fieldset::make('Order')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('pivot.total')
                                            ->label('Total Price')
                                            ->formatStateUsing(fn ($state): string => Money::prefixFormat($state)),
                                        TextEntry::make('pivot.amount')
                                            ->label('Amount'),
                                        TextEntry::make('pivot.price')
                                            ->label('Agreed Price')
                                            ->formatStateUsing(fn ($state): string => Money::prefixFormat($state)),
                                    ])->columnSpan(3),
                            ])->columns(2),
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

        if ($checked->contains(true)) {
            return true;
        } else {
            return false;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Back to processing')
                ->color(\Filament\Support\Colors\Color::Yellow)
                ->requiresConfirmation()
                ->modalHeading('Take the order back to processing?')
                ->modalDescription('This means the stock will be re-added, this cannot be undone! Only press when approved accidentally!')
                ->modalSubmitActionLabel('Yes, change to processing!')
                ->action(function ($record): void {
                    if ($record->status == OrderStatus::FINISHED) {
                        $orderProducts = $this->getOrderProduct();
                        $orderProducts->map(function (array $orderProduct): void {
                            $product = Product::find($orderProduct['id']);
                            $left = $product->stock + $orderProduct['amount'];
                            $product->update(['stock' => $left]);
                        });
                    }

                    $record->update(['status' => OrderStatus::PROCESSING]);
                }),

            Action::make('Approve')
                ->requiresConfirmation()
                ->modalHeading('Approve order?')
                ->modalDescription('This means the stock will be subtracted, this cannot be undone!')
                ->modalSubmitActionLabel('Yes, I approve!')
                ->color('success')
                ->action(function ($record): void {
                    $orderProducts = $this->getOrderProduct();
                    $orderProducts->map(function (array $orderProduct): void {
                        $product = Product::find($orderProduct['id']);
                        $left = $product->stock - $orderProduct['amount'];
                        $product->update(['stock' => $left]);
                    });
                    $record->update(['status' => OrderStatus::FINISHED]);
                }),

            Action::make('Deny and reactivate')
                ->requiresConfirmation()
                ->modalHeading('Deny & reactivate?')
                ->modalDescription("The order will be 'denied ', and turned back into the current shopping cart ")
                ->modalSubmitActionLabel('Yes, turn it back!')
                ->color('info')
                ->action(fn ($record) => $record->update(['status' => OrderStatus::ACTIVE])),

            Action::make('Cancel')
                ->requiresConfirmation()
                ->modalHeading('Cancel order?')
                ->modalDescription('This means the order cannot be interacted with anymore!')
                ->modalSubmitActionLabel('Yes, cancel!')
                ->color('danger')
                ->action(fn ($record) => $record->update(['status' => OrderStatus::CANCELLED])),
        ];
    }
}
