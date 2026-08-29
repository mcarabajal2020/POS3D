<?php

namespace App\Filament\Concerns;

use App\Models\Filamento;

trait HasCalculadoraCostos
{
    public ?array $resumenCostos = null;

    public function calcularResumenCostos(): void
    {
        $data = $this->data ?? [];

        $filamentoId = $data['filamento_id'] ?? null;
        $filamento = $filamentoId ? Filamento::deEmpresa()->find($filamentoId) : null;
        $costoFilamentoKg = $filamento?->precio_kg ?? 25000;
        $gramos = (float) ($data['filamento_gramos'] ?? 0);
        $horas = (float) ($data['horas_impresion'] ?? 0);
        $minutos = (float) ($data['tiempo_minutos'] ?? 0);
        $watts = (float) ($data['consumo_watts'] ?? 120);
        $costoKwh = (float) ($data['costo_kwh'] ?? 50);
        $desgaste = (float) ($data['desgaste_maquina'] ?? 120);
        $manoObra = (float) ($data['costo_mano_obra'] ?? 0);
        $horasTrabajo = (float) ($data['horas_trabajo'] ?? 0);
        $extras = (float) ($data['extras'] ?? 0);
        $margen = (int) ($data['multiplicador_precio'] ?? 4);
        $cantidad = max((int) ($data['cantidad_piezas'] ?? 1), 1);

        $tiempoTotalHoras = $horas + ($minutos / 60);

        $material = (int) round(($gramos * $costoFilamentoKg) / 1000);
        $electricidad = (int) round(($watts * $tiempoTotalHoras * $costoKwh) / 1000);
        $desgasteTotal = (int) round($tiempoTotalHoras * $desgaste);
        $manoObraTotal = (int) round($horasTrabajo * $manoObra);
        $costoTotal = $material + $electricidad + $desgasteTotal + $manoObraTotal + (int) $extras;
        $costoPorUnidad = (int) round($costoTotal / $cantidad);
        $ventaSugerida = (int) round($costoTotal * $margen);

        $this->resumenCostos = [
            'material' => $material,
            'electricidad' => $electricidad,
            'desgaste' => $desgasteTotal,
            'mano_obra' => $manoObraTotal,
            'extras' => (int) $extras,
            'costo_total' => $costoTotal,
            'costo_por_unidad' => $costoPorUnidad,
            'venta_sugerida' => $ventaSugerida,
        ];
    }
}
