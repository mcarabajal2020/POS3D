<?php

namespace Database\Factories;

use App\Enums\CondicionIva;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'cuit_cuil' => fake()->unique()->numerify('##-########-#'),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'condicion_iva' => fake()->randomElement(CondicionIva::cases()),
        ];
    }
}
