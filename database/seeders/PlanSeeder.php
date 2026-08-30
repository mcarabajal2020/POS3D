<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'nombre' => 'Básico',
                'precio_mensual' => 5000,
                'precio_anual' => 50000,
                'max_usuarios' => 2,
                'max_ventas_mensuales' => 100,
                'max_articulos' => 50,
                'max_filamentos' => 10,
                'max_impresoras' => 3,
                'trial_dias' => 14,
                'activo' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Profesional',
                'precio_mensual' => 15000,
                'precio_anual' => 150000,
                'max_usuarios' => 5,
                'max_ventas_mensuales' => 500,
                'max_articulos' => 200,
                'max_filamentos' => 30,
                'max_impresoras' => 10,
                'trial_dias' => 14,
                'activo' => true,
                'orden' => 2,
            ],
            [
                'nombre' => 'Enterprise',
                'precio_mensual' => 35000,
                'precio_anual' => 350000,
                'max_usuarios' => 0,
                'max_ventas_mensuales' => 0,
                'max_articulos' => 0,
                'max_filamentos' => 0,
                'max_impresoras' => 0,
                'trial_dias' => 30,
                'activo' => true,
                'orden' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['nombre' => $plan['nombre']],
                $plan,
            );
        }
    }
}
