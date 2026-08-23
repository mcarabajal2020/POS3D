<?php

namespace App\Services;

use App\Models\MovimientoCuentaCorriente;
use App\Models\Venta;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReporteVentasService
{
    public function obtenerDatos(Carbon $desde, Carbon $hasta): array
    {
        $ventas = $this->obtenerVentas($desde, $hasta);
        $movimientos = $this->obtenerMovimientos($desde, $hasta);

        return [
            'totales' => $this->calcularTotales($ventas, $movimientos),
            'ventas' => $ventas,
            'movimientos' => $movimientos,
            'timeline' => $this->fusionarTimeline($ventas, $movimientos),
        ];
    }

    private function obtenerVentas(Carbon $desde, Carbon $hasta): Collection
    {
        return Venta::deEmpresa()
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->with('cliente')
            ->orderBy('fecha', 'desc')
            ->get();
    }

    private function obtenerMovimientos(Carbon $desde, Carbon $hasta): Collection
    {
        return MovimientoCuentaCorriente::deEmpresa()
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->with('cliente')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function calcularTotales(Collection $ventas, Collection $movimientos): array
    {
        $totalVentas = $ventas->sum('total');
        $totalDescuentos = $ventas->sum('descuento');
        $cantidadVentas = $ventas->count();
        $promedio = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;

        $totalContado = $ventas->where('tipo_venta', 'contado')->sum('total');
        $totalTransferencia = $ventas->where('tipo_venta', 'transferencia')->sum('total');
        $totalCuentaCorriente = $ventas->where('tipo_venta', 'cuenta_corriente')->sum('total');
        $totalMercadoPago = $ventas->where('tipo_venta', 'mercado_pago')->sum('total');

        $cobros = $movimientos->where('tipo', 'pago');
        $totalCobrado = $cobros->sum('monto');

        return [
            'total_ventas' => $totalVentas,
            'total_descuentos' => $totalDescuentos,
            'cantidad_ventas' => $cantidadVentas,
            'promedio' => $promedio,
            'total_contado' => $totalContado,
            'total_transferencia' => $totalTransferencia,
            'total_cuenta_corriente' => $totalCuentaCorriente,
            'total_mercado_pago' => $totalMercadoPago,
            'total_cobrado' => abs($totalCobrado),
        ];
    }

    private function fusionarTimeline(Collection $ventas, Collection $movimientos): Collection
    {
        $items = collect();

        foreach ($ventas as $venta) {
            $items->push([
                'fecha' => $venta->fecha,
                'tipo' => 'Venta',
                'cliente' => $venta->cliente->nombre ?? '-',
                'descripcion' => "Venta #{$venta->id} ({$venta->tipo_venta->label()})",
                'monto' => $venta->total,
                'monto_color' => 'text-primary-600 dark:text-primary-400',
            ]);
        }

        foreach ($movimientos as $movimiento) {
            if ($movimiento->tipo === 'pago') {
                $items->push([
                    'fecha' => $movimiento->created_at,
                    'tipo' => 'Pago',
                    'cliente' => $movimiento->cliente->nombre ?? '-',
                    'descripcion' => $movimiento->descripcion ?? 'Pago recibido',
                    'monto' => $movimiento->monto,
                    'monto_color' => 'text-success-600 dark:text-success-400',
                ]);
            }
        }

        return $items->sortByDesc('fecha')->values();
    }

    public function exportarCsv(Carbon $desde, Carbon $hasta): void
    {
        $datos = $this->obtenerDatos($desde, $hasta);

        $callback = function () use ($datos, $desde, $hasta) {
            $handle = fopen('php://output', 'r+');

            // Totales
            fputcsv($handle, ['REPORTE DE VENTAS Y COBROS']);
            fputcsv($handle, ['Desde:', $desde->format('d/m/Y'), 'Hasta:', $hasta->format('d/m/Y')]);
            fputcsv($handle, []);
            fputcsv($handle, ['RESUMEN']);
            fputcsv($handle, ['Total Ventas', '$'.number_format($datos['totales']['total_ventas'], 0, ',', '.')]);
            fputcsv($handle, ['Total Cobrado', '$'.number_format($datos['totales']['total_cobrado'], 0, ',', '.')]);
            fputcsv($handle, ['Cantidad Ventas', $datos['totales']['cantidad_ventas']]);
            fputcsv($handle, ['Promedio por Venta', '$'.number_format($datos['totales']['promedio'], 0, ',', '.')]);
            fputcsv($handle, []);
            fputcsv($handle, ['DESGLOSE POR TIPO DE PAGO']);
            fputcsv($handle, ['Contado', '$'.number_format($datos['totales']['total_contado'], 0, ',', '.')]);
            fputcsv($handle, ['Transferencia', '$'.number_format($datos['totales']['total_transferencia'], 0, ',', '.')]);
            fputcsv($handle, ['Cuenta Corriente', '$'.number_format($datos['totales']['total_cuenta_corriente'], 0, ',', '.')]);
            fputcsv($handle, ['MercadoPago', '$'.number_format($datos['totales']['total_mercado_pago'], 0, ',', '.')]);
            fputcsv($handle, []);

            // Detalle
            fputcsv($handle, ['DETALLE DE MOVIMIENTOS']);
            fputcsv($handle, ['Fecha', 'Tipo', 'Cliente', 'Descripción', 'Monto']);

            foreach ($datos['timeline'] as $item) {
                fputcsv($handle, [
                    $item['fecha']->format('d/m/Y'),
                    $item['tipo'],
                    $item['cliente'],
                    $item['descripcion'],
                    '$'.number_format($item['monto'], 0, ',', '.'),
                ]);
            }

            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_ventas_'.$desde->format('Ymd').'_'.$hasta->format('Ymd').'.csv"',
        ];

        response()->stream($callback, 200, $headers)->send();
    }

    public function exportarPdf(Carbon $desde, Carbon $hasta): PDF
    {
        $datos = $this->obtenerDatos($desde, $hasta);

        $pdf = app(PDF::class)->loadView('filament.pages.reporte-ventas-pdf', [
            'datos' => $datos,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf;
    }
}
