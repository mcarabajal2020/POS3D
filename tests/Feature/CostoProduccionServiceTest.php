<?php

use App\Models\Articulo;
use App\Models\Configuracion;
use App\Models\Empresa;
use App\Models\User;
use App\Services\CostoProduccionService;

function makeArticulo(array $overrides = []): Articulo
{
    return Articulo::factory()->make(array_merge([
        'filamento_gramos' => 100,
        'horas_impresion' => 1,
        'tiempo_minutos' => 0,
        'consumo_watts' => 120,
        'costo_kwh' => 50,
        'desgaste_maquina' => 120,
        'costo_mano_obra' => 0,
        'horas_trabajo' => 0,
        'extras' => 0,
        'margen_ganancia' => 4,
        'cantidad_piezas' => 1,
    ], $overrides));
}

beforeEach(function () {
    $this->empresa = Empresa::factory()->create();
    $this->user = User::factory()->create();
    $this->user->empresas()->attach($this->empresa->id, ['role' => 'admin']);
    session(['empresa_id' => $this->empresa->id]);
});

it('calcula costo de material correctamente', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo(['filamento_gramos' => 100]);

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    // 100g * 25000 / 1000 = 2500
    expect($resultado['material'])->toBe(2500);
});

it('calcula costo de electricidad correctamente', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo([
        'horas_impresion' => 2,
        'tiempo_minutos' => 30,
        'consumo_watts' => 120,
        'costo_kwh' => 50,
    ]);

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    // 2.5h * 120W * 50 / 1000 = 15
    expect($resultado['electricidad'])->toBe(15);
});

it('calcula costo de desgaste correctamente', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo([
        'horas_impresion' => 2,
        'tiempo_minutos' => 0,
        'desgaste_maquina' => 120,
    ]);

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    // 2h * 120 = 240
    expect($resultado['desgaste'])->toBe(240);
});

it('calcula costo de mano de obra correctamente', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo([
        'horas_trabajo' => 3,
        'costo_mano_obra' => 500,
    ]);

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    // 3 * 500 = 1500
    expect($resultado['mano_obra'])->toBe(1500);
});

it('calcula costo total sumando todos los componentes', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo(['extras' => 500]);

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    $esperado = $resultado['material'] + $resultado['electricidad'] + $resultado['desgaste'] + $resultado['mano_obra'] + $resultado['extras'];
    expect($resultado['costo_total'])->toBe($esperado);
});

it('calcula venta sugerida con margen de ganancia', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo();

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    expect($resultado['venta_sugerida'])->toBe($resultado['costo_total'] * 4);
});

it('divide costo total entre cantidad de piezas', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo(['cantidad_piezas' => 5]);

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    expect($resultado['costo_por_unidad'])->toBe((int) round($resultado['costo_total'] / 5));
});

it('retorna desglose completo con todas las claves', function () {
    Configuracion::set('costo_filamento_kg', 25000);
    $articulo = makeArticulo();

    $resultado = app(CostoProduccionService::class)->calcular($articulo);

    expect($resultado)->toHaveKeys([
        'material',
        'electricidad',
        'desgaste',
        'mano_obra',
        'extras',
        'costo_total',
        'costo_por_unidad',
        'venta_sugerida',
    ]);
});
