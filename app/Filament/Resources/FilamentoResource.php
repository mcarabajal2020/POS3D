<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FilamentoResource\Pages\CreateFilamento;
use App\Filament\Resources\FilamentoResource\Pages\EditFilamento;
use App\Filament\Resources\FilamentoResource\Pages\ListFilamentos;
use App\Filament\Resources\Filamentos\Schemas\FilamentoForm;
use App\Filament\Resources\Filamentos\Tables\FilamentosTable;
use App\Models\Filamento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FilamentoResource extends Resource
{
    protected static ?string $model = Filamento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Filamentos';

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Filamento';

    protected static ?string $pluralModelLabel = 'Filamentos';

    public static function form(Schema $schema): Schema
    {
        return FilamentoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FilamentosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->deEmpresa();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFilamentos::route('/'),
            'create' => CreateFilamento::route('/create'),
            'edit' => EditFilamento::route('/{record}/edit'),
        ];
    }
}
