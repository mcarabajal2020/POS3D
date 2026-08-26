<?php

namespace App\Filament\SuperAdmin\Resources\EmpresaResource\Pages;

use App\Enums\CondicionIva;
use App\Filament\SuperAdmin\Resources\EmpresaResource;
use App\Models\Cliente;
use Filament\Resources\Pages\CreateRecord;

class CreateEmpresa extends CreateRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function afterCreate(): void
    {
        Cliente::create([
            'nombre' => 'Consumidor Final',
            'cuit_cuil' => '99-99999999-9',
            'direccion' => 'Sin especificar',
            'telefono' => '',
            'email' => '',
            'condicion_iva' => CondicionIva::ConsumidorFinal,
            'saldo' => 0,
            'empresa_id' => $this->record->id,
        ]);
    }
}
