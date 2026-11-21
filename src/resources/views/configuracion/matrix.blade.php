<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-300 bg-emerald-500/15 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                    Configuración · Matriz RBAC
                </span>
                <h1 class="font-black text-2xl text-white tracking-tight mt-1">
                    Matriz de Roles × Permisos ({{ $totalPermissions }} permisos)
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    El rol <strong class="text-white">admin</strong> conserva control total permanente. Todos los cambios se registran en la bitácora de seguridad.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @can('gestionar-usuarios')
                    <a href="{{ route('configuracion.users.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                        👥 Usuarios
                    </a>
                @endcan
                <a href="{{ route('configuracion.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    ← Configuración
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

            <!-- Creación rápida de Rol y Permiso -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form method="POST" action="{{ route('configuracion.roles.store') }}" class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700 flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <label for="role_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1.5">Crear Nuevo Rol</label>
                        <input id="role_name" type="text" name="name" placeholder="ej: supervisor_caja" required
                               class="w-full bg-slate-950 border border-slate-700 text-white placeholder-slate-400 rounded-xl px-3 py-2 text-xs">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition">
                        + Crear Rol
                    </button>
                </form>

                @can('gestionar-permisos')
                    <form method="POST" action="{{ route('configuracion.permissions.store') }}" class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700 flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label for="perm_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1.5">Crear Nuevo Permiso (kebab-case)</label>
                            <input id="perm_name" type="text" name="name" placeholder="ej: aprobar-descuentos" required
                                   class="w-full bg-slate-950 border border-slate-700 text-white placeholder-slate-400 rounded-xl px-3 py-2 text-xs">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                            + Crear Permiso
                        </button>
                    </form>
                @endcan
            </div>

            <!-- Matriz Roles x Permisos -->
            <form method="POST" action="{{ route('configuracion.matrix.update') }}" class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                @csrf
                @method('PUT')

                <div class="p-4 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-300">
                        Marca los permisos permitidos para cada rol y presiona <strong class="text-white">Guardar Matriz</strong>.
                    </span>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition">
                        Guardar Matriz
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950 text-slate-200 uppercase font-bold text-[10px] tracking-wider border-b border-slate-800 sticky top-0">
                            <tr>
                                <th class="py-3 px-4">Módulo / Permiso</th>
                                @foreach ($roles as $role)
                                    <th class="py-3 px-3 text-center whitespace-nowrap">
                                        <div class="capitalize text-white font-bold">{{ $role->name }}</div>
                                        <div class="text-[10px] font-normal text-slate-400">{{ $role->users_count }} usr</div>
                                    </th>
                                @endforeach
                                @can('gestionar-permisos')
                                    <th class="py-3 px-3 text-right">Acción</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            @foreach ($permissionGroups as $groupName => $perms)
                                <tr class="bg-slate-950/70">
                                    <td colspan="{{ $roles->count() + 2 }}" class="py-2 px-4 text-[11px] font-bold uppercase tracking-wider text-indigo-300">
                                        {{ $groupName }}
                                    </td>
                                </tr>
                                @foreach ($perms as $permission)
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="py-2.5 px-4 font-mono text-xs text-slate-200 whitespace-nowrap">
                                            {{ $permission->name }}
                                        </td>
                                        @foreach ($roles as $role)
                                            @php
                                                $isChecked = $role->name === 'admin' || $role->permissions->contains('id', $permission->id);
                                            @endphp
                                            <td class="py-2.5 px-3 text-center">
                                                @if ($role->name === 'admin')
                                                    <input type="checkbox" checked disabled title="El rol admin siempre tiene todos los permisos"
                                                           class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-emerald-500 opacity-75 cursor-not-allowed">
                                                @else
                                                    <input type="checkbox"
                                                           name="matrix[{{ $role->id }}][]"
                                                           value="{{ $permission->name }}"
                                                           @checked($isChecked)
                                                           class="w-4 h-4 rounded bg-slate-950 border-slate-600 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0">
                                                @endif
                                            </td>
                                        @endforeach
                                        @can('gestionar-permisos')
                                            <td class="py-2.5 px-3 text-right">
                                                @php
                                                    $isProtected = in_array($permission->name, $protectedPermissions ?? [], true);
                                                    $inUse = $permission->roles->where('name', '!=', 'admin')->isNotEmpty() || $permission->users->isNotEmpty();
                                                @endphp
                                                @if ($isProtected)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 text-[10px] font-semibold">
                                                        Sistema
                                                    </span>
                                                @elseif (! $inUse)
                                                    <button type="submit" form="delete-perm-{{ $permission->id }}" class="px-2 py-0.5 rounded bg-rose-500/15 text-rose-300 hover:bg-rose-500/25 border border-rose-500/30 text-[10px] font-bold">
                                                        Eliminar
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-slate-400">En uso</span>
                                                @endif
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex justify-end">
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition">
                        Guardar Matriz
                    </button>
                </div>
            </form>

            @can('gestionar-permisos')
                @foreach ($permissionGroups->flatten() as $permission)
                    @if (! in_array($permission->name, $protectedPermissions ?? [], true) && $permission->roles->where('name', '!=', 'admin')->isEmpty() && $permission->users->isEmpty())
                        <form id="delete-perm-{{ $permission->id }}" method="POST" action="{{ route('configuracion.permissions.destroy', $permission) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                @endforeach
            @endcan

            <!-- Gestión de Roles existentes (eliminación segura) -->
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                <h2 class="text-sm font-bold text-white">Roles Registrados</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach ($roles as $role)
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-xs text-slate-200">
                            <span class="font-bold capitalize">{{ $role->name }}</span>
                            <span class="text-[10px] text-slate-400">({{ $role->users_count }} usr)</span>
                            @if ($role->name !== 'admin' && $role->users_count === 0)
                                <form method="POST" action="{{ route('configuracion.roles.destroy', $role) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold ml-1" title="Eliminar rol sin usuarios">✕</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
