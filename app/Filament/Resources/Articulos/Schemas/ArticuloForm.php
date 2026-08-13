<?php

namespace App\Filament\Resources\Articulos\Schemas;

use App\Models\Articulo;
use App\Models\Configuracion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class ArticuloForm
{
    public static function siguienteSku(): string
    {
        $ultimoSku = Articulo::deEmpresa()
            ->where('codigo_sku', 'like', 'SKU-%')
            ->orderByRaw('CAST(SUBSTR(codigo_sku, 5) AS INTEGER) DESC')
            ->value('codigo_sku');

        if ($ultimoSku && preg_match('/^SKU-(\d+)$/', $ultimoSku, $matches)) {
            return 'SKU-'.str_pad((int) $matches[1] + 1, 5, '0', STR_PAD_LEFT);
        }

        return 'SKU-00001';
    }

    public static function configure(Schema $schema): Schema
    {
        $costoFilamentoKg = Configuracion::get('costo_filamento_kg', 25000);

        return $schema
            ->components([
                Section::make('Datos Generales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('codigo_sku')
                            ->label('Código/SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->default(fn () => self::siguienteSku()),
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ]),

                Section::make('Material')
                    ->columns(2)
                    ->schema([
                        Select::make('tipo_material')
                            ->label('Tipo de material')
                            ->options([
                                'PLA' => 'PLA',
                                'ABS' => 'ABS',
                                'PETG' => 'PETG',
                                'TPU' => 'TPU',
                            ])
                            ->default('PLA')
                            ->required()
                            ->live(),
                        TextInput::make('filamento_gramos')
                            ->label('Peso de la pieza (gramos)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('gramos')
                            ->live(onBlur: true),
                    ]),

                Section::make('Tiempo')
                    ->columns(2)
                    ->schema([
                        TextInput::make('horas_impresion')
                            ->label('Horas')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true),
                        TextInput::make('tiempo_minutos')
                            ->label('Minutos')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(59)
                            ->default(0)
                            ->live(onBlur: true),
                    ]),

                Section::make('Máquina y Energía')
                    ->columns(3)
                    ->schema([
                        Select::make('guia_modelo')
                            ->label('Guía rápida por modelo (Auto-completar)')
                            ->options([
                                'a1' => 'A1 (120W | Desgaste: $120/h)',
                                'a1_mini' => 'A1 Mini (100W | Desgaste: $80/h)',
                                'p1s' => 'P1S (120W | Desgaste: $150/h)',
                                'x1c' => 'X1C (120W | Desgaste: $200/h)',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state) {
                                $config = match ($state) {
                                    'a1' => ['consumo_watts' => 120, 'desgaste_maquina' => 120],
                                    'a1_mini' => ['consumo_watts' => 100, 'desgaste_maquina' => 80],
                                    'p1s' => ['consumo_watts' => 120, 'desgaste_maquina' => 150],
                                    'x1c' => ['consumo_watts' => 120, 'desgaste_maquina' => 200],
                                    default => null,
                                };
                                if ($config) {
                                    $set('consumo_watts', $config['consumo_watts']);
                                    $set('desgaste_maquina', $config['desgaste_maquina']);
                                }
                            }),
                        TextInput::make('consumo_watts')
                            ->label('Consumo (watts)')
                            ->numeric()
                            ->minValue(0)
                            ->default(120)
                            ->suffix('watts')
                            ->live(onBlur: true),
                        TextInput::make('costo_kwh')
                            ->label('Costo kWh')
                            ->numeric()
                            ->minValue(0)
                            ->default(50)
                            ->prefix('$')
                            ->live(onBlur: true),
                        TextInput::make('desgaste_maquina')
                            ->label('Desgaste máquina')
                            ->numeric()
                            ->minValue(0)
                            ->default(120)
                            ->prefix('$')
                            ->suffix('/h')
                            ->live(onBlur: true),
                    ]),

                Section::make('Mano de Obra y Extras')
                    ->columns(3)
                    ->schema([
                        TextInput::make('costo_mano_obra')
                            ->label('Mano de obra')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('$')
                            ->suffix('/h')
                            ->live(onBlur: true),
                        TextInput::make('horas_trabajo')
                            ->label('Horas de trabajo')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('h')
                            ->live(onBlur: true),
                        TextInput::make('extras')
                            ->label('Extras')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('$')
                            ->live(onBlur: true),
                    ]),

                Section::make('Ajustes Finales')
                    ->columns(2)
                    ->schema([
                        Select::make('margen_ganancia')
                            ->label('Margen de ganancia')
                            ->options([
                                1 => 'x1 - Sin margen',
                                2 => 'x2 - Doble',
                                3 => 'x3 - Triple',
                                4 => 'x4 - Estándar',
                                5 => 'x5',
                                6 => 'x6',
                            ])
                            ->default(4)
                            ->required()
                            ->live(),
                        TextInput::make('cantidad_piezas')
                            ->label('Cantidad de piezas')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->live(onBlur: true),
                    ]),

                View::make('filament.resources.articulos.calculator-summary')
                    ->live(),

                Section::make('Precio y Stock')
                    ->columns(2)
                    ->schema([
                        TextInput::make('precio_venta')
                            ->label('Precio de venta ($)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('$'),
                        TextInput::make('stock')
                            ->label('Stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),
            ]);
    }
}
