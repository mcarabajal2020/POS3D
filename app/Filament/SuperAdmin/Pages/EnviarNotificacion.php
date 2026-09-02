<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Empresa;
use App\Models\NotificacionHistorial;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use UnitEnum;

class EnviarNotificacion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'Enviar Notificación';

    protected static ?string $title = 'Enviar Notificación a Usuarios';

    protected static string|UnitEnum|null $navigationGroup = 'Herramientas';

    protected string $view = 'filament.super-admin.pages.enviar-notificacion';

    public ?array $data = [];

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('titulo')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Título de la notificación'),
                Textarea::make('mensaje')
                    ->required()
                    ->rows(4)
                    ->placeholder('Escribe el mensaje aquí...'),
                Select::make('tipo')
                    ->required()
                    ->options([
                        'info' => 'Información',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'danger' => 'Urgente',
                    ])
                    ->default('info'),
                Select::make('destino')
                    ->required()
                    ->options([
                        'todos' => 'Todos los usuarios',
                        'super_admins' => 'Solo super admins',
                        'por_empresa' => 'Por empresa(s)',
                    ])
                    ->default('todos')
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('empresas', [])),
                Select::make('empresas')
                    ->label('Empresas')
                    ->options(Empresa::pluck('nombre', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated()
                    ->helperText('Seleccioná una o más empresas')
                    ->disabled(fn ($get) => $get('destino') !== 'por_empresa'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $empresas = $data['empresas'] ?? null;

        if ($data['destino'] === 'por_empresa') {
            $empresas = is_array($empresas) ? $empresas : [];

            if (empty($empresas)) {
                Notification::make()
                    ->title('Seleccioná al menos una empresa')
                    ->danger()
                    ->send();

                return;
            }
        }

        $usuarios = match ($data['destino']) {
            'todos' => User::all(),
            'super_admins' => User::where('is_super_admin', true)->get(),
            'por_empresa' => User::whereHas('empresas', function ($query) use ($empresas) {
                $empresasIds = array_map('intval', array_filter((array) $empresas));
                $query->whereIn('empresas.id', $empresasIds);
            })->get(),
        };

        if ($usuarios->isEmpty()) {
            Notification::make()
                ->title('No hay usuarios')
                ->body('No se encontraron usuarios para el destino seleccionado.')
                ->warning()
                ->send();

            return;
        }

        $color = match ($data['tipo']) {
            'success' => 'success',
            'warning' => 'warning',
            'danger' => 'danger',
            default => 'info',
        };

        $usuarios->each(function (User $user) use ($data, $color) {
            $user->notifications()->create([
                'id' => Str::uuid(),
                'type' => 'database',
                'data' => [
                    'title' => $data['titulo'],
                    'body' => $data['mensaje'],
                    'color' => $color,
                    'format' => 'filament',
                    'duration' => 'persistent',
                ],
                'read_at' => null,
            ]);
        });

        NotificacionHistorial::create([
            'titulo' => $data['titulo'],
            'mensaje' => $data['mensaje'],
            'tipo' => $data['tipo'],
            'destino' => $data['destino'],
            'empresas_ids' => $empresas ?? null,
            'cantidad_usuarios' => $usuarios->count(),
            'enviada_por' => auth()->id(),
        ]);

        Notification::make()
            ->title('Notificación enviada')
            ->body("Se envió la notificación a {$usuarios->count()} usuario(s).")
            ->success()
            ->send();

        $this->form->fill([]);
    }
}
