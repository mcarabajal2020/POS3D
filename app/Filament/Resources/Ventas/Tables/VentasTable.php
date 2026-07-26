<?php

namespace App\Filament\Resources\Ventas\Tables;

use App\Enums\EstadoVenta;
use App\Enums\TipoComprobante;
use App\Enums\TipoVenta;
use App\Mail\VentaComprobanteMail;
use App\Services\ComprobanteService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VentasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (EstadoVenta $state): string => match ($state) {
                        EstadoVenta::Presupuesto => 'gray',
                        EstadoVenta::Pendiente => 'warning',
                        EstadoVenta::EnProduccion => 'info',
                        EstadoVenta::Terminado => 'success',
                        EstadoVenta::Entregado => 'success',
                        EstadoVenta::Facturado => 'primary',
                    }),
                TextColumn::make('formatted_total')
                    ->label('Total')
                    ->sortable(),
                TextColumn::make('tipo_venta')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (TipoVenta $state): string => $state->color()),
                TextColumn::make('formatted_descuento')
                    ->label('Descuento')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('factura_tipo')
                    ->label('Comprobante')
                    ->badge()
                    ->color(fn (?TipoComprobante $state): string => match ($state) {
                        TipoComprobante::FacturaA => 'primary',
                        TipoComprobante::FacturaB => 'success',
                        TipoComprobante::FacturaC => 'warning',
                        TipoComprobante::Presupuesto => 'gray',
                        TipoComprobante::NotaCredito => 'danger',
                        TipoComprobante::NotaDebito => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'presupuesto' => 'Presupuesto',
                        'pendiente' => 'Pendiente',
                        'en_produccion' => 'En Producción',
                        'terminado' => 'Terminado',
                        'entregado' => 'Entregado',
                        'facturado' => 'Facturado',
                    ]),
                SelectFilter::make('tipo_venta')
                    ->label('Tipo de venta')
                    ->options(TipoVenta::class),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('descargarPdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function ($record): StreamedResponse {
                        $pdf = app(ComprobanteService::class)->generarPdf($record);

                        return response()->streamDownload(function () use ($pdf): void {
                            echo $pdf->output();
                        }, "comprobante_venta_{$record->id}.pdf", [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
                Action::make('enviarEmail')
                    ->label('Enviar por Email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar comprobante por email')
                    ->modalSubmitActionLabel('Enviar')
                    ->modalContent(fn ($record) => view('filament.resources.ventas.tables.enviar-email-modal', [
                        'venta' => $record,
                    ]))
                    ->action(function ($record) {
                        if (empty($record->cliente->email)) {
                            Notification::make()
                                ->title('Error')
                                ->body('El cliente no tiene un email configurado.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Mail::to($record->cliente->email)
                            ->send(new VentaComprobanteMail($record));

                        Notification::make()
                            ->title('Comprobante enviado')
                            ->body("Se envió el comprobante a {$record->cliente->email}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => ! empty($record->cliente->email)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
