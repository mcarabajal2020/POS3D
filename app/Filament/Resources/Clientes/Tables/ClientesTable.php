<?php

namespace App\Filament\Resources\Clientes\Tables;

use App\Enums\CondicionIva;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('cuit_cuil')
                    ->label('CUIT/CUIL')
                    ->searchable(),
                TextColumn::make('condicion_iva')
                    ->label('Condición IVA')
                    ->badge()
                    ->color(fn (CondicionIva $state): string => match ($state) {
                        CondicionIva::ResponsableInscripto => 'primary',
                        CondicionIva::Monotributo => 'success',
                        CondicionIva::ConsumidorFinal => 'gray',
                        CondicionIva::Exento => 'warning',
                        CondicionIva::NoResponsable => 'info',
                    }),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('telefono')
                    ->label('Teléfono'),
                TextColumn::make('saldo')
                    ->label('Saldo CC')
                    ->formatStateUsing(fn (int $state): string => '$ '.number_format($state, 0, ',', '.'))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 0 => 'danger',
                        $state < 0 => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('con_deuda')
                    ->label('Con deuda')
                    ->query(fn ($query) => $query->where('saldo', '>', 0))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('historial')
                    ->label('Historial')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->modalHeading('Historial de movimientos')
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record) {
                        $movimientos = $record->movimientos()->latest()->get();
                        $ventas = $record->ventas()
                            ->where('tipo_venta', '!=', 'cuenta_corriente')
                            ->latest()
                            ->get();

                        return view('filament.resources.clientes.tables.historial-modal', [
                            'movimientos' => $movimientos,
                            'ventas' => $ventas,
                            'saldo' => $record->saldo,
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
