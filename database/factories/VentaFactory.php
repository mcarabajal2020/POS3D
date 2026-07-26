<?php

namespace Database\Factories;

use App\Enums\EstadoVenta;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venta>
 */
class VentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'fecha' => fake()->dateTimeBetween('-3 months', 'now'),
            'estado' => fake()->randomElement(EstadoVenta::cases()),
            'total' => fake()->numberBetween(1000, 500000),
            'descuento' => 0,
            'factura_tipo' => null,
            'factura_numero' => null,
            'factura_cae' => null,
        ];
    }
}
