<?php

namespace Database\Factories;

use App\Models\Impresora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Impresora>
 */
class ImpresoraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->word(),
            'consumo_watts' => fake()->numberBetween(80, 200),
            'desgaste_hora' => fake()->numberBetween(80, 250),
        ];
    }
}
