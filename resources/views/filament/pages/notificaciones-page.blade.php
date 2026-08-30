<x-filament-panels::page>
    @if ($unreadCount > 0)
        <div class="mb-4">
            <x-filament::button wire:click="markAllAsRead" size="sm">
                Marcar todas como leídas ({{ $unreadCount }})
            </x-filament::button>
        </div>
    @endif

    @if (empty($notifications))
        <div class="flex items-center justify-center py-12 text-gray-500 dark:text-gray-400">
            <x-heroicon-o-bell class="mr-2 h-6 w-6" />
            No tienes notificaciones.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($notifications as $notification)
                <div wire:click="markAsRead('{{ $notification['id'] }}')"
                     class="cursor-pointer rounded-xl border {{ $notification['read_at'] ? 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/50' : 'border-primary-200 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20' }} p-4 transition hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold {{ $notification['read_at'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-900 dark:text-white' }}">
                                {{ $notification['data']['title'] ?? 'Notificación' }}
                            </h3>
                            @if (!empty($notification['data']['body']))
                                <p class="mt-1 text-sm {{ $notification['read_at'] ? 'text-gray-500 dark:text-gray-400' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $notification['data']['body'] }}
                                </p>
                            @endif
                        </div>
                        @if (!empty($notification['data']['color']))
                            <x-filament::badge :color="$this->getColor($notification['data']['color'])" size="sm">
                                {{ $notification['data']['color'] }}
                            </x-filament::badge>
                        @endif
                    </div>
                    <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                        @if ($notification['read_at'])
                            · Leída
                        @else
                            · <span class="font-medium text-primary-600">No leída</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
