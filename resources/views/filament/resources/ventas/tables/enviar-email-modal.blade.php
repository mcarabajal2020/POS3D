<div class="space-y-2">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        Se enviará el comprobante de la venta <strong>#{{ $venta->id }}</strong> a:
    </p>
    <p class="text-sm font-medium text-gray-900 dark:text-white">
        {{ $venta->cliente->email }}
    </p>
</div>
