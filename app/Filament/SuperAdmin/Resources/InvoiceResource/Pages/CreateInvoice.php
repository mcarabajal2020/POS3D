<?php

namespace App\Filament\SuperAdmin\Resources\InvoiceResource\Pages;

use App\Filament\SuperAdmin\Resources\InvoiceResource;
use App\Notifications\FacturaGeneradaNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function afterCreate(): void
    {
        $empresa = $this->record->empresa;
        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());

        foreach ($adminUsers as $user) {
            $user->notify(new FacturaGeneradaNotification($this->record));
        }
    }
}
