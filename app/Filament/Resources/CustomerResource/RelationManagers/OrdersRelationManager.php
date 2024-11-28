<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return \App\Filament\Resources\OrderResource::form($form);
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\OrderResource::table($table)
            ->headerActions(
                [
                    \Filament\Tables\Actions\Action::make('totalPriceOfAllOrders')
                        ->label(function () {
                            $customer = $this->ownerRecord;

                            $orders = $customer->orders;

                            //returns collections with instances of products
                            $collection = $orders->map(function ($order) {
                                return $order->products;
                            });

                            //returns collection of collections which have the prices
                            $prices = $collection->map(function ($products) {
                                return $products->map(function ($product) {
                                    $price_per_product = $product->pivot->price * $product->pivot->amount;

                                    return $price_per_product;
                                });
                            });
                            foreach ($prices as $pricesCollection) {
                                foreach ($pricesCollection as $pricesArray) {
                                    $allPrices[] = $pricesArray;
                                }
                            }
                            $totalPriceRaw = collect($allPrices)->sum();
                            $totalPrice = ProductsRelationManager::moneyFormat($totalPriceRaw);

                            return new HtmlString('The total price of all orders = ' . '€' . $totalPrice);
                        })
                        ->color('secondary')
                        ->disabled(),
                ],
            );
    }
}
