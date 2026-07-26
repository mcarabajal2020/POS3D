<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaCorriente\Pages\ListMovimientos;
use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;
use App\Services\CuentaCorrienteService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class CuentaCorrienteResource extends Resource
{
    protected static ?string $model = MovimientoCuentaCorriente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Cuenta Corriente';

    protected static ?string $modelLabel = 'Movimiento';

    protected static ?string $pluralModelLabel = 'Movimientos';

    protected static string|UnitEnum|null $navigationGroup = 'Finanzas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'venta' => 'danger',
                        'pago' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn (int $state): string => ($state >= 0 ? '+' : '-') . '$ ' . number_format(abs($state), 0, ',', '.'))
                    ->color(fn (int $state): string => $state >= 0 ? 'danger' : 'success'),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50),
                TextColumn::make('venta_id')
                    ->label('Venta')
                    ->formatStateUsing(fn ($state): string => $state ? "#{$state}" : '-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->options(Cliente::pluck('nombre', 'id'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'venta' => 'Venta',
                        'pago' => 'Pago',
                    ]),
            ])
            ->recordActions([
                Action::make('cobrar')
                    ->label('Registrar Pago')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->form([
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->options(Cliente::pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('monto')
                            ->label('Monto ($)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->prefix('$'),
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->default('Pago recibido'),
                    ])
                    ->action(function (array $data): void {
                        $cliente = Cliente::findOrFail($data['cliente_id']);
                        app(CuentaCorrienteService::class)
                            ->registrarPago($cliente, $data['monto'], $data['descripcion']);
                    })
                    ->successNotificationTitle('Pago registrado'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMovimientos::route('/'),
        ];
    }
}
