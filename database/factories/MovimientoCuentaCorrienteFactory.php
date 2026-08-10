<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovimientoCuentaCorriente>
 */
class MovimientoCuentaCorrienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'venta_id' => null,
            'tipo' => fake()->randomElement(['venta', 'pago']),
            'monto' => fake()->numberBetween(1000, 100000),
            'descripcion' => fake()->sentence(),
        ];
    }
}
