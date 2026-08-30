<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FacturaGeneradaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $monto = '$ '.number_format($this->invoice->monto, 0, ',', '.');
        $vence = $this->invoice->fecha_vencimiento->format('d/m/Y');

        return [
            'title' => 'Nueva factura',
            'body' => "Se generó una factura por {$monto}. Vence el {$vence}.",
            'color' => 'warning',
            'format' => 'filament',
            'duration' => 0,
        ];
    }
}
