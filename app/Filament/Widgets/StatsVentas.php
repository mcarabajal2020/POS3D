<?php

namespace App\Filament\Widgets;

use App\Enums\EstadoVenta;
use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsVentas extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    public ?string $filtroEstado = null;

    protected function getStats(): array
    {
        $query = Venta::query()->where('estado', '!=', EstadoVenta::Terminado);

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        $total = (clone $query)->sum('total');
        $cantidad = (clone $query)->count();
        $promedio = $cantidad > 0 ? $total / $cantidad : 0;

        $contado = (clone $query)->where('tipo_venta', 'contado')->sum('total');
        $transferencia = (clone $query)->where('tipo_venta', 'transferencia')->sum('total');
        $cuentaCorriente = (clone $query)->where('tipo_venta', 'cuenta_corriente')->sum('total');

        return [
            Stat::make('Total Ventas', '$ '.number_format($total, 0, ',', '.'))
                ->description($cantidad.' ventas')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('primary'),
            Stat::make('Contado', '$ '.number_format($contado, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Transferencias', '$ '.number_format($transferencia, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info'),
            Stat::make('Cuenta Corriente', '$ '.number_format($cuentaCorriente, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Promedio', '$ '.number_format($promedio, 0, ',', '.'))
                ->description('por venta')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('gray'),
        ];
    }

    public function getFilters(): ?array
    {
        return [
            null => 'Sin terminado',
            'presupuesto' => 'Presupuesto',
            'pendiente' => 'Pendiente',
            'en_produccion' => 'En Producción',
            'entregado' => 'Entregado',
            'facturado' => 'Facturado',
        ];
    }

    public function getFilterLivewirePropertyName(): string
    {
        return 'filtroEstado';
    }
}
