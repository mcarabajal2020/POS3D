<?php

use App\Models\Venta;
use App\Services\ComprobanteService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/comprobante/{venta}/pdf', function (Venta $venta) {
    $pdf = app(ComprobanteService::class)->generarPdf($venta);

    return response()->streamDownload(function () use ($pdf): void {
        echo $pdf->output();
    }, "comprobante_venta_{$venta->id}.pdf", [
        'Content-Type' => 'application/pdf',
    ]);
})->name('comprobante.pdf');
