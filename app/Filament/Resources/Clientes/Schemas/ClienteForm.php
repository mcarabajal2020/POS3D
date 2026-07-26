<?php

namespace App\Filament\Resources\Clientes\Schemas;

use App\Enums\CondicionIva;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Cliente')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('cuit_cuil')
                            ->label('CUIT/CUIL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Select::make('condicion_iva')
                            ->label('Condición IVA')
                            ->options(CondicionIva::class)
                            ->required()
                            ->native(false),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('telefono')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('direccion')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
