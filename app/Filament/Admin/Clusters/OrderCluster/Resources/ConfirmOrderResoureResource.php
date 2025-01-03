<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster;
use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource\Pages;
use App\Helpers\Money;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
// FIXME refactor (resource resource)
class ConfirmOrderResoureResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $label = 'order';

    protected static ?string $navigationLabel = 'Order Review';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = OrderCluster::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereStatus(OrderStatus::PROCESSING);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable(),
                TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('id')
                    ->label('Total')
                    ->formatStateUsing(fn($state) => Money::prefixFormat(OrderProduct::whereOrderId($state)->sum('total'))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfirmOrderResoures::route('/'),
            'view' => Pages\ViewConfirmOrderResoure::route('/{record}'),
        ];
    }
}
