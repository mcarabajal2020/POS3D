<?php

namespace App\Mail;

use App\Models\Venta;
use App\Services\ComprobanteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VentaComprobanteMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Venta $venta,
    ) {}

    public function envelope(): Envelope
    {
        $comprobante = $this->venta->factura_tipo?->label() ?? 'Comprobante';

        return new Envelope(
            subject: "{$comprobante} #{$this->venta->id} - ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->renderView(),
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => app(ComprobanteService::class)->generarPdf($this->venta)->output(), "comprobante_venta_{$this->venta->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }

    private function renderView(): string
    {
        $this->venta->load(['cliente', 'items.articulo']);

        return view('emails.comprobante-venta', [
            'venta' => $this->venta,
        ])->render();
    }
}
