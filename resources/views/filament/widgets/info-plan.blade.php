<x-filament::widget>
    @if($subscription)
        <x-filament::section heading="Mi Plan" class="mt-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500">Plan actual</p>
                    <p class="text-xl font-bold">{{ $subscription['plan'] }}</p>
                    <span @class([
                        'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium mt-1',
                        'bg-success-50 text-success-700 dark:bg-success-900/30 dark:text-success-300' => $subscription['estado_color'] === 'success',
                        'bg-info-50 text-info-700 dark:bg-info-900/30 dark:text-info-300' => $subscription['estado_color'] === 'info',
                        'bg-warning-50 text-warning-700 dark:bg-warning-900/30 dark:text-warning-300' => $subscription['estado_color'] === 'warning',
                        'bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300' => $subscription['estado_color'] === 'danger',
                        'bg-gray-50 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300' => $subscription['estado_color'] === 'gray',
                    ])>
                        {{ $subscription['estado'] }}
                    </span>
                </div>

                @if($usage)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500">Usuarios</p>
                        <p class="text-xl font-bold">{{ $usage['usuarios']['actual'] }} / {{ $usage['usuarios']['maximo'] }}</p>
                        @php $pct = $usage['usuarios']['maximo'] > 0 ? round(($usage['usuarios']['actual'] / $usage['usuarios']['maximo']) * 100) : 0; @endphp
                        <div class="mt-1 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                            <div @class([
                                'h-1.5 rounded-full',
                                $pct >= 90 ? 'bg-danger-500' : ($pct >= 70 ? 'bg-warning-500' : 'bg-success-500'),
                            ]) style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500">Ventas este mes</p>
                        <p class="text-xl font-bold">{{ $usage['ventas_mes']['actual'] }} / {{ $usage['ventas_mes']['maximo'] }}</p>
                        @php $pct = $usage['ventas_mes']['maximo'] > 0 ? round(($usage['ventas_mes']['actual'] / $usage['ventas_mes']['maximo']) * 100) : 0; @endphp
                        <div class="mt-1 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                            <div @class([
                                'h-1.5 rounded-full',
                                $pct >= 90 ? 'bg-danger-500' : ($pct >= 70 ? 'bg-warning-500' : 'bg-success-500'),
                            ]) style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500">Artículos</p>
                        <p class="text-xl font-bold">{{ $usage['articulos']['actual'] }} / {{ $usage['articulos']['maximo'] }}</p>
                        @php $pct = $usage['articulos']['maximo'] > 0 ? round(($usage['articulos']['actual'] / $usage['articulos']['maximo']) * 100) : 0; @endphp
                        <div class="mt-1 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                            <div @class([
                                'h-1.5 rounded-full',
                                $pct >= 90 ? 'bg-danger-500' : ($pct >= 70 ? 'bg-warning-500' : 'bg-success-500'),
                            ]) style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            @if($subscription['dias_restantes'] !== null && $subscription['dias_restantes'] <= 7)
                <div class="mt-4 rounded-xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-800 dark:bg-warning-900/20">
                    <p class="text-sm font-medium text-warning-800 dark:text-warning-200">
                        ⚠️ Tu plan vence en {{ $subscription['dias_restantes'] }} día(s).
                        @if($subscription['fecha_fin'])
                            Fecha de vencimiento: {{ $subscription['fecha_fin'] }}
                        @elseif($subscription['trial_fin'])
                            Fin de prueba: {{ $subscription['trial_fin'] }}
                        @endif
                    </p>
                </div>
            @endif
        </x-filament::section>
    @else
        <x-filament::section heading="Sin plan activo" class="mt-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-center dark:border-gray-700 dark:bg-gray-900">
                <p class="text-gray-500">Tu empresa no tiene un plan de suscripción activo.</p>
                <p class="text-sm text-gray-400 mt-1">Contactá al administrador para asignar un plan.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament::widget>
