<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;

class VentasMesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Ventas del Mes';

    protected ?string $description = 'Total vendido y cantidad de ventas por día';

    protected function getData(): array
    {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();
        $hoy = now()->startOfDay();

        $labels = [];
        $totales = [];
        $cantidades = [];

        $ventas = Venta::query()
            ->deEmpresa()
            ->whereBetween('fecha', [$inicio, $fin])
            ->selectRaw('fecha, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');

        $cursor = $inicio->copy();

        while ($cursor <= $fin) {
            $fechaStr = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');

            $ventasDia = $ventas->get($fechaStr);

            if ($cursor > $hoy) {
                $totales[] = null;
                $cantidades[] = null;
            } else {
                $totales[] = $ventasDia ? (int) $ventasDia->total : 0;
                $cantidades[] = $ventasDia ? (int) $ventasDia->cantidad : 0;
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
                    'yAxisID' => 'y',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Cantidad de ventas',
                    'data' => $cantidades,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => false,
                    'yAxisID' => 'y1',
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
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            var label = context.dataset.label || "";
                            if (context.parsed.y !== null) {
                                if (label.includes("$")) {
                                    label += ": $" + context.parsed.y.toLocaleString();
                                } else {
                                    label += ": " + context.parsed.y;
                                }
                            }
                            return label;
                        }',
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
