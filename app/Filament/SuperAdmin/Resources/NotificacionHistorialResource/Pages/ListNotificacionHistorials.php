<?php

namespace App\Filament\SuperAdmin\Resources\NotificacionHistorialResource\Pages;

use App\Filament\SuperAdmin\Resources\NotificacionHistorialResource;
use Filament\Resources\Pages\ListRecords;

class ListNotificacionHistorials extends ListRecords
{
    protected static string $resource = NotificacionHistorialResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
