<?php

use App\Models\Venta;
use App\Services\ComprobanteService;
use App\Services\ReporteVentasService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/comprobante/{venta}/pdf', function (Venta $venta) {
    if (auth()->guest()) {
        abort(403);
    }

    $venta = Venta::deEmpresa()->findOrFail($venta->id);

    $pdf = app(ComprobanteService::class)->generarPdf($venta);

    return response()->streamDownload(function () use ($pdf): void {
        echo $pdf->output();
    }, "comprobante_venta_{$venta->id}.pdf", [
        'Content-Type' => 'application/pdf',
    ]);
})->middleware(['auth', 'throttle:pdf'])->name('comprobante.pdf');

Route::post('/empresa/switch', function (Request $request) {
    $user = auth()->user();
    $empresaId = $request->input('empresa_id');

    if ($user->empresas()->where('empresa_id', $empresaId)->exists()) {
        session(['empresa_id' => $empresaId]);
    }

    return redirect()->back();
})->middleware(['auth', 'throttle:empresa-switch'])->name('super-admin.empresa.switch');

Route::get('/reporte/ventas/csv', function (Request $request) {
    $desde = Carbon::parse($request->query('desde', now()->startOfMonth()->toDateString()));
    $hasta = Carbon::parse($request->query('hasta', now()->toDateString()));

    app(ReporteVentasService::class)->exportarCsv($desde, $hasta);
})->middleware(['auth'])->name('reporte.ventas.csv');

Route::get('/reporte/ventas/pdf', function (Request $request) {
    $desde = Carbon::parse($request->query('desde', now()->startOfMonth()->toDateString()));
    $hasta = Carbon::parse($request->query('hasta', now()->toDateString()));

    $pdf = app(ReporteVentasService::class)->exportarPdf($desde, $hasta);

    $filename = 'reporte_ventas_'.$desde->format('Ymd').'_'.$hasta->format('Ymd').'.pdf';

    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->output();
    }, $filename, [
        'Content-Type' => 'application/pdf',
    ]);
})->middleware(['auth'])->name('reporte.ventas.pdf');
