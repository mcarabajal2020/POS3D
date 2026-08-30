<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class NotificacionesPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notificaciones';

    protected static ?string $title = 'Notificaciones';

    protected static UnitEnum|string|null $navigationGroup = 'Operaciones';

    protected static ?int $navigationSort = 10;

    public ?array $notifications = [];

    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $user = Auth::user();

        $this->notifications = $user->notifications()
            ->latest()
            ->take(50)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ])
            ->toArray();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        Auth::user()->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        $this->loadNotifications();
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
