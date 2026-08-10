<?php

namespace App\Filament\SuperAdmin\Resources\EmpresaResource\Pages;

use App\Filament\SuperAdmin\Resources\EmpresaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpresa extends EditRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
