<?php

namespace App\Filament\Resources\Ventas\Schemas;

use App\Enums\EstadoVenta;
use App\Enums\TipoComprobante;
use App\Enums\TipoVenta;
use App\Models\Articulo;
use App\Models\Cliente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VentaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos de la Venta')
                    ->columns(1)
                    ->schema([
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->options(Cliente::deEmpresa()->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('fecha')
                            ->label('Fecha')
                            ->default(now())
                            ->required(),
                        Select::make('estado')
                            ->label('Estado')
                            ->options(EstadoVenta::class)
                            ->required()
                            ->native(false),
                        Select::make('tipo_venta')
                            ->label('Tipo de venta')
                            ->options(TipoVenta::class)
                            ->required()
                            ->native(false)
                            ->default(TipoVenta::Contado),
                        TextInput::make('descuento')
                            ->label('Descuento ($)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('$'),
                    ]),
                Section::make('Ítems de la Venta')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('articulo_id')
                                    ->label('Artículo')
                                    ->options(Articulo::deEmpresa()->pluck('nombre', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($set, $state) {
                                        if ($state) {
                                            $articulo = Articulo::deEmpresa()->find($state);
                                            if ($articulo) {
                                                $set('precio_unitario', $articulo->precio_venta);
                                            }
                                        }
                                    }),
                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $precio = $get('precio_unitario') ?? 0;
                                        $set('subtotal', $precio * ($state ?? 0));
                                    }),
                                TextInput::make('precio_unitario')
                                    ->label('Precio unitario ($)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->prefix('$')
                                    ->reactive()
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $cantidad = $get('cantidad') ?? 0;
                                        $set('subtotal', $state * $cantidad);
                                    }),
                                TextInput::make('subtotal')
                                    ->label('Subtotal ($)')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix('$'),
                            ])
                            ->columns(4)
                            ->addActionLabel('Agregar ítem')
                            ->defaultItems(1),
                    ]),
                Section::make('Facturación')
                    ->columns(1)
                    ->schema([
                        Select::make('factura_tipo')
                            ->label('Tipo de comprobante')
                            ->options(TipoComprobante::class)
                            ->native(false),
                        TextInput::make('factura_numero')
                            ->label('Número de factura')
                            ->maxLength(255),
                        TextInput::make('factura_cae')
                            ->label('CAE')
                            ->maxLength(255),
                    ])
                    ->collapsible(),
            ]);
    }
}
