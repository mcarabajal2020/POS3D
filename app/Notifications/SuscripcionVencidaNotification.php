<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SuscripcionVencidaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $fecha = $this->subscription->fecha_fin
            ? $this->subscription->fecha_fin->format('d/m/Y')
            : 'hoy';

        return [
            'title' => 'Suscripción vencida',
            'body' => "Tu suscripción venció el {$fecha}. Contactá al administrador para renovar.",
            'color' => 'danger',
            'format' => 'filament',
            'duration' => 0,
        ];
    }
}
