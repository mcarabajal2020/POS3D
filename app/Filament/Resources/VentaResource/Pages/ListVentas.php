<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Mail\VentaComprobanteMail;
use App\Models\Venta;
use App\Services\ComprobanteService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function descargarPdfCompartir(int $ventaId): StreamedResponse
    {
        $venta = Venta::findOrFail($ventaId);
        $pdf = app(ComprobanteService::class)->generarPdf($venta);

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, "comprobante_venta_{$venta->id}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function enviarEmailCompartir(int $ventaId): void
    {
        $venta = Venta::findOrFail($ventaId);

        if (empty($venta->cliente->email)) {
            Notification::make()
                ->title('Error')
                ->body('El cliente no tiene un email configurado.')
                ->danger()
                ->send();

            return;
        }

        Mail::to($venta->cliente->email)
            ->send(new VentaComprobanteMail($venta));

        Notification::make()
            ->title('Comprobante enviado')
            ->body("Se envió el comprobante a {$venta->cliente->email}")
            ->success()
            ->send();
    }
}
