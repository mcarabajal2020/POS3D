<?php

use App\Models\Empresa;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function seedRolesAndPermissions(): void
{
    $roles = ['super_admin', 'admin', 'vendedor', 'cajero'];
    foreach ($roles as $roleName) {
        Role::findOrCreate($roleName, 'web');
    }

    $permissionNames = [
        'view_any_cliente', 'view_cliente', 'create_cliente', 'update_cliente', 'delete_cliente',
        'view_any_venta', 'view_venta', 'create_venta', 'update_venta', 'delete_venta',
        'view_any_articulo', 'view_articulo', 'create_articulo', 'update_articulo', 'delete_articulo',
        'view_any_empresa', 'view_empresa', 'create_empresa', 'update_empresa', 'delete_empresa',
        'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
        'view_dashboard',
    ];

    foreach ($permissionNames as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $allPermissions = Permission::where('guard_name', 'web')->get();
    Role::findByName('super_admin', 'web')->syncPermissions($allPermissions);

    $vendedorPermissions = Permission::where('guard_name', 'web')
        ->whereIn('name', [
            'view_any_cliente', 'view_cliente',
            'view_any_venta', 'view_venta', 'create_venta',
            'view_any_articulo', 'view_articulo',
            'view_dashboard',
        ])->get();
    Role::findByName('vendedor', 'web')->syncPermissions($vendedorPermissions);
}

beforeEach(function () {
    seedRolesAndPermissions();

    $this->empresa = Empresa::factory()->create();
    $this->user = User::factory()->create();
    $this->user->empresas()->attach($this->empresa->id, ['role' => 'admin']);
    session(['empresa_id' => $this->empresa->id]);
});

it('crea los roles basicos del sistema', function () {
    $roles = ['super_admin', 'admin', 'vendedor', 'cajero'];

    foreach ($roles as $roleName) {
        expect(Role::where('name', $roleName)->where('guard_name', 'web')->exists())->toBeTrue();
    }
});

it('asigna rol super_admin a usuario', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $superAdminRole = Role::findByName('super_admin', 'web');

    $user->assignRole($superAdminRole);

    expect($user->hasRole('super_admin'))->toBeTrue();
    expect($user->isSuperAdmin())->toBeTrue();
});

it('asigna rol admin a usuario', function () {
    $adminRole = Role::findByName('admin', 'web');

    $this->user->syncRoles([$adminRole]);

    expect($this->user->hasRole('admin'))->toBeTrue();
});

it('verifica permisos de super_admin', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $superAdminRole = Role::findByName('super_admin', 'web');

    $user->assignRole($superAdminRole);

    expect($user->can('view_any_cliente'))->toBeTrue();
    expect($user->can('create_venta'))->toBeTrue();
    expect($user->can('delete_articulo'))->toBeTrue();
});

it('verifica que vendedor no puede eliminar', function () {
    $vendedorRole = Role::findByName('vendedor', 'web');
    $this->user->syncRoles([$vendedorRole]);

    expect($this->user->can('delete_cliente'))->toBeFalse();
    expect($this->user->can('delete_venta'))->toBeFalse();
});

it('verifica que vendedor puede ver y crear ventas', function () {
    $vendedorRole = Role::findByName('vendedor', 'web');
    $this->user->syncRoles([$vendedorRole]);

    expect($this->user->can('view_any_venta'))->toBeTrue();
    expect($this->user->can('create_venta'))->toBeTrue();
});

it('is_super_admin retorna false para usuario normal', function () {
    expect($this->user->isSuperAdmin())->toBeFalse();
});

it('el trait HasRoles funciona correctamente', function () {
    $this->user->assignRole('vendedor');

    expect($this->user->hasRole('vendedor'))->toBeTrue();
    expect($this->user->hasRole('admin'))->toBeFalse();
});

it('puede revocar permisos de un rol', function () {
    $vendedorRole = Role::findByName('vendedor', 'web');

    $vendedorRole->revokePermissionTo('delete_cliente');

    $this->user->syncRoles([$vendedorRole]);
    expect($this->user->can('delete_cliente'))->toBeFalse();
});
