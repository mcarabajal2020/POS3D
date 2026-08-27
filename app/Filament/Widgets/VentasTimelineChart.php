<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;

class VentasTimelineChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $heading = 'Ventas por día';

    protected ?string $description = 'Total vendido por día del mes en curso';

    protected function getData(): array
    {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();
        $hoy = now()->startOfDay();

        $ventas = Venta::query()
            ->deEmpresa()
            ->whereBetween('fecha', [$inicio, $fin])
            ->selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labels = [];
        $totales = [];

        $cursor = $inicio->copy();

        while ($cursor <= $fin) {
            $fechaStr = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');

            $ventasDia = $ventas->get($fechaStr);

            if ($cursor > $hoy) {
                $totales[] = null;
            } else {
                $totales[] = $ventasDia ? (int) $ventasDia->total : 0;
            }

            $cursor->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => '$ Vendidos',
                    'data' => $totales,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
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
                        'text' => '$ Vendidos',
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
