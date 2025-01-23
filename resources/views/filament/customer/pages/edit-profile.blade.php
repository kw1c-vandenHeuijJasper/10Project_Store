<x-filament-panels::page>

    <x-filament-panels::form wire:submit="updateProfile">
        {{ $this->editProfileForm }}
        <x-filament-panels::form.actions :actions="$this->getUpdateProfileFormActions()" />
    </x-filament-panels::form>

    <!-- TODO customer user-->
    {{-- <x-filament-panels::form wire:submit="updateCustomer">
        {{ $this->editCustomerForm }}
        <x-filament-panels::form.actions :actions="$this->getUpdateCustomerFormActions()" />
    </x-filament-panels::form> --}}

    <x-filament-panels::form wire:submit="updatePassword">
        {{ $this->editPasswordForm }}
        <x-filament-panels::form.actions :actions="$this->getUpdatePasswordFormActions()" />
    </x-filament-panels::form>

</x-filament-panels::page>
