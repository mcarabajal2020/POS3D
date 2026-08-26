<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $empresa = Empresa::factory()->create([
            'nombre' => 'Mi Empresa',
        ]);

        $user->empresas()->attach($empresa->id, ['role' => 'admin']);

        session(['empresa_id' => $empresa->id]);

        $this->call([
            ConfiguracionSeeder::class,
            ConsumidorFinalSeeder::class,
        ]);
    }
}
