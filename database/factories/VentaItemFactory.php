<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VentaItem>
 */
class VentaItemFactory extends Factory
{
    public function definition(): array
    {
        $cantidad = fake()->numberBetween(1, 10);
        $precioUnitario = fake()->numberBetween(1000, 100000);

        return [
            'venta_id' => Venta::factory(),
            'articulo_id' => Articulo::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $cantidad * $precioUnitario,
        ];
    }
}
