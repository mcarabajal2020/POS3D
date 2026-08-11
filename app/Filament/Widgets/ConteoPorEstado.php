<?php

namespace App\Filament\Widgets;

use App\Enums\EstadoVenta;
use App\Models\Venta;
use App\Models\VentaItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConteoPorEstado extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public ?string $filtroEstado = null;

    protected function getStats(): array
    {
        $query = Venta::query()->deEmpresa();

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        $porEstado = $query->toBase()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $pendientesImpresion = VentaItem::query()
            ->whereHas('venta', function ($q) {
                $q->deEmpresa()->whereIn('estado', [EstadoVenta::Pendiente, EstadoVenta::EnProduccion]);
            })
            ->sum('cantidad');

        $stats = [
            Stat::make('Pendiente', $porEstado->get('pendiente', 0))
                ->description('ventas')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('En Producción', $porEstado->get('en_produccion', 0))
                ->description('ventas')
                ->descriptionIcon('heroicon-o-cog')
                ->color('info'),
            Stat::make('Terminado', $porEstado->get('terminado', 0))
                ->description('ventas')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Entregado', $porEstado->get('entregado', 0))
                ->description('ventas')
                ->descriptionIcon('heroicon-o-truck')
                ->color('success'),
            Stat::make('Facturado', $porEstado->get('facturado', 0))
                ->description('ventas')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Pendientes Impresión', $pendientesImpresion)
                ->description('artículos')
                ->descriptionIcon('heroicon-o-cube')
                ->color('danger'),
        ];

        if (! $this->filtroEstado) {
            array_unshift($stats, Stat::make('Presupuesto', $porEstado->get('presupuesto', 0))
                ->description('ventas')
                ->descriptionIcon('heroicon-o-document-duplicate')
                ->color('gray'));
        }

        return $stats;
    }

    public function getFilters(): ?array
    {
        return [
            null => 'Todos',
            'presupuesto' => 'Presupuesto',
            'pendiente' => 'Pendiente',
            'en_produccion' => 'En Producción',
            'terminado' => 'Terminado',
            'entregado' => 'Entregado',
            'facturado' => 'Facturado',
        ];
    }

    public function getFilterLivewirePropertyName(): string
    {
        return 'filtroEstado';
    }
}
