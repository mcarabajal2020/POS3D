<x-filament-panels::page>
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 rounded-xl border border-danger-300 bg-danger-50 p-6 dark:border-danger-700 dark:bg-danger-900/20">
            <div class="flex items-center gap-3">
                <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-danger-500" />
                <div>
                    <h2 class="text-lg font-bold text-danger-700 dark:text-danger-300">
                        Tu empresa no tiene un plan activo
                    </h2>
                    <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">
                        No podés realizar operaciones hasta que se active tu suscripción.
                        Subí tu comprobante de transferencia para que revisemos tu pago.
                    </p>
                </div>
            </div>
        </div>

        <x-filament::section heading="Enviar comprobante de pago">
            <form wire:submit="submit">
                {{ $this->form }}

                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" color="primary">
                        Enviar comprobante
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        @if ($empresa && $empresa->subscription)
            <x-filament::section heading="Estado actual" class="mt-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-500 dark:text-gray-400">Plan:</span>
                        <span class="ml-2">{{ $empresa->subscription->plan->nombre ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500 dark:text-gray-400">Estado:</span>
                        <x-filament::badge :color="$empresa->subscription->estado->color()">
                            {{ $empresa->subscription->estado->label() }}
                        </x-filament::badge>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
