<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SuscripcionCreadaNotification extends Notification implements ShouldQueue
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
        $plan = $this->subscription->plan;

        return [
            'title' => 'Suscripción activada',
            'body' => "Se activó el plan \"{$plan->nombre}\" para tu empresa.",
            'color' => 'success',
            'format' => 'filament',
            'duration' => 0,
        ];
    }
}
