<?php

namespace App\Filament\Customer\Pages;

use App\Filament\Customer\Widgets\AddressRelationManagerFormWidget;
use App\Filament\Customer\Widgets\YourAddressesWidget;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.customer.pages.edit-profile';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public ?array $profileData = [];

    public ?array $passwordData = [];

    public ?array $customerData = [];

    public static function canGoToPage(): bool
    {
        return Auth::user()->customer == null ? false : true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canGoToPage();
    }

    public static function canAccess(): bool
    {
        return self::canGoToPage();
    }

    public function mount(): void
    {
        $this->canGoToPage() == true ? $this->fillForms() : null;
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editCustomerForm',
            'editPasswordForm',
        ];
    }

    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Update your account\'s profile information and email address.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editCustomerForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Extra information')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->required()
                            ->native(false),
                    ]),
            ])
            ->model($this->getUser()->customer)
            ->statePath('customerData');
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password to stay secure.')
                    ->schema([
                        Forms\Components\TextInput::make('Current password')
                            ->password()
                            ->required()
                            ->currentPassword()
                            ->revealable(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation')
                            ->revealable(),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->password()
                            ->required()
                            ->dehydrated(false)
                            ->revealable(),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('passwordData');
    }

    protected function getUser(): Authenticatable&Model
    {
        $user = Filament::auth()->user();

        return $user instanceof Model ? $user
            : throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
    }

    protected function fillForms(): void
    {
        $userData = $this->getUser()->attributesToArray();
        $customerData = $this->getUser()->customer->attributesToArray();

        $this->editProfileForm->fill($userData);
        $this->editPasswordForm->fill();
        $this->editCustomerForm->fill($customerData);
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label('Save Profile')
                ->submit('editProfileForm'),
        ];
    }

    protected function getUpdateCustomerFormActions(): array
    {
        return [
            Action::make('updateCustomerAction')
                ->label('Save Extra Information')
                ->submit('editCustomerForm'),
        ];
    }

    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePasswordAction')
                ->label('Save Password')
                ->submit('editPasswordForm'),
        ];
    }

    public function updateProfile(): void
    {
        $data = $this->editProfileForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);

        Notification::make('profileUpdatedNotification')
            ->title('Saved!')
            ->body('Profile saved succesfully!')
            ->success()
            ->send();
    }

    public function updateCustomer(): void
    {
        $data = $this->editCustomerForm->getState();
        $this->handleRecordUpdate($this->getUser()->customer, $data);

        Notification::make('customerUpdatedNotification')
            ->title('Saved!')
            ->body('Extra information saved succesfully!')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $data = $this->editPasswordForm->getState();

        $this->handleRecordUpdate($this->getUser(), ['password' => $data['password']]);

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password' => $data['password'],
                'password_hash_web' => $data['password'],
            ]);
        }

        $this->editPasswordForm->fill();

        Notification::make('passwordUpdatedNotification')
            ->title('Saved!')
            ->body('Password saved succesfully!')
            ->success()
            ->send();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function getFooterWidgets(): array
    {
        return [
            YourAddressesWidget::class,
            AddressRelationManagerFormWidget::class,
        ];
    }
}
