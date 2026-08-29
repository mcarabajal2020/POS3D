<?php

namespace App\Filament\Widgets;

use App\Models\MovimientoCuentaCorriente;
use App\Models\Venta;
use Filament\Widgets\ChartWidget;

class FacturacionDeudaChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $heading = 'Facturación y Estado de cuenta';

    protected ?string $description = 'Total facturado, deuda y saldo a favor acumulado por día del mes en curso';

    protected function getData(): array
    {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();
        $hoy = now()->startOfDay();

        $facturado = Venta::query()
            ->deEmpresa()
            ->where('estado', 'facturado')
            ->whereBetween('fecha', [$inicio, $fin])
            ->selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $deudaInicial = MovimientoCuentaCorriente::query()
            ->deEmpresa()
            ->where('created_at', '<', $inicio)
            ->sum('monto');

        $movimientos = MovimientoCuentaCorriente::query()
            ->deEmpresa()
            ->whereBetween('created_at', [$inicio, $fin])
            ->selectRaw('DATE(created_at) as dia, SUM(monto) as saldo')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labels = [];
        $facturadoAcum = [];
        $deudaAcum = [];
        $saldoFavorAcum = [];

        $cursor = $inicio->copy();
        $acumFacturado = 0;
        $acumDeuda = $deudaInicial;

        while ($cursor <= $fin) {
            $fechaStr = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');

            if ($cursor > $hoy) {
                $facturadoAcum[] = null;
                $deudaAcum[] = null;
                $saldoFavorAcum[] = null;
            } else {
                $facturadoDia = $facturado->get($fechaStr);
                $acumFacturado += $facturadoDia ? (int) $facturadoDia->total : 0;
                $facturadoAcum[] = $acumFacturado;

                $movDia = $movimientos->get($fechaStr);
                $acumDeuda += $movDia ? (int) $movDia->saldo : 0;

                // positivo = cliente debe (deuda), negativo = saldo a favor
                $deudaAcum[] = $acumDeuda > 0 ? $acumDeuda : 0;
                $saldoFavorAcum[] = $acumDeuda < 0 ? abs($acumDeuda) : 0;
            }

            $cursor->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Facturado acumulado',
                    'data' => $facturadoAcum,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Deuda acumulada',
                    'data' => $deudaAcum,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Saldo a favor',
                    'data' => $saldoFavorAcum,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => '$',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
