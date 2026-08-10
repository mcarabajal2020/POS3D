<?php

namespace App\Services;

use App\Models\Venta;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ComprobanteService
{
    public function generarPdf(Venta $venta): PDF
    {
        $venta->load(['cliente', 'items.articulo']);

        return app(PDF::class)
            ->loadView('comprobantes.venta', ['venta' => $venta])
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'Helvetica');
    }

    public function descargarPdf(Venta $venta): Response
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
