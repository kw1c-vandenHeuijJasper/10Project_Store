<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Address;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AddressRelationManagerFormWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.customer.widgets.address-relation-manager-form-widget';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('house_number')
                    ->required(),
                TextInput::make('street_name')
                    ->required(),
                TextInput::make('zip_code')
                    ->required(),
                TextInput::make('city')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $state = $this->form->getState() + ['customer_id' => Auth::user()->customer->id];
        Address::create($state);
        $this->form->fill();
        $this->dispatch('address-created');

        Notification::make('addressCreated')
            ->title('Created!')
            ->body('The address was created!')
            ->success()
            ->send();
    }
}
