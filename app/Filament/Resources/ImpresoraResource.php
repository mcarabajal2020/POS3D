<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImpresoraResource\Pages\CreateImpresora;
use App\Filament\Resources\ImpresoraResource\Pages\EditImpresora;
use App\Filament\Resources\ImpresoraResource\Pages\ListImpresoras;
use App\Filament\Resources\Impresoras\Schemas\ImpresoraForm;
use App\Filament\Resources\Impresoras\Tables\ImpresorasTable;
use App\Models\Impresora;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ImpresoraResource extends Resource
{
    protected static ?string $model = Impresora::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPrinter;

    protected static ?string $navigationLabel = 'Impresoras';

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Impresora';

    protected static ?string $pluralModelLabel = 'Impresoras';

    public static function form(Schema $schema): Schema
    {
        return ImpresoraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImpresorasTable::configure($table);
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
            'index' => ListImpresoras::route('/'),
            'create' => CreateImpresora::route('/create'),
            'edit' => EditImpresora::route('/{record}/edit'),
        ];
    }
}
