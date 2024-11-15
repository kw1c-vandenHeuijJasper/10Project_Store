<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return \App\Filament\Resources\ProductResource::form($form);
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\ProductResource::table($table)
            ->recordAction(null)
            ->recordTitleAttribute('name')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->limit(25),
                Tables\Columns\TextInputColumn::make('amount')
                    ->width('5%'),
                Tables\Columns\TextColumn::make('pivot.price')
                    ->label('Agreed price')
                    ->formatStateUsing(function ($state) {
                        return self::moneyFormat($state);
                    })
                    ->prefix('€'),
                \Filament\Tables\Columns\TextColumn::make('total')
                    ->label('Total Price')
                    ->prefix('€')
                    ->getStateUsing(function ($record) {
                        $total = self::moneyFormat(($record->pivot->price) * ($record->amount));

                        return new HtmlString($total);
                    }),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\Action::make('totalPriceLabel')
                //     ->label(new HtmlString('The total price = ' . $total_price))
                //     ->color('secondary')
                //     ->disabled(),
                Tables\Actions\Action::make('totalPriceLabel')
                    ->label(function () {
                        $products = $this->getRelationship()->get();

                        $prices = $products->map(function ($product) {
                            return (int) $product->pivot->price * (int) $product->pivot->amount;
                        });

                        $totalPrice = $prices->sum();

                        $totalPrice = self::moneyFormat($totalPrice);

                        return new HtmlString('The total price = €'.($totalPrice > 0 ? $totalPrice : 'UNKNOWN'));
                    })
                    ->color('secondary')
                    ->disabled(),
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (\Filament\Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->reactive()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),

                        Forms\Components\TextInput::make('amount')
                            ->integer()
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),

                        \Filament\Forms\Components\Placeholder::make('Price Guide Placeholder')
                            ->label('Price Guide')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<div style="background-color:grey">
                                    The price system is an integer, so 7 = 0,07 in decimal. <br>
                                    input 701 = 7,01 in decimal
                                </div>'
                            )),

                        Forms\Components\TextInput::make('price')
                            ->label('Price for one')
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->live()
                            ->readOnly()
                            ->prefix('€'),

                        Forms\Components\Placeholder::make('total')
                            ->reactive()
                            ->live()
                            ->label('Total Price')
                            ->content(function (Get $get, Set $set) {
                                return '€'.$get('total');
                            }),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    public static function moneyFormat($state)
    {
        (string) $input = $state;

        $input = str($input)->remove(' ')->toString();
        (string) $parttwo = substr($input, -2);

        if ($input[0] === '0') {
            $trimmed_input = ltrim($input, '0');
        } else {
            $trimmed_input = $input;
        }
        $partone = \Illuminate\Support\Str::of($trimmed_input)->chopEnd($parttwo);
        if ($partone == '' || $partone == $input) {
            $partone = '0';
        }
        $output = $partone.','.$parttwo;
        if (strlen($input) == 1) {
            $output = '0,0'.$input;
        }

        return $output;
    }

    public static function updateTotals(Get $get, Set $set): void
    {

        $price = \App\Models\Product::where('id', $get('recordId'))->pluck('price')->first();
        $set('price', $price);

        (int) $total = (int) $get('amount') * (int) $get('price');
        Log::warning($total);

        $set('total', $total);
        if ($get('total')) {
            (string) $input = $get('total');
            $input = str($input)->remove(' ')->toString();
            (string) $parttwo = substr($input, -2);

            if ($input[0] === '0') {
                $trimmed_input = ltrim($input, '0');
            } else {
                $trimmed_input = $input;
            }
            $partone = \Illuminate\Support\Str::of($trimmed_input)->chopEnd($parttwo);
            if ($partone == '' || $partone == $input) {
                $partone = '0';
            }
            $total = $partone.','.$parttwo;
            if (strlen($input) == 1) {
                $total = '0,0'.$input;
            }
        }
        $set('total', $total);
    }
}
