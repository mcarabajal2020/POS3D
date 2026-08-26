<?php

namespace Database\Seeders;

use App\Enums\CondicionIva;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Database\Seeder;

class ConsumidorFinalSeeder extends Seeder
{
    public function run(): void
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            Cliente::updateOrCreate(
                [
                    'cuit_cuil' => '99-99999999-9',
                    'empresa_id' => $empresa->id,
                ],
                [
                    'nombre' => 'Consumidor Final',
                    'direccion' => 'Sin especificar',
                    'telefono' => '',
                    'email' => '',
                    'condicion_iva' => CondicionIva::ConsumidorFinal,
                    'saldo' => 0,
                    'empresa_id' => $empresa->id,
                ],
            );
        }
    }
}
