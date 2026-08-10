@php
    $user = auth()->user();
    $empresas = $user->empresas;
    $empresaActual = $user->empresaActual();
@endphp

<div class="space-y-2 p-3">
    @if($empresaActual)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-2">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Empresa actual</p>
            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $empresaActual->nombre }}</p>
            <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ ucfirst($user->roleEnEmpresa($empresaActual)) }}</p>
        </div>
    @endif

    @if($empresas->count() > 1)
        <form method="POST" action="{{ route('super-admin.empresa.switch') }}">
            @csrf
            <select name="empresa_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}" {{ $empresa->id === ($empresaActual?->id) ? 'selected' : '' }}>
                        {{ $empresa->nombre }}
                    </option>
                @endforeach
            </select>
        </form>
    @endif

    <div class="text-center text-[10px] text-gray-400 dark:text-gray-500">
        v{{ config('app.version', '1.0.0') }}
    </div>
</div>
