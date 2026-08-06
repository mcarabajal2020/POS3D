@php
    $allRecords = $movimientos->map(fn ($m) => [
        'date' => $m->created_at,
        'tipo' => $m->tipo,
        'descripcion' => $m->descripcion,
        'monto' => $m->monto,
        'venta_id' => $m->venta_id,
    ])->concat($ventas->map(fn ($v) => [
        'date' => $v->created_at,
        'tipo' => $v->tipo_venta === 'contado' ? 'contado' : 'transferencia',
        'descripcion' => 'Venta #' . $v->id,
        'monto' => $v->total,
        'venta_id' => $v->id,
    ]))->sortByDesc('date')->values();
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-white/5">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Deuda actual</span>
        <span class="text-lg font-bold @if($saldo > 0) text-danger-600 @elseif($saldo < 0) text-success-600 @else text-gray-500 @endif">
            $ {{ number_format($saldo, 0, ',', '.') }}
        </span>
    </div>

    @if($allRecords->isEmpty())
        <div class="text-center text-gray-500 py-8">
            No hay movimientos registrados.
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-2 font-medium text-gray-600 dark:text-gray-400">Fecha</th>
                    <th class="text-left py-2 font-medium text-gray-600 dark:text-gray-400">Tipo</th>
                    <th class="text-left py-2 font-medium text-gray-600 dark:text-gray-400">Detalle</th>
                    <th class="text-right py-2 font-medium text-gray-600 dark:text-gray-400">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allRecords as $item)
                    @php
                        $ventaUrl = $item['venta_id']
                            ? \Filament\Facades\Filament::getPanel('admin')->getUrlBuilder()
                                ->forRecord(\App\Models\Venta::find($item['venta_id']))
                                ->resources()
                                ->edit()
                            : null;
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $item['date']->format('d/m/Y H:i') }}</td>
                        <td class="py-2">
                            @if($item['tipo'] === 'venta')
                                <span class="inline-flex items-center rounded-md bg-gray-600/10 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">
                                    Venta CC
                                </span>
                            @elseif($item['tipo'] === 'pago')
                                <span class="inline-flex items-center rounded-md bg-success-600/10 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/20">
                                    Pago
                                </span>
                            @elseif($item['tipo'] === 'contado')
                                <span class="inline-flex items-center rounded-md bg-primary-600/10 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">
                                    Contado
                                </span>
                            @elseif($item['tipo'] === 'transferencia')
                                <span class="inline-flex items-center rounded-md bg-info-600/10 px-2 py-1 text-xs font-medium text-info-700 ring-1 ring-inset ring-info-600/20">
                                    Transferencia
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-warning-600/10 px-2 py-1 text-xs font-medium text-warning-700 ring-1 ring-inset ring-warning-600/20">
                                    Ajuste
                                </span>
                            @endif
                        </td>
                        <td class="py-2 text-gray-600 dark:text-gray-400">
                            @if($ventaUrl)
                                <a href="{{ $ventaUrl }}" class="text-primary-600 hover:text-primary-500 underline">
                                    {{ $item['descripcion'] }}
                                </a>
                            @else
                                {{ $item['descripcion'] }}
                            @endif
                        </td>
                        <td class="py-2 text-right font-medium @if($item['monto'] > 0) text-danger-600 @else text-success-600 @endif">
                            {{ $item['monto'] > 0 ? '+ ' : '' }}$ {{ number_format($item['monto'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
