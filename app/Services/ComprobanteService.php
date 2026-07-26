<?php

namespace App\Services;

use App\Models\Venta;
use Illuminate\Support\Facades\Storage;

class ComprobanteService
{
    public function generarPdf(Venta $venta): \Barryvdh\DomPDF\PDF
    {
        $venta->load(['cliente', 'items.articulo']);

        return app(\Barryvdh\DomPDF\PDF::class)
            ->loadView('comprobantes.venta', ['venta' => $venta])
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'Helvetica');
    }

    public function descargarPdf(Venta $venta): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generarPdf($venta);

        $nombreArchivo = "comprobante_venta_{$venta->id}.pdf";

        return $pdf->download($nombreArchivo);
    }

    public function guardarPdf(Venta $venta): string
    {
        $pdf = $this->generarPdf($venta);

        $nombreArchivo = "comprobantes/venta_{$venta->id}.pdf";

        Storage::disk('local')->put($nombreArchivo, $pdf->output());

        return $nombreArchivo;
    }
}
