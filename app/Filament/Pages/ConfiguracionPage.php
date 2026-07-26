<?php

namespace App\Filament\Pages;

use App\Models\Configuracion;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ConfiguracionPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'General';

    protected static ?string $title = 'Configuración';

    protected static ?string $slug = 'configuracion';

    protected string $view = 'filament.pages.configuracion';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'costo_filamento_kg' => Configuracion::get('costo_filamento_kg', 25000),
            'mail_mailer' => Configuracion::get('mail_mailer', 'smtp'),
            'mail_host' => Configuracion::get('mail_host', 'smtp.gmail.com'),
            'mail_port' => Configuracion::get('mail_port', '587'),
            'mail_username' => Configuracion::get('mail_username', ''),
            'mail_password' => Configuracion::get('mail_password', ''),
            'mail_encryption' => Configuracion::get('mail_encryption', 'tls'),
            'mail_from_address' => Configuracion::get('mail_from_address', ''),
            'mail_from_name' => Configuracion::get('mail_from_name', config('app.name')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Costo del Filamento')
                    ->description('Precio de referencia para el cálculo de costos de producción')
                    ->schema([
                        TextInput::make('costo_filamento_kg')
                            ->label('Costo del filamento por kg')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->suffix('/ kg'),
                    ]),

                Section::make('Configuración de Correo')
                    ->description('Datos del servidor SMTP para envío de comprobantes y notificaciones')
                    ->schema([
                        Select::make('mail_mailer')
                            ->label('Transporte')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'log' => 'Log (pruebas)',
                            ])
                            ->default('smtp')
                            ->required()
                            ->live(),
                        TextInput::make('mail_host')
                            ->label('Servidor SMTP (Host)')
                            ->default('smtp.gmail.com')
                            ->required()
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),
                        TextInput::make('mail_port')
                            ->label('Puerto')
                            ->default('587')
                            ->required()
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),
                        Select::make('mail_encryption')
                            ->label('Encriptación')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                '' => 'Ninguna',
                            ])
                            ->default('tls')
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),
                        TextInput::make('mail_username')
                            ->label('Usuario')
                            ->default('')
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),
                        TextInput::make('mail_password')
                            ->label('Contraseña')
                            ->password()
                            ->default('')
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),
                        TextInput::make('mail_from_address')
                            ->label('Email remitente')
                            ->email()
                            ->default('')
                            ->required()
                            ->visible(fn ($get) => $get('mail_mailer') !== 'log'),
                        TextInput::make('mail_from_name')
                            ->label('Nombre del remitente')
                            ->default(config('app.name'))
                            ->visible(fn ($get) => $get('mail_mailer') !== 'log'),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Configuracion::set('costo_filamento_kg', (int) $data['costo_filamento_kg']);
        Configuracion::setTexto('mail_mailer', $data['mail_mailer']);
        Configuracion::setTexto('mail_host', $data['mail_host'] ?? '');
        Configuracion::setTexto('mail_port', $data['mail_port'] ?? '');
        Configuracion::setTexto('mail_username', $data['mail_username'] ?? '');
        Configuracion::setTexto('mail_password', $data['mail_password'] ?? '');
        Configuracion::setTexto('mail_encryption', $data['mail_encryption'] ?? '');
        Configuracion::setTexto('mail_from_address', $data['mail_from_address'] ?? '');
        Configuracion::setTexto('mail_from_name', $data['mail_from_name'] ?? '');

        $this->aplicarConfiguracionCorreo($data);

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    private function aplicarConfiguracionCorreo(array $data): void
    {
        config([
            'mail.default' => $data['mail_mailer'] ?? 'smtp',
            'mail.mailers.smtp.host' => $data['mail_host'] ?? '',
            'mail.mailers.smtp.port' => $data['mail_port'] ?? '',
            'mail.mailers.smtp.username' => $data['mail_username'] ?? '',
            'mail.mailers.smtp.password' => $data['mail_password'] ?? '',
            'mail.mailers.smtp.encryption' => $data['mail_encryption'] ?? '',
            'mail.from.address' => $data['mail_from_address'] ?? '',
            'mail.from.name' => $data['mail_from_name'] ?? '',
        ]);
    }
}
