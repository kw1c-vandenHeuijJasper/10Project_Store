<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Helpers\Money;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return ProductResource::form($form);
    }

    public function table(Table $table): Table
    {
        return ProductResource::table($table)
            ->recordAction(null)
            ->recordTitleAttribute('name')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->limit(25),
                Tables\Columns\TextColumn::make('amount'),
                // TODO MAYBE, breaks observer things, I dont want it being updated.
                //I only want the created event to do something and also deleted event
                // \Filament\Tables\Columns\TextInputColumn::make('amount')
                //     ->rules(function ($record): array {
                //         $max = 'max:' . (int) $record->stock;
                //         return ['numeric', $max];
                //     })
                //     ->width('5%'),
                Tables\Columns\TextColumn::make('pivot.price')
                    ->label('Agreed price')
                    ->formatStateUsing(function ($state) {
                        return Money::prefix(Money::format($state));
                    }),
                \Filament\Tables\Columns\TextColumn::make('total')
                    ->label('Total Price')
                    ->getStateUsing(function ($record) {
                        return new HtmlString(Money::prefix(
                            Money::format(($record->pivot->price) * ($record->amount))
                        ));
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
                Tables\Actions\Action::make('totalPriceLabel')
                    ->label(function () {
                        $products = $this->getRelationship()->get();

                        $prices = $products->map(function ($product) {
                            return (int) $product->pivot->price * (int) $product->pivot->amount;
                        });

                        $totalPrice = Money::format($prices->sum());

                        return new HtmlString('Total price: '.Money::prefix($totalPrice > 0 ? $totalPrice : 'UNKNOWN'));
                    })
                    ->color('secondary')
                    ->disabled(),

                Tables\Actions\AttachAction::make()
                    // ->preloadRecordSelect()
                    ->form(fn (\Filament\Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),

                        Forms\Components\TextInput::make('amount')
                            ->integer()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->minValue(1)
                            ->rules(function (Get $get): array {
                                $recordId = $get('recordId');
                                if ($recordId) {
                                    $record = Product::find($recordId);
                                    if ($record) {
                                        $maxStock = (int) $record->stock;

                                        return ['numeric', 'max:'.$maxStock];
                                    }
                                }

                                return ['numeric'];
                            }),

                        \Filament\Forms\Components\Placeholder::make('Price Guide')
                            ->label('Price Guide')
                            ->content(new HtmlString('Prices are saved as an integer, so 72 is 0,72')),

                        Forms\Components\TextInput::make('price')
                            ->label('Price for one')
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->live()
                            ->readOnly()
                            ->prefix('€'),

                        Forms\Components\Placeholder::make('total')
                            ->label('Total Price')
                            ->content(function (Get $get) {
                                return Money::prefix($get('total'));
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

    public static function updateTotals(Get $get, Set $set): void
    {
        $price = Product::where('id', $get('recordId'))->pluck('price')->first();
        $set('price', $price);

        (int) $total = (int) $get('amount') * (int) $get('price');

        $set('total', $total);

        if ($get('total')) {
            (string) $input = $get('total');
            $total = Money::format($input);
        }

        $set('total', $total);
    }
}
