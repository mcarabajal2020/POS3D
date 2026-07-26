<?php

namespace Database\Factories;

use App\Models\Articulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Articulo>
 */
class ArticuloFactory extends Factory
{
    public function definition(): array
    {
        $gramos = fake()->randomFloat(2, 10, 500);
        $horas = (int) fake()->numberBetween(0, 8);
        $minutos = (int) fake()->numberBetween(0, 59);

        return [
            'codigo_sku' => fake()->unique()->numerify('SKU-#####'),
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'tipo_material' => fake()->randomElement(['PLA', 'ABS', 'PETG', 'TPU']),
            'filamento_gramos' => $gramos,
            'horas_impresion' => $horas,
            'tiempo_minutos' => $minutos,
            'consumo_watts' => fake()->randomElement([100, 120, 150, 200]),
            'costo_kwh' => fake()->numberBetween(30, 100),
            'desgaste_maquina' => fake()->numberBetween(80, 200),
            'costo_mano_obra' => 0,
            'horas_trabajo' => 0,
            'extras' => 0,
            'margen_ganancia' => fake()->randomElement([2, 3, 4, 5]),
            'cantidad_piezas' => 1,
            'precio_venta' => fake()->numberBetween(5000, 100000),
            'stock' => fake()->numberBetween(0, 50),
        ];
    }
}
