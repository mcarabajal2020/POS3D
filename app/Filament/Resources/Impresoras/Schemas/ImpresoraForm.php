<?php

namespace App\Filament\Resources\Impresoras\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ImpresoraForm
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
                TextInput::make('consumo_watts')
                    ->label('Consumo (watts)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('watts'),
                TextInput::make('desgaste_hora')
                    ->label('Desgaste por hora')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$')
                    ->suffix('/ h'),
            ]);
    }
}
