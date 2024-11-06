<x-filament::widget>
    <x-filament::card>
        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-6">
                Submit
            </x-filament::button>
        </form>
    </x-filament::card>
</x-filament::widget>
