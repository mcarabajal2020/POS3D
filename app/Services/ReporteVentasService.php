<?php

namespace App\Services;

use App\Models\MovimientoCuentaCorriente;
use App\Models\Venta;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReporteVentasService
{
    public function __construct(
        protected CostoProduccionService $costoService,
    ) {}

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
            ->with(['cliente', 'items.articulo.filamento'])
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

        $totalCostoProduccion = 0;
        foreach ($ventas as $venta) {
            foreach ($venta->items as $item) {
                if ($item->articulo) {
                    $costo = $this->costoService->calcular($item->articulo);
                    $totalCostoProduccion += $costo['costo_por_unidad'] * $item->cantidad;
                }
            }
        }

        $gananciaReal = $totalVentas - $totalCostoProduccion;

        return [
            'total_ventas' => $totalVentas,
            'total_descuentos' => $totalDescuentos,
            'cantidad_ventas' => $cantidadVentas,
            'promedio' => $promedio,
            'total_contado' => $totalContado,
            'total_transferencia' => $totalTransferencia,
            'total_cuenta_corriente' => $totalCuentaCorriente,
            'total_mercado_pago' => $totalMercadoPago,
            'total_cobrado' => -1 * $totalCobrado,
            'total_costo_produccion' => $totalCostoProduccion,
            'ganancia_real' => $gananciaReal,
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
            fputcsv($handle, ['Total Ventas', $datos['totales']['total_ventas']]);
            fputcsv($handle, ['Costo Produccion', $datos['totales']['total_costo_produccion']]);
            fputcsv($handle, ['Ganancia Real', $datos['totales']['ganancia_real']]);
            fputcsv($handle, ['Total Cobrado', $datos['totales']['total_cobrado']]);
            fputcsv($handle, ['Cantidad Ventas', $datos['totales']['cantidad_ventas']]);
            fputcsv($handle, ['Promedio por Venta', $datos['totales']['promedio']]);
            fputcsv($handle, []);
            fputcsv($handle, ['DESGLOSE POR TIPO DE PAGO']);
            fputcsv($handle, ['Contado', $datos['totales']['total_contado']]);
            fputcsv($handle, ['Transferencia', $datos['totales']['total_transferencia']]);
            fputcsv($handle, ['Cuenta Corriente', $datos['totales']['total_cuenta_corriente']]);
            fputcsv($handle, ['MercadoPago', $datos['totales']['total_mercado_pago']]);
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
                    $item['monto'],
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
