<?php

namespace App\Filament\Resources\Impresoras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImpresorasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('consumo_watts')
                    ->label('Consumo')
                    ->sortable()
                    ->suffix('W'),
                TextColumn::make('desgaste_hora')
                    ->label('Desgaste/h')
                    ->sortable()
                    ->prefix('$')
                    ->suffix('/ h'),
                TextColumn::make('articulos_count')
                    ->counts('articulos')
                    ->label('Artículos')
                    ->sortable(),
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
