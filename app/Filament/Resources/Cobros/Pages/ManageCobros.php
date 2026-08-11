<?php

namespace App\Filament\Resources\Cobros\Pages;

use App\Filament\Resources\CobrosResource;
use App\Models\Cliente;
use App\Models\Venta;
use App\Services\CuentaCorrienteService;
use BoreiStudio\FilamentMercadoPago\Features\Payments\Actions\CreatePreferenceAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageCobros extends Page
{
    protected static string $resource = CobrosResource::class;

    protected string $view = 'filament.resources.cobros.pages.manage-cobros';

    public ?array $data = [
        'cliente_id' => null,
        'metodo_pago' => 'contado',
        'monto' => null,
        'venta_id' => null,
        'descripcion' => null,
    ];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Datos del Cobro')
                    ->columns(2)
                    ->schema([
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->options(Cliente::deEmpresa()->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Select::make('metodo_pago')
                            ->label('Método de pago')
                            ->options([
                                'contado' => 'Contado',
                                'transferencia' => 'Transferencia',
                                'mercado_pago' => 'MercadoPago',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),
                        TextInput::make('monto')
                            ->label('Monto ($)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->prefix('$'),
                        Select::make('venta_id')
                            ->label('Venta asociada (opcional)')
                            ->options(function () {
                                $clienteId = $this->data['cliente_id'] ?? null;
                                if (! $clienteId) {
                                    return [];
                                }

                                return Venta::deEmpresa()->where('cliente_id', $clienteId)
                                    ->where('tipo_venta', 'cuenta_corriente')
                                    ->where('total', '>', 0)
                                    ->get()
                                    ->mapWithKeys(fn ($v) => [$v->id => "Venta #{$v->id} - {$v->formatted_total}"]);
                            })
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function crearCobro(): void
    {
        $data = $this->form->getState();

        $cliente = Cliente::deEmpresa()->findOrFail($data['cliente_id']);
        $monto = (int) $data['monto'];

        if ($data['metodo_pago'] === 'mercado_pago') {
            $this->generarPagoMercadoPago($cliente, $monto, $data);

            return;
        }

        $tipoVenta = $data['metodo_pago'] === 'contado' ? 'contado' : 'transferencia';

        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => now()->toDateString(),
            'estado' => 'facturado',
            'total' => $monto,
            'descuento' => 0,
            'tipo_venta' => $tipoVenta,
        ]);

        $this->aplicarSaldoACuentaCorriente($cliente, $monto, $data);

        Notification::make()
            ->title('Cobro registrado')
            ->body("{$cliente->nombre} - $".number_format($monto, 0, ',', '.'))
            ->success()
            ->send();

        $this->form->fill([
            'cliente_id' => null,
            'metodo_pago' => 'contado',
            'monto' => null,
            'venta_id' => null,
            'descripcion' => null,
        ]);
    }

    private function aplicarSaldoACuentaCorriente(Cliente $cliente, int $monto, array $data): void
    {
        $cliente->refresh();

        $montoRestante = $monto;

        if (! empty($data['venta_id'])) {
            $movimiento = $cliente->movimientos()
                ->where('venta_id', $data['venta_id'])
                ->where('tipo', 'venta')
                ->where('monto', '>', 0)
                ->first();

            if ($movimiento) {
                $pago = min($montoRestante, $movimiento->monto);
                app(CuentaCorrienteService::class)
                    ->registrarPago($cliente, $pago, "Pago venta #{$data['venta_id']}");
                $montoRestante -= $pago;
            }
        }

        if ($montoRestante > 0) {
            app(CuentaCorrienteService::class)
                ->registrarPago($cliente, $montoRestante, 'Pago general');
        }
    }

    private function generarPagoMercadoPago(Cliente $cliente, int $monto, array $data): void
    {
        try {
            $action = app(CreatePreferenceAction::class);

            $titulo = $data['descripcion'] ?: "Cobro a {$cliente->nombre}";

            $result = $action->execute(
                items: [
                    [
                        'title' => $titulo,
                        'quantity' => 1,
                        'unit_price' => (float) $monto,
                        'currency_id' => 'ARS',
                    ],
                ],
                externalReference: "cobro_{$cliente->id}_".time(),
                backUrls: [
                    'success' => route('filament.admin.resources.cobros.index'),
                    'failure' => route('filament.admin.resources.cobros.index'),
                    'pending' => route('filament.admin.resources.cobros.index'),
                ],
            );

            $url = $result['sandbox_init_point'] ?? $result['init_point'];

            Notification::make()
                ->title('Link de pago generado')
                ->body('Redirigiendo a MercadoPago...')
                ->success()
                ->send();

            $this->redirect($url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al crear pago')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
