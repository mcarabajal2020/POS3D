<?php

use App\Models\Empresa;
use App\Models\NotificacionHistorial;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->empresa = Empresa::factory()->create();
    $this->adminUser = User::factory()->create(['is_super_admin' => false]);
    $this->adminUser->empresas()->attach($this->empresa->id, ['role' => 'admin']);
    $this->superAdmin = User::factory()->create(['is_super_admin' => true]);
    session(['empresa_id' => $this->empresa->id]);
});

it('crea una notificacion y se almacena en la tabla notifications', function () {
    $this->adminUser->notify(new AdminNotification(
        titulo: 'Test',
        mensaje: 'Mensaje de prueba',
        tipo: 'info',
    ));

    $notification = $this->adminUser->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->type)->toBe(AdminNotification::class);
});

it('AdminNotification envia por canal database', function () {
    $notification = new AdminNotification(
        titulo: 'Test',
        mensaje: 'Mensaje de prueba',
        tipo: 'info',
    );

    $channels = $notification->via($this->adminUser);

    expect($channels)->toBe(['database']);
});

it('AdminNotification retorna array con titulo mensaje tipo', function () {
    $notification = new AdminNotification(
        titulo: 'Test Title',
        mensaje: 'Test Body',
        tipo: 'warning',
    );

    $array = $notification->toArray($this->adminUser);

    expect($array)->toHaveKeys(['titulo', 'mensaje', 'tipo']);
    expect($array['titulo'])->toBe('Test Title');
    expect($array['mensaje'])->toBe('Test Body');
    expect($array['tipo'])->toBe('warning');
});

it('el super admin puede enviar notificaciones a todos los usuarios admin', function () {
    Notification::fake();

    $usuariosAdmin = User::factory()->count(3)->create(['is_super_admin' => false]);
    foreach ($usuariosAdmin as $usuario) {
        $usuario->empresas()->attach($this->empresa->id, ['role' => 'admin']);
    }

    $usuariosAdmin->each(function (User $user) {
        $user->notify(new AdminNotification(
            titulo: 'Notificación del Super Admin',
            mensaje: 'Este es un mensaje de prueba',
            tipo: 'info',
        ));
    });

    foreach ($usuariosAdmin as $usuario) {
        Notification::assertSentTo(
            $usuario,
            AdminNotification::class,
        );
    }
});

it('AdminNotification retorna notificacion Filament con tipo correcto', function () {
    $notification = new AdminNotification(
        titulo: 'Test',
        mensaje: 'Mensaje de prueba',
        tipo: 'success',
    );

    $filamentNotification = $notification->toFilament($this->adminUser);

    expect($filamentNotification)->toBeInstanceOf(Filament\Notifications\Notification::class);
});

it('guarda historial de notificacion enviada', function () {
    NotificacionHistorial::create([
        'titulo' => 'Test Historial',
        'mensaje' => 'Mensaje de prueba',
        'tipo' => 'info',
        'destino' => 'todos',
        'empresas_ids' => null,
        'cantidad_usuarios' => 5,
        'enviada_por' => $this->superAdmin->id,
    ]);

    $historial = NotificacionHistorial::first();

    expect($historial)->not->toBeNull();
    expect($historial->titulo)->toBe('Test Historial');
    expect($historial->cantidad_usuarios)->toBe(5);
    expect($historial->autor->id)->toBe($this->superAdmin->id);
});

it('el historial muestra las empresas destino cuando es por_empresa', function () {
    $empresa2 = Empresa::factory()->create();

    NotificacionHistorial::create([
        'titulo' => 'Test Por Empresa',
        'mensaje' => 'Mensaje de prueba',
        'tipo' => 'warning',
        'destino' => 'por_empresa',
        'empresas_ids' => [$this->empresa->id, $empresa2->id],
        'cantidad_usuarios' => 10,
        'enviada_por' => $this->superAdmin->id,
    ]);

    $historial = NotificacionHistorial::first();

    expect($historial->empresas_ids)->toBe([$this->empresa->id, $empresa2->id]);
    expect($historial->destino)->toBe('por_empresa');
});
