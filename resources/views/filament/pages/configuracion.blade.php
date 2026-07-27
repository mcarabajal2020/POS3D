<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit">
                Guardar
            </x-filament::button>

            <x-filament::button
                wire:click="probarConexion"
                color="gray"
                icon="heroicon-o-signal"
            >
                Probar conexión
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
