<x-filament-panels::page>
    {{-- Filtros --}}
    <x-filament::section heading="Filtros" class="mb-6">
        <form wire:submit="buscar">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                {{ $this->form }}

                <div class="flex items-center">
                    <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                        Buscar
                    </x-filament::button>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-500">Exportar:</span>
                <x-filament::button tag="a" :href="route('reporte.ventas.csv', ['desde' => $fechaDesde, 'hasta' => $fechaHasta])" icon="heroicon-o-arrow-down-tray" color="gray" size="sm" target="_blank">
                    Excel (CSV)
                </x-filament::button>
                <x-filament::button tag="a" :href="route('reporte.ventas.pdf', ['desde' => $fechaDesde, 'hasta' => $fechaHasta])" icon="heroicon-o-document-text" color="danger" size="sm" target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    {{-- Totales --}}
    @if($totales)
        <x-filament::section heading="Resumen del período" class="mt-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500">Total Ventas</p>
                    <p class="text-xl font-bold">${{ number_format($totales['total_ventas'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">{{ $totales['cantidad_ventas'] }} ventas · Promedio ${{ number_format($totales['promedio'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500">Costo Producción</p>
                    <p class="text-xl font-bold text-danger-600">${{ number_format($totales['total_costo_produccion'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">Material + Electricidad + Desgaste + Mano de obra</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500">Ganancia Real</p>
                    @php $gananciaColor = $totales['ganancia_real'] >= 0 ? 'text-success-600' : 'text-danger-600'; @endphp
                    <p class="text-xl font-bold {{ $gananciaColor }}">${{ number_format($totales['ganancia_real'], 0, ',', '.') }}</p>
                    @if($totales['total_ventas'] > 0)
                        @php $margen = round(($totales['ganancia_real'] / $totales['total_ventas']) * 100, 1); @endphp
                        <p class="text-xs text-gray-400">Margen: {{ $margen }}%</p>
                    @endif
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500">Total Cobrado</p>
                    <p class="text-xl font-bold text-success-600">${{ number_format($totales['total_cobrado'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">Pagos recibidos en el período</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500">Descuentos</p>
                    <p class="text-sm font-semibold text-warning-600">${{ number_format($totales['total_descuentos'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500">Contado</p>
                    <p class="text-sm font-semibold">${{ number_format($totales['total_contado'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500">Transferencia</p>
                    <p class="text-sm font-semibold">${{ number_format($totales['total_transferencia'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500">MercadoPago</p>
                    <p class="text-sm font-semibold">${{ number_format($totales['total_mercado_pago'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-1 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500">Cuenta Corriente (pendiente)</p>
                    <p class="text-sm font-semibold text-danger-600">${{ number_format($totales['total_cuenta_corriente'], 0, ',', '.') }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Detalle --}}
        <x-filament::section heading="Detalle de movimientos" class="mt-6">
            @if(count($timeline) > 0)
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Fecha</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Tipo</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Cliente</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Descripción</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($timeline as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($item['fecha'])->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span @class([
                                            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium',
                                            'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' => $item['tipo'] === 'Venta',
                                            'bg-success-50 text-success-700 dark:bg-success-900/30 dark:text-success-300' => $item['tipo'] === 'Pago',
                                        ])>
                                            {{ $item['tipo'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $item['cliente'] }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $item['descripcion'] }}</td>
                                    <td class="px-4 py-3 text-right font-medium {{ $item['tipo'] === 'Venta' ? 'text-primary-600' : 'text-success-600' }}">
                                        ${{ number_format($item['monto'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay movimientos en el rango seleccionado.</p>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>
