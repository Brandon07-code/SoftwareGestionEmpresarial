<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-300 bg-indigo-500/15 px-2.5 py-0.5 rounded-full border border-indigo-500/30">
                    Configuración · Usuarios
                </span>
                <h1 class="font-black text-2xl text-white tracking-tight mt-1">
                    Administración de Usuarios y Roles
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Control de cuentas activas, asignación multirrol y permisos directos
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @can('gestionar-roles')
                    <a href="{{ route('configuracion.matrix') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                        🛡️ Matriz Roles × Permisos
                    </a>
                @endcan
                <a href="{{ route('configuracion.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition">
                    + Nuevo Usuario
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-semibold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-5 border-b border-slate-800 bg-slate-950/60">
                    <form method="GET" action="{{ route('configuracion.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, correo o teléfono..." class="bg-slate-900 border border-slate-700 text-white placeholder-slate-400 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">

                        <select name="role" class="bg-slate-900 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">Todos los roles</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ ucfirst($r->name) }}</option>
                            @endforeach
                        </select>

                        <select name="estado" class="bg-slate-900 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">Todos los estados</option>
                            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                            <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                        </select>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition">
                                Filtrar
                            </button>
                            @if (request()->hasAny(['search', 'role', 'estado']))
                                <a href="{{ route('configuracion.users.index') }}" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-300 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-4">Usuario</th>
                                <th class="py-3 px-4">Empresa</th>
                                <th class="py-3 px-4">Roles Asignados</th>
                                <th class="py-3 px-4">Permisos Directos</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70 text-slate-200">
                            @forelse ($users as $u)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-white text-sm">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-300 font-mono">{{ $u->email }}</div>
                                        @if ($u->telefono)
                                            <div class="text-[11px] text-slate-400">Tel: {{ $u->telefono }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-300">
                                        {{ $u->empresa?->nombre_comercial ?? 'Sin empresa' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($u->roles as $role)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 capitalize">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                                    Sin rol
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if ($u->permissions->isNotEmpty())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                {{ $u->permissions->count() }} directo(s)
                                            </span>
                                        @else
                                            <span class="text-[11px] text-slate-400">Ninguno</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($u->activo)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                                ● Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                                ● Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                                        <a href="{{ route('configuracion.users.edit', $u) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 border border-slate-700 transition">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('configuracion.users.toggle', $u) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $u->activo ? 'bg-rose-500/15 text-rose-300 hover:bg-rose-500/25 border border-rose-500/30' : 'bg-emerald-500/15 text-emerald-300 hover:bg-emerald-500/25 border border-emerald-500/30' }} transition">
                                                {{ $u->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-300 text-sm">
                                        No se encontraron usuarios con los criterios aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
