<?php

namespace App\Filament\Resources\CuentaCorriente\Pages;

use App\Filament\Resources\CuentaCorrienteResource;
use Filament\Resources\Pages\ListRecords;

class ListMovimientos extends ListRecords
{
    protected static string $resource = CuentaCorrienteResource::class;
}
