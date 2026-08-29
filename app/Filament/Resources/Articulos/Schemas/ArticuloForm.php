<?php

namespace App\Filament\Resources\Articulos\Schemas;

use App\Models\Articulo;
use App\Models\Filamento;
use App\Models\Impresora;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

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

    protected static function money(string $name, ?string $label = null, bool $decimal = false): TextInput
    {
        $cleanInteger = fn ($state) => str_replace('.', '', $state);

        $cleanDecimal = function ($state) {
            $state = str_replace(' ', '', $state);

            if (str_contains($state, ',')) {
                $parts = explode(',', $state);
                $entero = str_replace('.', '', array_shift($parts));
                $decimales = implode(',', $parts);

                return $entero.'.'.$decimales;
            }

            return str_replace('.', '', $state);
        };

        $field = TextInput::make($name)
            ->label($label)
            ->type('text')
            ->inputMode('decimal')
            ->numeric()
            ->minValue(0)
            ->default(0)
            ->mask(RawJs::make($decimal ? '$money($input, ",", ".", 2)' : '$money($input, ",", ".", 0)'))
            ->mutateStateForValidationUsing($decimal ? $cleanDecimal : $cleanInteger)
            ->dehydrateStateUsing($decimal ? $cleanDecimal : $cleanInteger);

        return $field;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos Generales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('codigo_sku')
                            ->label('Código/SKU')
                            ->required()
                            ->scopedUnique(
                                ignoreRecord: true,
                                modifyQueryUsing: fn ($query) => $query->deEmpresa(),
                            )
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
                        Select::make('filamento_id')
                            ->label('Filamento')
                            ->options(Filamento::deEmpresa()->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $filamento = Filamento::deEmpresa()->find($state);
                                    if ($filamento) {
                                        $set('costo_filamento_kg', $filamento->precio_kg);
                                    }
                                }
                            }),
                        self::money('costo_filamento_kg', 'Costo filamento/kg')
                            ->disabled()
                            ->dehydrated()
                            ->prefix('$')
                            ->suffix('/ kg'),
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
                        self::money('filamento_gramos', 'Peso de la pieza (gramos)', decimal: true)
                            ->suffix('gramos')
                            ->live(onBlur: true),
                    ]),

                Section::make('Tiempo')
                    ->columns(2)
                    ->schema([
                        self::money('horas_impresion', 'Horas')
                            ->live(onBlur: true),
                        self::money('tiempo_minutos', 'Minutos')
                            ->maxValue(59)
                            ->live(onBlur: true),
                    ]),

                Section::make('Máquina y Energía')
                    ->columns(3)
                    ->schema([
                        Select::make('impresora_id')
                            ->label('Impresora')
                            ->options(Impresora::deEmpresa()->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $impresora = Impresora::deEmpresa()->find($state);
                                    if ($impresora) {
                                        $set('consumo_watts', $impresora->consumo_watts);
                                        $set('desgaste_maquina', $impresora->desgaste_hora);
                                    }
                                }
                            }),
                        self::money('consumo_watts', 'Consumo (watts)')
                            ->suffix('watts')
                            ->default(120)
                            ->live(onBlur: true),
                        self::money('costo_kwh', 'Costo kWh')
                            ->prefix('$')
                            ->default(50)
                            ->live(onBlur: true),
                        self::money('desgaste_maquina', 'Desgaste máquina')
                            ->prefix('$')
                            ->suffix('/h')
                            ->default(120)
                            ->live(onBlur: true),
                    ]),

                Section::make('Mano de Obra y Extras')
                    ->columns(3)
                    ->schema([
                        self::money('costo_mano_obra', 'Mano de obra')
                            ->prefix('$')
                            ->suffix('/h')
                            ->live(onBlur: true),
                        self::money('horas_trabajo', 'Horas de trabajo', decimal: true)
                            ->suffix('h')
                            ->live(onBlur: true),
                        self::money('extras', 'Extras')
                            ->prefix('$')
                            ->live(onBlur: true),
                    ]),

                Section::make('Ajustes Finales')
                    ->columns(2)
                    ->schema([
                        Select::make('multiplicador_precio')
                            ->label('Multiplicador de precio')
                            ->options([
                                1 => 'x1',
                                2 => 'x2 - Doble',
                                3 => 'x3 - Triple',
                                4 => 'x4 - Estándar',
                                5 => 'x5',
                                6 => 'x6',
                            ])
                            ->default(4)
                            ->required()
                            ->live(),
                        self::money('cantidad_piezas', 'Cantidad de piezas')
                            ->minValue(1)
                            ->live(onBlur: true),
                    ]),

                View::make('filament.resources.articulos.calculator-summary')
                    ->live(),

                Section::make('Precio y Stock')
                    ->columns(2)
                    ->schema([
                        self::money('precio_venta', 'Precio de venta ($)')
                            ->required()
                            ->prefix('$'),
                        self::money('stock', 'Stock')
                            ->required(),
                    ]),
            ]);
    }
}
