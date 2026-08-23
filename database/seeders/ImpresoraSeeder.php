<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Impresora;
use Illuminate\Database\Seeder;

class ImpresoraSeeder extends Seeder
{
    public function run(): void
    {
        $empresas = Empresa::all();

        $impresoras = [
            ['nombre' => 'A1', 'consumo_watts' => 120, 'desgaste_hora' => 120],
            ['nombre' => 'A1 Mini', 'consumo_watts' => 100, 'desgaste_hora' => 80],
            ['nombre' => 'P1S', 'consumo_watts' => 120, 'desgaste_hora' => 150],
            ['nombre' => 'X1C', 'consumo_watts' => 120, 'desgaste_hora' => 200],
        ];

        foreach ($empresas as $empresa) {
            foreach ($impresoras as $impresora) {
                Impresora::updateOrCreate(
                    ['nombre' => $impresora['nombre'], 'empresa_id' => $empresa->id],
                    $impresora,
                );
            }
        }
    }
}
