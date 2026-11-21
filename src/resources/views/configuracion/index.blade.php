<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-300 bg-indigo-500/15 px-2.5 py-0.5 rounded-full border border-indigo-500/30">
                    Administración & Gobierno RBAC
                </span>
                <h1 class="font-black text-2xl text-white tracking-tight mt-1">
                    Módulo de Configuración y Control de Acceso
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Gestión de usuarios, activación de cuentas, asignación multirrol, permisos directos y matriz de seguridad
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    ← Volver al Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-lg">
                    <span class="text-xs font-medium text-slate-300 block">Total Usuarios</span>
                    <p class="text-2xl font-black text-white mt-1 font-mono">{{ $usersCount }}</p>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Cuentas registradas en el ERP</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/80 border border-emerald-500/30 shadow-lg">
                    <span class="text-xs font-medium text-emerald-300 block">Usuarios Activos</span>
                    <p class="text-2xl font-black text-emerald-300 mt-1 font-mono">{{ $activeUsersCount }}</p>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Inactivos bloqueados: {{ $inactiveUsersCount }}</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/80 border border-indigo-500/30 shadow-lg">
                    <span class="text-xs font-medium text-indigo-300 block">Roles Definidos</span>
                    <p class="text-2xl font-black text-indigo-300 mt-1 font-mono">{{ $rolesCount }}</p>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Perfiles RBAC en guardia web</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/80 border border-amber-500/30 shadow-lg">
                    <span class="text-xs font-medium text-amber-300 block">Permisos Granulares</span>
                    <p class="text-2xl font-black text-amber-300 mt-1 font-mono">{{ $permissionsCount }}</p>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Capacidades auditadas por módulo</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @can('gestionar-usuarios')
                    <a href="{{ route('configuracion.users.index') }}" class="group block p-6 rounded-2xl bg-slate-800/70 border border-slate-700/80 hover:border-indigo-500/60 hover:bg-slate-800 transition shadow-xl">
                        <div class="flex items-start justify-between">
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 flex items-center justify-center font-black text-lg">
                                👥
                            </div>
                            <span class="text-xs font-bold text-indigo-300 group-hover:translate-x-1 transition">Administrar →</span>
                        </div>
                        <h2 class="text-lg font-bold text-white mt-4">Gestión de Usuarios</h2>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Crear y editar usuarios, activar o desactivar cuentas, asignar múltiples roles de trabajo y permisos directos excepcionales.
                        </p>
                    </a>
                @endcan

                @can('gestionar-roles')
                    <a href="{{ route('configuracion.matrix') }}" class="group block p-6 rounded-2xl bg-slate-800/70 border border-slate-700/80 hover:border-emerald-500/60 hover:bg-slate-800 transition shadow-xl">
                        <div class="flex items-start justify-between">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 flex items-center justify-center font-black text-lg">
                                🛡️
                            </div>
                            <span class="text-xs font-bold text-emerald-300 group-hover:translate-x-1 transition">Configurar →</span>
                        </div>
                        <h2 class="text-lg font-bold text-white mt-4">Matriz Roles × Permisos</h2>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Control visual de capacidades por rol agrupadas por módulo, creación de nuevos roles/permisos y protección del rol administrador.
                        </p>
                    </a>
                @endcan
            </div>

        </div>
    </div>
</x-app-layout>
