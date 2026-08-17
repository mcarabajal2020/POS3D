<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Enums\EstadoVenta;
use App\Filament\Resources\VentaResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cambiarEstado')
                ->label('Cambiar estado')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Select::make('estado')
                        ->label('Estado')
                        ->options(EstadoVenta::class)
                        ->required()
                        ->native(false)
                        ->default(fn ($record) => $record->estado),
                ])
                ->action(function (array $data, $record) {
                    $record->update(['estado' => $data['estado']]);

                    Notification::make()
                        ->title('Estado actualizado')
                        ->success()
                        ->send();
                }),
        ];
    }
}
