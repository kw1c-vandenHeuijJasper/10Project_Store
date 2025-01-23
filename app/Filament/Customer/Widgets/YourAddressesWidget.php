<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Address;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class YourAddressesWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    #[On('address-created')]
    public function refresh() {}

    public function table(Table $table): Table
    {
        return $table
            ->query(Address::where('user_id', Auth::id()))
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('house_number'),
                \Filament\Tables\Columns\TextColumn::make('street_name'),
                \Filament\Tables\Columns\TextColumn::make('zip_code'),
                \Filament\Tables\Columns\TextColumn::make('city'),
            ])
            ->recordTitleAttribute('street_name')
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
