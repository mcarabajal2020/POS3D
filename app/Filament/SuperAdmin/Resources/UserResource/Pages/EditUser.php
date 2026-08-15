<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['password']) && ! empty($data['current_password'])) {
            if (! Hash::check($data['current_password'], $this->record->password)) {
                Notification::make()
                    ->title('La contraseña actual es incorrecta')
                    ->danger()
                    ->send();

                $data['password'] = $this->record->password;
            } else {
                unset($data['current_password']);
            }
        } else {
            unset($data['current_password'], $data['password']);
        }

        return $data;
    }
}
