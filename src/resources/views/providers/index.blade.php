<x-app-layout>
    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wider uppercase bg-blue-500/10 text-blue-400 border border-blue-500/20">Cadena de Abastecimiento</span>
                        @if ($showTrashed)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Papelera de Reciclaje</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-black text-white tracking-tight mt-1">Directorio de Proveedores</h1>
                    <p class="text-sm text-slate-400">Gestión de distribuidores, laboratorios y aliados comerciales del ERP.</p>
                </div>

                <div class="flex items-center gap-3">
                    @can('crear-proveedores')
                        <a href="{{ route('providers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nuevo Proveedor
                        </a>
                    @endcan

                    @can('eliminar-proveedores')
                        @if ($showTrashed)
                            <a href="{{ route('providers.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-sm font-medium border border-slate-700 transition">
                                Ver Activos
                            </a>
                        @else
                            <a href="{{ route('providers.index', ['trashed' => 1]) }}" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-sm font-medium border border-slate-700 transition flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Papelera ({{ $trashedCount }})
                            </a>
                        @endif
                    @endcan
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 shadow-xl backdrop-blur-sm">
                <form method="GET" action="{{ route('providers.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif
                    <div class="sm:col-span-7">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por razón social, NIT o persona de contacto..." 
                            class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                    </div>
                    <div class="sm:col-span-3">
                        <select name="status" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            <option value="">Todos los estados</option>
                            <option value="active" @selected(request('status') === 'active')>Solo Activos</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Solo Inactivos</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold border border-slate-700 transition">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-slate-800/80">
                        <thead class="bg-slate-950/60">
                            <tr class="text-left text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3.5 px-4">Documento</th>
                                <th class="py-3.5 px-4">Razón Social</th>
                                <th class="py-3.5 px-4">Contacto</th>
                                <th class="py-3.5 px-4">Teléfono</th>
                                <th class="py-3.5 px-4 text-center">Compras</th>
                                <th class="py-3.5 px-4 text-center">Estado</th>
                                <th class="py-3.5 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-200">
                            @forelse ($providers as $provider)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-bold me-1.5">{{ $provider->document_type }}</span>
                                        {{ $provider->document_number }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-white">
                                        {{ $provider->name }}
                                        @if ($provider->address)
                                            <div class="text-xs text-slate-400 font-normal">{{ $provider->address }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        {{ $provider->contact_name ?? '—' }}
                                        @if ($provider->email)
                                            <div class="text-xs text-slate-500">{{ $provider->email }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs text-slate-300">{{ $provider->phone ?? '—' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $provider->purchases_count > 0 ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/30' : 'bg-slate-800 text-slate-400' }}">
                                            {{ $provider->purchases_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $provider->active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            {{ $provider->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                        @if ($provider->trashed())
                                            <form method="POST" action="{{ route('providers.restore', $provider) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-emerald-400 hover:text-emerald-300 font-semibold text-xs transition">Restaurar</button>
                                            </form>
                                        @else
                                            @can('editar-proveedores')
                                                <a href="{{ route('providers.edit', $provider) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold text-xs transition">Editar</a>
                                            @endcan
                                            @can('eliminar-proveedores')
                                                <form method="POST" action="{{ route('providers.destroy', $provider) }}" class="inline"
                                                    onsubmit="return confirm('¿Enviar este proveedor a la papelera?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs transition">Eliminar</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">No hay proveedores registrados para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($providers->hasPages())
                    <div class="p-4 border-t border-slate-800">
                        {{ $providers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
