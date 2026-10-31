<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-purple-400 bg-purple-500/10 px-2.5 py-0.5 rounded-full border border-purple-500/20">
                    Control de Calidad, Cumplimiento & Seguridad (Clase 12)
                </span>
                <h1 class="font-black text-2xl text-white tracking-tight mt-1">
                    Bitácora de Auditoría del Sistema & Seguridad
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">
                    Registro forense inmutable de cambios en 5 modelos (Antes → Después) y eventos de seguridad
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    ← Volver al Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Resumen de Métricas de Auditoría -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 shadow-lg">
                    <span class="text-xs font-medium text-slate-400 block">Total Eventos Registrados</span>
                    <p class="text-2xl font-black text-white mt-1 font-mono">{{ $totalActivities }}</p>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Historial completo con Spatie ActivityLog</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-indigo-500/20 shadow-lg">
                    <span class="text-xs font-medium text-indigo-400 block">Eventos de Negocio</span>
                    <p class="text-2xl font-black text-indigo-300 mt-1 font-mono">{{ $totalBusiness }}</p>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Productos, Compras, Ventas, Proveedores y Clientes</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-rose-500/20 shadow-lg">
                    <span class="text-xs font-medium text-rose-400 block">Bitácora de Seguridad</span>
                    <p class="text-2xl font-black text-rose-300 mt-1 font-mono">{{ $totalSecurity }}</p>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Inicios de sesión, fallos y autenticación</span>
                </div>
            </div>

            <!-- Panel Principal con Filtros y Tabla -->
            <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
                <!-- Toolbar y Filtros -->
                <div class="p-5 border-b border-slate-800/80 bg-slate-950/60">
                    <form method="GET" action="{{ route('audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Módulo Auditado</label>
                            <select name="log_name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Todos los módulos</option>
                                @foreach ($modules as $mod)
                                    <option value="{{ $mod }}" {{ request('log_name') === $mod ? 'selected' : '' }}>
                                        {{ ucfirst($mod) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Usuario Responsable</label>
                            <select name="user_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Todos los usuarios</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Desde</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Hasta</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="w-full py-2 px-3 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl transition shadow-md">
                                Filtrar
                            </button>
                            @if(request()->hasAny(['log_name', 'user_id', 'from', 'to', 'search']))
                                <a href="{{ route('audit.index') }}" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition" title="Limpiar filtros">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Tabla de Auditoría (Antes -> Después) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-4">Fecha / Hora</th>
                                <th class="py-3 px-4">Módulo</th>
                                <th class="py-3 px-4">Acción / Descripción</th>
                                <th class="py-3 px-4">Responsable</th>
                                <th class="py-3 px-4">Trazabilidad de Cambios (Antes → Después)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300 font-medium">
                            @forelse ($activities as $act)
                                <tr class="hover:bg-slate-800/30 transition">
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-slate-400">
                                        {{ $act->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($act->log_name === 'seguridad')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                🔒 Seguridad
                                            </span>
                                        @elseif ($act->log_name === 'compras')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                                                📦 Compras
                                            </span>
                                        @elseif ($act->log_name === 'ventas')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                💰 Ventas
                                            </span>
                                        @elseif ($act->log_name === 'productos')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-400 border border-purple-500/30">
                                                🏷 Productos
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                                ● {{ ucfirst($act->log_name) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-white">
                                        {{ $act->description }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs text-slate-400">
                                        {{ $act->causer?->name ?? 'Sistema / Externo' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs font-mono">
                                        @php
                                            $changes = $act->attribute_changes ?: $act->properties;
                                            $old = $changes['old'] ?? null;
                                            $attributes = $changes['attributes'] ?? null;
                                        @endphp

                                        @if ($old && $attributes)
                                            <div class="space-y-1">
                                                @foreach ($attributes as $key => $newVal)
                                                    @if (array_key_exists($key, $old) && $old[$key] != $newVal)
                                                        <div class="text-[11px] bg-slate-950/60 p-1.5 rounded border border-slate-800">
                                                            <strong class="text-slate-400">{{ $key }}:</strong>
                                                            <span class="text-rose-400 line-through">{{ is_bool($old[$key]) ? ($old[$key] ? 'true' : 'false') : $old[$key] }}</span>
                                                            <span class="text-slate-500 mx-1">→</span>
                                                            <span class="text-emerald-400 font-bold">{{ is_bool($newVal) ? ($newVal ? 'true' : 'false') : $newVal }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @elseif ($attributes)
                                            <span class="text-slate-400 text-[11px]">
                                                Valores iniciales registrados: 
                                                <code class="text-indigo-300 font-mono">{{ json_encode($attributes, JSON_UNESCAPED_UNICODE) }}</code>
                                            </span>
                                        @elseif ($act->properties->isNotEmpty())
                                            <span class="text-slate-500 text-[11px]">
                                                {{ json_encode($act->properties, JSON_UNESCAPED_UNICODE) }}
                                            </span>
                                        @else
                                            <span class="text-slate-600 text-[11px]">Sin cambios en atributos</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400 text-sm">
                                        No hay registros de auditoría para los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
                    {{ $activities->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
