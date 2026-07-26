<x-filament-panels::page>
    <form wire:submit="crearCobro">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" size="lg">
                Registrar Cobro
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
