<?php

namespace App\Filament\SuperAdmin\Resources\ComprobantePagoResource\Pages;

use App\Enums\EstadoSubscription;
use App\Filament\SuperAdmin\Resources\ComprobantePagoResource;
use App\Models\Empresa;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewComprobante extends ViewRecord
{
    protected static string $resource = ComprobantePagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('aprobar')
                ->label('Aprobar y activar empresa')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aprobar comprobante')
                ->modalDescription('Se activará la empresa y se cambiará el estado de la suscripción a activa.')
                ->action(function () {
                    $record = $this->record;
                    $record->update(['estado' => 'aprobado']);

                    $empresa = Empresa::find($record->empresa_id);

                    if ($empresa) {
                        $empresa->update(['activa' => true]);

                        if ($empresa->subscription) {
                            $empresa->subscription->update([
                                'estado' => EstadoSubscription::Activa,
                                'fecha_fin' => now()->addMonth(),
                            ]);
                        } else {
                            Subscription::create([
                                'empresa_id' => $empresa->id,
                                'plan_id' => 1,
                                'estado' => EstadoSubscription::Activa,
                                'fecha_inicio' => now(),
                                'fecha_fin' => now()->addMonth(),
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('Comprobante aprobado')
                        ->body("La empresa {$empresa->nombre} fue activada correctamente.")
                        ->success()
                        ->send();

                    $this->refreshFormData(['estado']);
                })
                ->visible(fn () => $this->record->estado === 'pendiente'),
            Actions\Action::make('rechazar')
                ->label('Rechazar')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Rechazar comprobante')
                ->modalDescription('El comprobante será marcado como rechazado.')
                ->action(function () {
                    $this->record->update(['estado' => 'rechazado']);

                    Notification::make()
                        ->title('Comprobante rechazado')
                        ->danger()
                        ->send();

                    $this->refreshFormData(['estado']);
                })
                ->visible(fn () => $this->record->estado === 'pendiente'),
        ];
    }
}
