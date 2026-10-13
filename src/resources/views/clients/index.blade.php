<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-black text-2xl text-white tracking-tight">
                    Directorio de Clientes {{ $showTrashed ? '— Papelera de Reciclaje' : '' }}
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Gestión de cuentas comerciales, datos de facturación e historial transaccional</p>
            </div>
            <div class="flex items-center gap-2">
                @can('crear-clientes')
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo Cliente
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
                <!-- Toolbar y Filtros -->
                <div class="p-5 border-b border-slate-800/80 bg-slate-950/60 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        @can('eliminar-clientes')
                            @if ($showTrashed)
                                <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-200 transition">
                                    ← Volver a clientes activos
                                </a>
                            @else
                                <a href="{{ route('clients.index', ['trashed' => 1]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-850 border border-slate-800 hover:bg-rose-500/10 hover:text-rose-300 hover:border-rose-500/30 text-slate-400 transition">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Ver Papelera ({{ $trashedCount }})
                                </a>
                            @endif
                        @endcan
                    </div>

                    <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap items-center gap-2">
                        @if ($showTrashed)
                            <input type="hidden" name="trashed" value="1">
                        @endif

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, doc o email..." class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 px-3 py-2 w-64">

                        <select name="status" class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs px-3 py-2 focus:border-indigo-500">
                            <option value="">Todos los estados</option>
                            <option value="active" @selected(request('status') === 'active')>Activos</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                        </select>

                        <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition">
                            Filtrar
                        </button>
                    </form>
                </div>

                <!-- Tabla de Clientes -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-[11px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-800/80">
                            <tr>
                                <th class="py-4 px-5">Documento</th>
                                <th class="py-4 px-5">Nombre / Razón Social</th>
                                <th class="py-4 px-5">Correo</th>
                                <th class="py-4 px-5">Teléfono</th>
                                <th class="py-4 px-5 text-center">Ventas</th>
                                <th class="py-4 px-5">Estado</th>
                                <th class="py-4 px-5 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                            @forelse ($clients as $client)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="py-4 px-5 font-mono font-bold text-indigo-300">
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">{{ $client->document_type }}</span>
                                        <span class="ml-1">{{ $client->document_number }}</span>
                                    </td>
                                    <td class="py-4 px-5 font-bold text-white text-sm">
                                        {{ $client->name }}
                                        @if($client->address)
                                            <span class="text-[11px] text-slate-400 block font-normal">📍 {{ $client->address }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-slate-300 font-mono text-[11px]">
                                        {{ $client->email ?? 'S/N' }}
                                    </td>
                                    <td class="py-4 px-5 text-slate-300">
                                        {{ $client->phone ?? 'S/N' }}
                                    </td>
                                    <td class="py-4 px-5 text-center font-bold text-indigo-400">
                                        {{ $client->sales_count }}
                                    </td>
                                    <td class="py-4 px-5">
                                        @if($client->active)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                                Activo
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if ($client->trashed())
                                                <form method="POST" action="{{ route('clients.restore', $client) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-950/60 hover:bg-emerald-900 text-emerald-300 border border-emerald-800 text-xs font-bold transition">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @else
                                                @can('editar-clientes')
                                                    <a href="{{ route('clients.edit', $client) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition" title="Editar">
                                                        ✏️
                                                    </a>
                                                @endcan

                                                @can('eliminar-clientes')
                                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" class="inline" onsubmit="return confirm('¿Enviar este cliente a la papelera?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-950/40 hover:bg-rose-900 text-rose-400 border border-rose-900/50 transition" title="Eliminar">
                                                            🗑️
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                                        No hay clientes registrados en esta vista.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                    <div class="p-4 bg-slate-950/60 border-t border-slate-800/80">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
