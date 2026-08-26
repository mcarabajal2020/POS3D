<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $mensaje,
        public readonly string $tipo = 'info',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
        ];
    }

    public function toFilament(object $notifiable): FilamentNotification
    {
        $notification = FilamentNotification::make()
            ->title($this->titulo)
            ->body($this->mensaje);

        return match ($this->tipo) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            'danger' => $notification->danger(),
            default => $notification->info(),
        };
    }
}
