<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight flex items-center gap-2">
                <span>🔔</span> Notificaciones del Sistema
            </h2>
            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-semibold transition">
                        ✓ Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-900 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-slate-800/90 border border-slate-700/80 shadow-xl rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                    <p class="text-xs text-slate-400">
                        Historial de alertas operativas, recepción de compras y eventos de inventario
                    </p>
                    <span class="text-xs font-mono font-bold text-slate-400">
                        {{ auth()->user()->unreadNotifications()->count() }} no leídas
                    </span>
                </div>

                <ul class="divide-y divide-slate-700/60">
                    @forelse ($notifications as $notification)
                        <li class="py-4 flex items-start justify-between gap-4 transition duration-150 {{ $notification->read_at ? 'opacity-60' : 'bg-slate-800/30 -mx-2 px-2 rounded-xl' }}">
                            <div class="space-y-1">
                                <p class="font-semibold text-sm text-white flex items-center gap-2">
                                    @unless ($notification->read_at)
                                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-500 animate-pulse" title="No leída"></span>
                                    @endunless
                                    {{ $notification->data['title'] ?? 'Notificación' }}
                                </p>
                                <p class="text-xs text-slate-300 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-[11px] text-slate-500 font-mono">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 border border-indigo-500/30 whitespace-nowrap transition">
                                    Abrir →
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="py-12 text-center text-slate-400 text-sm">
                            <div class="text-3xl mb-2">📭</div>
                            No tienes notificaciones registradas.
                        </li>
                    @endforelse
                </ul>

                @if ($notifications->hasPages())
                    <div class="pt-4 border-t border-slate-700/60">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
