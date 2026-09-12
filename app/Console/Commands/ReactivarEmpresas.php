<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use Illuminate\Console\Command;

class ReactivarEmpresas extends Command
{
    protected $signature = 'empresas:reactivar';

    protected $description = 'Reactiva empresas que tienen suscripción activa pero están marcadas como inactivas';

    public function handle(): int
    {
        $empresas = Empresa::where('activa', false)
            ->whereHas('subscription', fn ($q) => $q->where('estado', 'activa'))
            ->get();

        if ($empresas->isEmpty()) {
            $this->info('No hay empresas para reactivar.');

            return self::SUCCESS;
        }

        $count = $empresas->each(fn (Empresa $e) => $e->update(['activa' => true]))->count();

        $this->info("{$count} empresa(s) reactivada(s).");

        return self::SUCCESS;
    }
}
