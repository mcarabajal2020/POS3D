<?php

namespace App\Filament\SuperAdmin\Resources\NotificacionHistorialResource\Tables;

use App\Models\NotificacionHistorial;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class NotificacionHistorialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('mensaje')
                    ->limit(50)
                    ->tooltip(fn (NotificacionHistorial $record): string => $record->mensaje),
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => 'Información',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'danger' => 'Urgente',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('destino')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todos' => 'Todos',
                        'super_admins' => 'Super Admins',
                        'por_empresa' => 'Por Empresa(s)',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('cantidad_usuarios')
                    ->label('Enviado a')
                    ->suffix(' usuario(s)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('autor.name')
                    ->label('Enviado por')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
