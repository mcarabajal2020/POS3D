<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('super-admin')
            ->path('super-admin')
            ->login()
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/SuperAdmin/Resources'), for: 'App\Filament\SuperAdmin\Resources')
            ->discoverPages(in: app_path('Filament/SuperAdmin/Pages'), for: 'App\Filament\SuperAdmin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SuperAdmin/Widgets'), for: 'App\Filament\SuperAdmin\Widgets')
            ->navigationGroups([
                'Empresas',
                'Herramientas',
                'Facturación',
                'Usuarios',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => '<style>.fi-sidebar{background-color:#dbeafe}.dark .fi-sidebar{background-color:rgb(17 24 39)}.fi-body-has-topbar .fi-sidebar-header{background-color:#dbeafe}.dark .fi-body-has-topbar .fi-sidebar-header{background-color:rgb(17 24 39)}.fi-sidebar-item-label{color:#1e3a5f}.dark .fi-sidebar-item-label{color:rgb(209 213 219)}.fi-sidebar-item-active .fi-sidebar-item-icon{color:#1e3a5f}.dark .fi-sidebar-item-active .fi-sidebar-item-icon{color:rgb(96 165 250)}.fi-sidebar-item-active{background-color:#bfdbfe}.dark .fi-sidebar-item-active{background-color:rgba(255 255 255 / 0.05)}.fi-sidebar-group-label{color:#1e3a5f}.dark .fi-sidebar-group-label{color:rgb(156 163 175)}</style>',
            );
    }
}
