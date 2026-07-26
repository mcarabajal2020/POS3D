<div class="space-y-4">
    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-white/5">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Deuda actual</span>
        <span class="text-lg font-bold @if($saldo > 0) text-danger-600 @elseif($saldo < 0) text-success-600 @else text-gray-500 @endif">
            $ {{ number_format($saldo, 0, ',', '.') }}
        </span>
    </div>

    @if($movimientos->isEmpty())
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
                @foreach($movimientos as $mov)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2">
                            @if($mov->tipo === 'venta')
                                <span class="inline-flex items-center rounded-md bg-gray-600/10 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">
                                    Venta
                                </span>
                            @elseif($mov->tipo === 'pago')
                                <span class="inline-flex items-center rounded-md bg-success-600/10 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/20">
                                    Pago
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-warning-600/10 px-2 py-1 text-xs font-medium text-warning-700 ring-1 ring-inset ring-warning-600/20">
                                    Ajuste
                                </span>
                            @endif
                        </td>
                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $mov->descripcion }}</td>
                        <td class="py-2 text-right font-medium @if($mov->monto > 0) text-danger-600 @else text-success-600 @endif">
                            {{ $mov->monto > 0 ? '+ ' : '' }}$ {{ number_format($mov->monto, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
