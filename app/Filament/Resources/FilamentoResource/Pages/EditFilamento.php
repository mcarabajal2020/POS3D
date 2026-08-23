<?php

namespace App\Filament\Resources\FilamentoResource\Pages;

use App\Filament\Resources\FilamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFilamento extends EditRecord
{
    protected static string $resource = FilamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
