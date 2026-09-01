<x-filament-panels::page>
    @php
        $notifications = $this->getNotifications();
        $unreadCount = $this->getUnreadCount();
    @endphp

    @if ($unreadCount > 0)
        <div class="mb-6">
            <x-filament::button wire:click="markAllAsRead" size="sm">
                Marcar todas como leídas ({{ $unreadCount }})
            </x-filament::button>
        </div>
    @endif

    @if ($notifications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-500 dark:text-gray-400">
            <x-heroicon-o-bell class="mb-3 h-12 w-12 opacity-40" />
            <p class="text-lg font-medium">No tienes notificaciones</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isRead = !is_null($notification->read_at);
                    $color = $data['color'] ?? 'gray';
                @endphp
                <div wire:click="markAsRead('{{ $notification->id }}')"
                     class="relative cursor-pointer overflow-hidden rounded-2xl border shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg
                         @if($isRead)
                             border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900/50 hover:border-gray-300 dark:hover:border-gray-600
                         @else
                             border-amber-300 bg-amber-50 dark:border-amber-600 dark:bg-amber-900/20 hover:border-amber-400 dark:hover:border-amber-500
                         @endif">

                    <div class="flex items-start gap-4 p-5">
                        <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                            @if($color === 'warning') bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400
                            @elseif($color === 'success') bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400
                            @elseif($color === 'danger') bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400
                            @elseif($color === 'info') bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400
                            @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                            @endif">
                            <x-heroicon-o-bell class="h-5 w-5" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="truncate text-sm font-semibold
                                    @if($isRead) text-gray-600 dark:text-gray-400
                                    @else text-gray-900 dark:text-white
                                    @endif">
                                    {{ $data['title'] ?? 'Notificación' }}
                                </h3>
                                @if(!$isRead)
                                    <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500"></span>
                                @endif
                            </div>

                            @if(!empty($data['body']))
                                <p class="mt-1 text-sm leading-relaxed
                                    @if($isRead) text-gray-500 dark:text-gray-500
                                    @else text-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ $data['body'] }}
                                </p>
                            @endif

                            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                                @if($isRead)
                                    <span class="ml-1 text-gray-400">· Leída</span>
                                @else
                                    <span class="ml-1 font-medium text-amber-600 dark:text-amber-400">· No leída</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if(!$isRead)
                        <div class="absolute inset-y-0 left-0 w-1 bg-amber-500"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
