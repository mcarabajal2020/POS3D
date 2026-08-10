<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Cobros\Pages\ManageCobros;
use App\Models\Venta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CobrosResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Cobros';

    protected static ?string $modelLabel = 'Cobro';

    protected static ?string $pluralModelLabel = 'Cobros';

    protected static string|UnitEnum|null $navigationGroup = 'Finanzas';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('tipo_venta')
                    ->label('Método')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value) {
                        'contado' => 'success',
                        'transferencia' => 'info',
                        'cuenta_corriente' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('formatted_total')
                    ->label('Monto')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value) {
                        'presupuesto' => 'gray',
                        'pendiente' => 'warning',
                        'en_produccion' => 'info',
                        'terminado' => 'success',
                        'entregado' => 'success',
                        'facturado' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Registrado')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCobros::route('/'),
        ];
    }
}
