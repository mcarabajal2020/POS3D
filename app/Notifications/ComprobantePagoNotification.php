<?php

namespace App\Notifications;

use App\Models\ComprobantePago;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ComprobantePagoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ComprobantePago $comprobante,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nuevo comprobante de pago',
            'body' => "La empresa {$this->comprobante->empresa->nombre} envió un comprobante de $".number_format($this->comprobante->monto, 0, ',', '.').'.',
            'color' => 'warning',
            'comprobante_id' => $this->comprobante->id,
            'empresa_id' => $this->comprobante->empresa_id,
        ];
    }
}
