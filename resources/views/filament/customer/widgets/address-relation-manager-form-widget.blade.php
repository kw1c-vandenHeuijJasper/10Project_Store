<x-filament-widgets::widget>
    <!--
        TODO fix this,
        For some reason I need this instead of just doing
        x-filament::section collapsible collapsed
    -->
    @if (true == 1)
        <x-filament::section collapsible collapsed>
    @endif

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
