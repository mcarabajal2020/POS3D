@php
    $user = auth()->user();
    $empresa = $user?->empresaActual();
@endphp

@if($empresa && $empresa->logo)
    <div class="px-4 pb-2">
        <img
            src="{{ $empresa->logo_url }}"
            alt="{{ $empresa->nombre }}"
            class="h-16 w-full rounded-lg object-contain"
        >
    </div>
@endif
