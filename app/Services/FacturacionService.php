<?php

namespace App\Services;

use App\Enums\CicloFacturacion;
use App\Enums\EstadoInvoice;
use App\Enums\EstadoSubscription;
use App\Models\Empresa;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Notifications\FacturaGeneradaNotification;
use App\Notifications\PlanCambiadoNotification;
use App\Notifications\SuscripcionCreadaNotification;
use App\Notifications\SuscripcionVencidaNotification;

class FacturacionService
{
    public function crearSuscripcion(Empresa $empresa, Plan $plan, CicloFacturacion $ciclo = CicloFacturacion::Mensual): Subscription
    {
        $subscription = Subscription::create([
            'empresa_id' => $empresa->id,
            'plan_id' => $plan->id,
            'estado' => $plan->trial_dias > 0 ? EstadoSubscription::Trial : EstadoSubscription::Activa,
            'fecha_inicio' => now(),
            'fecha_fin' => $plan->trial_dias > 0 ? null : now()->addMonth(),
            'trial_fin' => $plan->trial_dias > 0 ? now()->addDays($plan->trial_dias) : null,
            'facturacion_ciclo' => $ciclo,
        ]);

        $empresa->update(['subscription_id' => $subscription->id]);

        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());
        foreach ($adminUsers as $user) {
            $user->notify(new SuscripcionCreadaNotification($subscription));
        }

        if ($plan->trial_dias <= 0) {
            $this->generarInvoice($subscription);
        }

        return $subscription;
    }

    public function cambiarPlan(Subscription $subscription, Plan $nuevoPlan): Subscription
    {
        $planAnterior = $subscription->plan->nombre;

        $subscription->update([
            'plan_id' => $nuevoPlan->id,
        ]);

        $empresa = $subscription->empresa;
        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());
        foreach ($adminUsers as $user) {
            $user->notify(new PlanCambiadoNotification($subscription, $planAnterior));
        }

        return $subscription;
    }

    public function suspender(Subscription $subscription): Subscription
    {
        $subscription->update(['estado' => EstadoSubscription::Suspendida]);

        $this->notificarVencimiento($subscription);

        return $subscription;
    }

    public function reactivar(Subscription $subscription): Subscription
    {
        $subscription->update([
            'estado' => EstadoSubscription::Activa,
            'fecha_fin' => now()->addMonth(),
        ]);

        $empresa = $subscription->empresa;
        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());
        foreach ($adminUsers as $user) {
            $user->notify(new SuscripcionCreadaNotification($subscription));
        }

        return $subscription;
    }

    public function generarInvoice(Subscription $subscription): Invoice
    {
        $monto = $subscription->facturacion_ciclo === CicloFacturacion::Anual
            ? ($subscription->plan->precio_anual ?? $subscription->plan->precio_mensual * 12)
            : $subscription->plan->precio_mensual;

        $invoice = Invoice::create([
            'subscription_id' => $subscription->id,
            'empresa_id' => $subscription->empresa_id,
            'monto' => $monto,
            'estado' => EstadoInvoice::Pendiente,
            'fecha_emision' => now(),
            'fecha_vencimiento' => now()->addDays(15),
        ]);

        $empresa = $subscription->empresa;
        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());
        foreach ($adminUsers as $user) {
            $user->notify(new FacturaGeneradaNotification($invoice));
        }

        return $invoice;
    }

    public function marcarInvoicePagada(Invoice $invoice, ?string $metodoPago = null): Invoice
    {
        $invoice->update([
            'estado' => EstadoInvoice::Pagada,
            'fecha_pago' => now(),
            'metodo_pago' => $metodoPago,
        ]);

        if ($invoice->subscription->estado === EstadoSubscription::Suspendida) {
            $this->reactivar($invoice->subscription);
        }

        return $invoice;
    }

    public function verificarVencimientos(): int
    {
        $vencidas = Subscription::query()
            ->whereIn('estado', [EstadoSubscription::Trial, EstadoSubscription::Activa])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('estado', EstadoSubscription::Trial)
                        ->where('trial_fin', '<', now());
                })->orWhere(function ($q) {
                    $q->where('estado', EstadoSubscription::Activa)
                        ->where('fecha_fin', '<', now());
                });
            })
            ->get();

        foreach ($vencidas as $subscription) {
            $subscription->update(['estado' => EstadoSubscription::Vencida]);
            $this->notificarVencimiento($subscription);
        }

        $invoicesVencidas = Invoice::query()
            ->where('estado', EstadoInvoice::Pendiente)
            ->where('fecha_vencimiento', '<', now())
            ->get();

        foreach ($invoicesVencidas as $invoice) {
            $invoice->update(['estado' => EstadoInvoice::Vencida]);
        }

        return $vencidas->count();
    }

    public function limitesAlcanzados(Empresa $empresa, string $recurso): bool
    {
        $subscription = $empresa->subscription;

        if (! $subscription || ! $subscription->estaActiva()) {
            return true;
        }

        $plan = $subscription->plan;

        if ($plan->isEnterprise()) {
            return false;
        }

        return match ($recurso) {
            'usuarios' => $empresa->users()->count() >= $plan->max_usuarios,
            'ventas' => $empresa->ventas()->whereMonth('created_at', now()->month)->count() >= $plan->max_ventas_mensuales,
            'articulos' => $empresa->articulos()->count() >= $plan->max_articulos,
            'filamentos' => $empresa->articulos()->distinct('filamento_id')->count('filamento_id') >= $plan->max_filamentos,
            'impresoras' => $empresa->articulos()->distinct('impresora_id')->count('impresora_id') >= $plan->max_impresoras,
            default => false,
        };
    }

    public function usage(Empresa $empresa): array
    {
        $subscription = $empresa->subscription;
        $plan = $subscription?->plan;

        return [
            'usuarios' => [
                'actual' => $empresa->users()->count(),
                'maximo' => $plan?->max_usuarios ?? 0,
            ],
            'ventas_mes' => [
                'actual' => $empresa->ventas()->whereMonth('created_at', now()->month)->count(),
                'maximo' => $plan?->max_ventas_mensuales ?? 0,
            ],
            'articulos' => [
                'actual' => $empresa->articulos()->count(),
                'maximo' => $plan?->max_articulos ?? 0,
            ],
        ];
    }

    private function notificarVencimiento(Subscription $subscription): void
    {
        $empresa = $subscription->empresa;
        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());
        foreach ($adminUsers as $user) {
            $user->notify(new SuscripcionVencidaNotification($subscription));
        }
    }
}
