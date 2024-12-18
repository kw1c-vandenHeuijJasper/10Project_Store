<?php

namespace App\Filament\Admin\Clusters\OrderCluster\Resources;

use App\Enums\OrderStatus;
use App\Filament\Admin\Clusters\OrderCluster;
use App\Filament\Admin\Clusters\OrderCluster\Resources\ConfirmOrderResoureResource\Pages;
use App\Helpers\Money;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConfirmOrderResoureResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $label = 'Order Review';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = OrderCluster::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereStatus(OrderStatus::PROCESSING);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable(),
                TextColumn::make('customer.user.name')
                    ->searchable(),
                TextColumn::make('id')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => Money::prefixFormat(OrderProduct::whereOrderId($state)->sum('total'))),
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
