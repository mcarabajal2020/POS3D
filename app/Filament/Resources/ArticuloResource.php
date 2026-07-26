<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages\CreateArticulo;
use App\Filament\Resources\ArticuloResource\Pages\EditArticulo;
use App\Filament\Resources\ArticuloResource\Pages\ListArticulos;
use App\Filament\Resources\Articulos\Schemas\ArticuloForm;
use App\Filament\Resources\Articulos\Tables\ArticulosTable;
use App\Models\Articulo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Artículos';

    protected static ?string $modelLabel = 'Artículo';

    protected static ?string $pluralModelLabel = 'Artículos';

    public static function form(Schema $schema): Schema
    {
        return ArticuloForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticulosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticulos::route('/'),
            'create' => CreateArticulo::route('/create'),
            'edit' => EditArticulo::route('/{record}/edit'),
        ];
    }
}
