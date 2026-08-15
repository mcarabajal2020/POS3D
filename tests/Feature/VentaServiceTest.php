<?php

use App\Enums\EstadoVenta;
use App\Enums\TipoVenta;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Services\VentaService;

beforeEach(function () {
    $this->empresa = Empresa::factory()->create();
    $this->user = User::factory()->create();
    $this->user->empresas()->attach($this->empresa->id, ['role' => 'admin']);
    session(['empresa_id' => $this->empresa->id]);

    $this->service = app(VentaService::class);
});

function createVenta(array $overrides = []): Venta
{
    return Venta::factory()->create(array_merge([
        'empresa_id' => session('empresa_id'),
        'tipo_venta' => TipoVenta::Contado,
    ], $overrides));
}

it('calcula total correctamente con items y descuento', function () {
    $venta = createVenta(['descuento' => 1000]);

    VentaItem::factory()->create([
        'venta_id' => $venta->id,
        'cantidad' => 2,
        'precio_unitario' => 5000,
        'subtotal' => 10000,
        'empresa_id' => $this->empresa->id,
    ]);

    VentaItem::factory()->create([
        'venta_id' => $venta->id,
        'cantidad' => 1,
        'precio_unitario' => 3000,
        'subtotal' => 3000,
        'empresa_id' => $this->empresa->id,
    ]);

    $total = $this->service->calcularTotal($venta);

    expect($total)->toBe(12000); // 10000 + 3000 - 1000
});

it('calcula total como cero si descuento supera subtotal', function () {
    $venta = createVenta(['descuento' => 50000]);

    VentaItem::factory()->create([
        'venta_id' => $venta->id,
        'cantidad' => 1,
        'precio_unitario' => 5000,
        'subtotal' => 5000,
        'empresa_id' => $this->empresa->id,
    ]);

    $total = $this->service->calcularTotal($venta);

    expect($total)->toBe(0);
});

it('recalcula y guarda el total en la venta', function () {
    $venta = createVenta(['total' => 0, 'descuento' => 500]);

    VentaItem::factory()->create([
        'venta_id' => $venta->id,
        'cantidad' => 3,
        'precio_unitario' => 2000,
        'subtotal' => 6000,
        'empresa_id' => $this->empresa->id,
    ]);

    $this->service->recalcularTotal($venta);

    expect($venta->fresh()->total)->toBe(5500); // 6000 - 500
});

it('registra movimiento en cuenta corriente para venta CC', function () {
    $cliente = Cliente::factory()->create([
        'empresa_id' => $this->empresa->id,
        'saldo' => 0,
    ]);

    $venta = createVenta([
        'tipo_venta' => TipoVenta::CuentaCorriente,
        'cliente_id' => $cliente->id,
        'total' => 15000,
    ]);

    $this->service->registrarEnCuentaCorriente($venta);

    $this->assertDatabaseHas('movimientos_cuenta_corriente', [
        'cliente_id' => $cliente->id,
        'venta_id' => $venta->id,
        'tipo' => 'venta',
        'monto' => 15000,
    ]);

    expect($cliente->fresh()->saldo)->toBe(15000);
});

it('no registra movimiento en cuenta corriente para venta de contado', function () {
    $cliente = Cliente::factory()->create([
        'empresa_id' => $this->empresa->id,
        'saldo' => 0,
    ]);

    $venta = createVenta([
        'tipo_venta' => TipoVenta::Contado,
        'cliente_id' => $cliente->id,
        'total' => 10000,
    ]);

    $this->service->registrarEnCuentaCorriente($venta);

    $this->assertDatabaseMissing('movimientos_cuenta_corriente', [
        'venta_id' => $venta->id,
    ]);

    expect($cliente->fresh()->saldo)->toBe(0);
});

it('calcula total del dia solo de contado', function () {
    $hoy = now()->toDateString();

    Venta::factory()->create([
        'fecha' => $hoy,
        'total' => 5000,
        'tipo_venta' => TipoVenta::Contado,
        'empresa_id' => $this->empresa->id,
    ]);

    Venta::factory()->create([
        'fecha' => $hoy,
        'total' => 8000,
        'tipo_venta' => TipoVenta::Transferencia,
        'empresa_id' => $this->empresa->id,
    ]);

    $total = $this->service->totalDelDia(TipoVenta::Contado);

    expect($total)->toBe(5000);
});

it('calcula total del dia de todas las ventas', function () {
    $hoy = now()->toDateString();

    Venta::factory()->create([
        'fecha' => $hoy,
        'total' => 3000,
        'empresa_id' => $this->empresa->id,
    ]);

    Venta::factory()->create([
        'fecha' => $hoy,
        'total' => 7000,
        'empresa_id' => $this->empresa->id,
    ]);

    $total = $this->service->totalDelDia();

    expect($total)->toBe(10000);
});

it('permite transicion de presupuesto a pendiente', function () {
    $venta = createVenta(['estado' => EstadoVenta::Presupuesto]);

    expect($this->service->puedeTransicionar($venta, EstadoVenta::Pendiente))->toBeTrue();
});

it('no permite transicion de presupuesto a terminado', function () {
    $venta = createVenta(['estado' => EstadoVenta::Presupuesto]);

    expect($this->service->puedeTransicionar($venta, EstadoVenta::Terminado))->toBeFalse();
});

it('no permite transicion de facturado a cualquier estado', function () {
    $venta = createVenta(['estado' => EstadoVenta::Facturado]);

    expect($this->service->puedeTransicionar($venta, EstadoVenta::Entregado))->toBeFalse();
    expect($this->service->puedeTransicionar($venta, EstadoVenta::Pendiente))->toBeFalse();
});
