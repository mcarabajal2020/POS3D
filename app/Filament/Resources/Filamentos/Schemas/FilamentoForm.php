<?php

namespace App\Filament\Resources\Filamentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FilamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('precio_kg')
                    ->label('Precio por kg')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$')
                    ->suffix('/ kg'),
            ]);
    }
}
