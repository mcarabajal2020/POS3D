<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Enums\EstadoInvoice;
use App\Enums\EstadoSubscription;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsFacturacion extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $mrr = Subscription::activas()
            ->with('plan')
            ->get()
            ->sum(fn (Subscription $s) => $s->plan->precio_mensual);

        $activas = Subscription::where('estado', EstadoSubscription::Activa)->count();
        $trials = Subscription::where('estado', EstadoSubscription::Trial)->count();
        $vencidas = Subscription::where('estado', EstadoSubscription::Vencida)->count();

        $facturasPendientes = Invoice::where('estado', EstadoInvoice::Pendiente)->count();
        $montoPendiente = Invoice::where('estado', EstadoInvoice::Pendiente)->sum('monto');

        return [
            Stat::make('MRR', '$ '.number_format($mrr, 0, ',', '.'))
                ->description('Ingreso mensual recurrente')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Suscripciones activas', $activas)
                ->description("{$trials} en prueba · {$vencidas} vencidas")
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('primary'),
            Stat::make('Facturas pendientes', $facturasPendientes)
                ->description('$ '.number_format($montoPendiente, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-document-text')
                ->color($facturasPendientes > 0 ? 'warning' : 'success'),
            Stat::make('Planes', Plan::where('activo', true)->count())
                ->description('Planes disponibles')
                ->descriptionIcon('heroicon-o-cube')
                ->color('info'),
        ];
    }
}
