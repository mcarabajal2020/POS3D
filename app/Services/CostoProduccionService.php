<?php

namespace App\Services;

use App\Models\Articulo;

class CostoProduccionService
{
    public function calcular(Articulo $articulo): array
    {
        $costoFilamentoKg = $articulo->filamento->precio_kg ?? 25000;
        $costoMaterial = (int) round(($articulo->filamento_gramos * $costoFilamentoKg) / 1000);

        $tiempoTotalHoras = $articulo->horas_impresion + ($articulo->tiempo_minutos / 60);

        $costoElectricidad = (int) round(
            ($articulo->consumo_watts * $tiempoTotalHoras * $articulo->costo_kwh) / 1000
        );

        $costoDesgaste = (int) round($tiempoTotalHoras * $articulo->desgaste_maquina);

        $costoManoObra = (int) round($articulo->horas_trabajo * $articulo->costo_mano_obra);

        $extras = $articulo->extras;

        $costoTotal = $costoMaterial + $costoElectricidad + $costoDesgaste + $costoManoObra + $extras;

        $cantidad = max($articulo->cantidad_piezas, 1);
        $costoPorUnidad = (int) round($costoTotal / $cantidad);

        $ventaSugerida = (int) round($costoTotal * $articulo->margen_ganancia);

        return [
            'material' => $costoMaterial,
            'electricidad' => $costoElectricidad,
            'desgaste' => $costoDesgaste,
            'mano_obra' => $costoManoObra,
            'extras' => $extras,
            'costo_total' => $costoTotal,
            'costo_por_unidad' => $costoPorUnidad,
            'venta_sugerida' => $ventaSugerida,
        ];
    }
}
