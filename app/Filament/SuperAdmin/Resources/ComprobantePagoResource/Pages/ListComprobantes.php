<?php

namespace App\Filament\SuperAdmin\Resources\ComprobantePagoResource\Pages;

use App\Filament\SuperAdmin\Resources\ComprobantePagoResource;
use Filament\Resources\Pages\ListRecords;

class ListComprobantes extends ListRecords
{
    protected static string $resource = ComprobantePagoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
