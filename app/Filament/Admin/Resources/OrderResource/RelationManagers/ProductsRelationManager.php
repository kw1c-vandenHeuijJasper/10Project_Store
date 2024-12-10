<?php

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use App\Filament\Admin\Resources\ProductResource;
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
                Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->limit(25),
                Tables\Columns\TextColumn::make('amount')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('pivot.price')
                    ->label('Agreed price')
                    ->formatStateUsing(fn($state) => Money::prefixFormat(($state))),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Price')
                    ->formatStateUsing(fn($record) => Money::prefixFormat($record->total)),

                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //TODO widget-ify please this hurts my soul
                Tables\Actions\Action::make('totalPriceLabel')
                    ->label(function () {
                        $products = $this->getRelationship()->get();
                        $totals = $products->map(fn($product) => $product->pivot->total);

                        return new HtmlString('Total price: ' . Money::prefixFormat($totals->sum()));
                    })
                    ->color('secondary')
                    ->disabled(),

                Tables\Actions\AttachAction::make()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
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
                                //TODO make better
                                $recordId = $get('recordId');
                                if ($recordId) {
                                    $record = Product::find($recordId);

                                    $maxStock = $record->stock;

                                    return ['numeric', 'max:' . $maxStock];
                                }

                                return ['numeric'];
                            }),

                        Forms\Components\Placeholder::make('Price Guide')
                            ->label('Price Guide')
                            ->content(new HtmlString('Prices are saved as an integer, so 72 is 0,72')),

                        Forms\Components\TextInput::make('price')
                            ->label('Price for one')
                            ->afterStateHydrated(
                                fn(Get $get, Set $set) => self::updateTotals($get, $set)
                            )
                            ->live()
                            ->readOnly()
                            ->prefix('€'),

                        Forms\Components\Placeholder::make('total')
                            ->label('Total Price')
                            ->content(fn(Get $get) => Money::prefix($get('total'))),
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
