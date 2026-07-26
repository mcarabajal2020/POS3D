<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Enums\TipoVenta;
use App\Filament\Resources\VentaResource;
use App\Services\VentaService;
use BoreiStudio\FilamentMercadoPago\Features\Payments\Actions\CreatePreferenceAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function afterCreate(): void
    {
        $service = app(VentaService::class);
        $service->recalcularTotal($this->record);
        $service->registrarEnCuentaCorriente($this->record);

        if ($this->record->tipo_venta === TipoVenta::MercadoPago) {
            $this->generarPagoMercadoPago();
        }
    }

    private function generarPagoMercadoPago(): void
    {
        try {
            $action = app(CreatePreferenceAction::class);

            $items = $this->record->items->map(fn ($item) => [
                'title' => $item->articulo->nombre ?? "Venta #{$this->record->id}",
                'quantity' => $item->cantidad,
                'unit_price' => (float) $item->precio_unitario,
                'currency_id' => 'ARS',
            ])->toArray();

            if ($items === []) {
                $items = [
                    [
                        'title' => "Venta #{$this->record->id}",
                        'quantity' => 1,
                        'unit_price' => (float) $this->record->total,
                        'currency_id' => 'ARS',
                    ],
                ];
            }

            $result = $action->execute(
                items: $items,
                externalReference: "venta_{$this->record->id}",
                backUrls: [
                    'success' => route('filament.admin.resources.ventas.index'),
                    'failure' => route('filament.admin.resources.ventas.index'),
                    'pending' => route('filament.admin.resources.ventas.index'),
                ],
            );

            $url = $result['sandbox_init_point'] ?? $result['init_point'];

            Notification::make()
                ->title('Redirigiendo a MercadoPago...')
                ->success()
                ->send();

            $this->redirect($url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Venta creada pero error al generar pago')
                ->body($e->getMessage())
                ->warning()
                ->send();
        }
    }
}
