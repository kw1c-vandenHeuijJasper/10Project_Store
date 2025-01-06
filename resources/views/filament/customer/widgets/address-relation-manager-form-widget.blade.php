<x-filament-widgets::widget>
    <x-filament::section collapsible collapsed id="add-address">
        <x-slot name="heading">
            Add another address
        </x-slot>

        <form wire:submit="create">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-3">
                Submit
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
