<?php

use App\Models\Venta;
use App\Services\ComprobanteService;
use Illuminate\Http\Request;
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

Route::post('/empresa/switch', function (Request $request) {
    $user = auth()->user();
    $empresaId = $request->input('empresa_id');

    if ($user->empresas()->where('empresa_id', $empresaId)->exists()) {
        session(['empresa_id' => $empresaId]);
    }

    return redirect()->back();
})->middleware('auth')->name('super-admin.empresa.switch');
