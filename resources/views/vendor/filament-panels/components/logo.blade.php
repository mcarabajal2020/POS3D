@php
    $brandName = filament()->getBrandName();
    $empresa = auth()->user()?->empresaActual();
    $empresaLogo = $empresa?->logo_url;
    $brandLogo = filament()->getBrandLogo();
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.5rem';

    $logoStyles = 'height: ' . e($brandLogoHeight);
@endphp

@if ($empresaLogo)
    <div class="fi-logo" style="{{ $logoStyles }}">
        <img
            alt="{{ $empresa->nombre }}"
            src="{{ $empresaLogo }}"
            class="h-8 w-auto object-contain rounded-[30px]"
        />
    </div>
@elseif ($brandLogo)
    <div class="fi-logo" style="{{ $logoStyles }}">
        <img
            alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
            src="{{ $brandLogo }}"
            style="{{ $logoStyles }}"
        />
    </div>
@else
    <div class="fi-logo" style="{{ $logoStyles }}">
        {{ $brandName }}
    </div>
@endif
