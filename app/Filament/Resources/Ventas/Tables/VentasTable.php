<?php

namespace App\Filament\Resources\Ventas\Tables;

use App\Enums\EstadoVenta;
use App\Enums\TipoComprobante;
use App\Enums\TipoVenta;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                    ->weight(FontWeight::Bold),
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
                Action::make('compartir')
                    ->label('Compartir')
                    ->icon('heroicon-o-share')
                    ->color('primary')
                    ->modalHeading('Compartir comprobante')
                    ->modalWidth('sm')
                    ->modalSubmitAction(false)
                    ->modalContent(fn ($record) => view('filament.resources.ventas.tables.compartir-modal', [
                        'venta' => $record,
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
