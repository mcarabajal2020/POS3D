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

    public function probarConexion(): void
    {
        $data = $this->form->getState();
        $mailer = $data['mail_mailer'] ?? 'smtp';

        if ($mailer !== 'smtp') {
            Notification::make()
                ->title('Transporte no soportado')
                ->body('La prueba de conexión solo está disponible para SMTP.')
                ->warning()
                ->send();

            return;
        }

        $host = $data['mail_host'] ?? '';
        $port = (int) ($data['mail_port'] ?? 587);
        $username = $data['mail_username'] ?? '';
        $password = $data['mail_password'] ?? '';
        $encryption = $data['mail_encryption'] ?? 'tls';
        $fromAddress = $data['mail_from_address'] ?? '';
        $fromName = $data['mail_from_name'] ?? config('app.name');

        if (empty($host)) {
            Notification::make()
                ->title('Error')
                ->body('Debe configurar el servidor SMTP.')
                ->danger()
                ->send();

            return;
        }

        if (empty($fromAddress)) {
            Notification::make()
                ->title('Error')
                ->body('Debe configurar el email remitente.')
                ->danger()
                ->send();

            return;
        }

        try {
            $connection = $this->smtpConnect($host, $port, $encryption, $username, $password);

            Notification::make()
                ->title('Conexión exitosa')
                ->body("Se conectó correctamente a {$host}:{$port}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error de conexión')
                ->body("No se pudo conectar a {$host}:{$port}. {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }

    private function smtpConnect(string $host, int $port, string $encryption, string $username, string $password): void
    {
        $errno = 0;
        $errstr = '';

        $scheme = match ($encryption) {
            'ssl' => 'ssl://',
            default => 'tcp://',
        };

        $fp = fsockopen($scheme.$host, $port, $errno, $errstr, 10);

        if (! $fp) {
            throw new \RuntimeException("No se pudo conectar: {$errstr} (código: {$errno})");
        }

        $response = fgets($fp, 512);

        if (str_starts_with($response, '220') === false) {
            fclose($fp);
            throw new \RuntimeException("Respuesta inesperada del servidor: {$response}");
        }

        fwrite($fp, "EHLO localhost\r\n");
        $this->readSmtpResponse($fp);

        if ($encryption === 'tls') {
            fwrite($fp, "STARTTLS\r\n");
            $starttlsResponse = fgets($fp, 512);

            if (str_starts_with($starttlsResponse, '220') === false) {
                fclose($fp);
                throw new \RuntimeException("STARTTLS no soportado: {$starttlsResponse}");
            }

            $crypto = stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);

            if (! $crypto) {
                fclose($fp);
                throw new \RuntimeException('Error al habilitar TLS.');
            }

            fwrite($fp, "EHLO localhost\r\n");
            $this->readSmtpResponse($fp);
        }

        if (! empty($username) && ! empty($password)) {
            fwrite($fp, "AUTH LOGIN\r\n");
            $authResponse = fgets($fp, 512);

            if (! str_starts_with($authResponse, '334')) {
                fclose($fp);
                throw new \RuntimeException("AUTH LOGIN no soportado: {$authResponse}");
            }

            fwrite($fp, base64_encode($username)."\r\n");
            fgets($fp, 512);

            fwrite($fp, base64_encode($password)."\r\n");
            $passResponse = fgets($fp, 512);

            if (! str_starts_with($passResponse, '235')) {
                fclose($fp);
                throw new \RuntimeException('Autenticación fallida. Verifique usuario y contraseña.');
            }
        }

        fwrite($fp, "QUIT\r\n");
        fclose($fp);
    }

    private function readSmtpResponse($fp): void
    {
        while (($line = fgets($fp, 512)) !== false) {
            if (preg_match('/^\d{3}-/', $line) === 0) {
                break;
            }
        }
    }
}
