<?php

namespace App\Filament\Widgets;

use App\Services\FacturacionService;
use Filament\Widgets\Widget;

class InfoPlanWidget extends Widget
{
    protected string $view = 'filament.widgets.info-plan';

    protected int|string|array $columnSpan = 'full';

    public ?array $subscription = null;

    public ?array $usage = null;

    public function mount(): void
    {
        $empresa = auth()->user()?->empresaActual();

        if (! $empresa) {
            return;
        }

        $subscription = $empresa->subscription;

        if (! $subscription) {
            return;
        }

        $service = app(FacturacionService::class);

        $this->subscription = [
            'plan' => $subscription->plan->nombre,
            'estado' => $subscription->estado->label(),
            'estado_color' => $subscription->estado->color(),
            'ciclo' => $subscription->facturacion_ciclo->label(),
            'fecha_fin' => $subscription->fecha_fin?->format('d/m/Y'),
            'trial_fin' => $subscription->trial_fin?->format('d/m/Y'),
            'dias_restantes' => $subscription->diasRestantes(),
        ];

        $this->usage = $service->usage($empresa);
    }
}
