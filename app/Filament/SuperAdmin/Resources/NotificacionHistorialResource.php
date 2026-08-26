<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\NotificacionHistorialResource\Pages;
use App\Filament\SuperAdmin\Resources\NotificacionHistorialResource\Tables\NotificacionHistorialsTable;
use App\Models\NotificacionHistorial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class NotificacionHistorialResource extends Resource
{
    protected static ?string $model = NotificacionHistorial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Historial de Notificaciones';

    protected static ?string $modelLabel = 'Notificación Enviada';

    protected static ?string $pluralModelLabel = 'Historial de Notificaciones';

    protected static string|UnitEnum|null $navigationGroup = 'Herramientas';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return NotificacionHistorialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificacionHistorials::route('/'),
        ];
    }
}
