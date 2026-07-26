<?php

namespace App\Filament\Resources\ArticuloResource\Pages;

use App\Filament\Concerns\HasCalculadoraCostos;
use App\Filament\Resources\ArticuloResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticulo extends EditRecord
{
    use HasCalculadoraCostos;

    protected static string $resource = ArticuloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Costos ya calculados via trait
    }
}
