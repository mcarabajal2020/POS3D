<?php

namespace App\Filament\Resources\Articulos\Tables;

use App\Services\CostoProduccionService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticulosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo_sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo_material')
                    ->label('Material')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PLA' => 'success',
                        'ABS' => 'warning',
                        'PETG' => 'info',
                        'TPU' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('formatted_costo_produccion')
                    ->label('Costo Prod.')
                    ->sortable(),
                TextColumn::make('formatted_precio')
                    ->label('Precio Venta')
                    ->sortable(),
                TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
