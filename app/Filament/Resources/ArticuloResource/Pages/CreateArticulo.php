<?php

namespace App\Filament\Resources\ArticuloResource\Pages;

use App\Filament\Concerns\HasCalculadoraCostos;
use App\Filament\Resources\ArticuloResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticulo extends CreateRecord
{
    use HasCalculadoraCostos;

    protected static string $resource = ArticuloResource::class;

    protected function afterCreate(): void
    {
        // Costos ya calculados via trait
    }
}
