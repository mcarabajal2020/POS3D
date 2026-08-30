<?php

namespace App\Filament\Pages;

use App\Models\ComprobantePago;
use App\Models\Empresa;
use App\Models\User;
use App\Notifications\ComprobantePagoNotification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class EmpresaInactivaPage extends Page
{
    protected static ?string $title = 'Empresa Inactiva';

    protected static ?string $slug = 'empresa-inactiva';

    protected string $view = 'filament.pages.empresa-inactiva';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public ?Empresa $empresa = null;

    public function mount(): void
    {
        $empresaId = session('empresa_id');
        $this->empresa = $empresaId ? Empresa::find($empresaId) : null;

        $this->form->fill([
            'monto' => '',
            'notas' => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('monto')
                    ->label('Monto de la transferencia ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('archivo')
                    ->label('Comprobante de transferencia')
                    ->image()
                    ->disk('public')
                    ->directory('comprobantes-pago')
                    ->visibility('public')
                    ->required(),
                TextInput::make('notas')
                    ->label('Notas (opcional)')
                    ->placeholder('Número de transferencia, fecha, etc.'),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $archivo = $data['archivo'];

        if ($this->empresa) {
            $comprobante = ComprobantePago::create([
                'empresa_id' => $this->empresa->id,
                'subscription_id' => $this->empresa->subscription_id,
                'archivo' => $archivo,
                'monto' => $data['monto'],
                'estado' => 'pendiente',
                'notas' => $data['notas'] ?? null,
            ]);

            $admins = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin']))->get();

            foreach ($admins as $admin) {
                $admin->notify(new ComprobantePagoNotification($comprobante));
            }
        }

        Notification::make()
            ->title('Comprobante enviado')
            ->body('Tu comprobante fue enviado correctamente. Esperá la revisión del administrador.')
            ->success()
            ->send();

        $this->form->fill([
            'monto' => '',
            'notas' => '',
        ]);
    }
}
