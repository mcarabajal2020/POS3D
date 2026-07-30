<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Rezadaulay\FilamentWhatsappNotification\FilamentWhatsappNotificationServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    FilamentWhatsappNotificationServiceProvider::class,
];
