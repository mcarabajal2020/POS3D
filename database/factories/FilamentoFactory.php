<?php

namespace Database\Factories;

use App\Models\Filamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Filamento>
 */
class FilamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->word(),
            'precio_kg' => fake()->numberBetween(15000, 40000),
        ];
    }
}
