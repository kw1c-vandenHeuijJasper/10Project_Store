<?php

namespace App\Filament\Customer\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getPhoneNumberFormComponent(),
                        $this->getDateOfBirthFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getPhoneNumberFormComponent(): Component
    {
        return TextInput::make('phone_number')
            ->tel();
    }

    protected function getDateOfBirthFormComponent(): Component
    {
        return DatePicker::make('date_of_birth')
            ->native(false);
    }
}
