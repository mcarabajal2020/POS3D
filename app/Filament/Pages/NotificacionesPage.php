<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificacionesPage extends Page
{
    protected static ?string $navigationLabel = 'Notificaciones';

    protected static ?string $title = 'Notificaciones';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.notificaciones-page';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bell';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Operaciones';
    }

    public function getNotifications(): Collection
    {
        return Auth::user()->notifications()->latest()->take(50)->get();
    }

    public function getUnreadCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        Auth::user()->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function getColor(string $color): string
    {
        return match ($color) {
            'success' => 'success',
            'danger' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => 'gray',
        };
    }
}
