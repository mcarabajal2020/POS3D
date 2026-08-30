<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PlanCambiadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public string $planAnterior,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $nuevoPlan = $this->subscription->plan->nombre;

        return [
            'title' => 'Plan cambiado',
            'body' => "Tu plan cambió de \"{$this->planAnterior}\" a \"{$nuevoPlan}\".",
            'color' => 'info',
            'format' => 'filament',
            'duration' => 0,
        ];
    }
}
