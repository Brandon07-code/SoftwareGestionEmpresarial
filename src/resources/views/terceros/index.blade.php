@extends('layouts.app')

@section('title', 'Directorio de Terceros - Party Model ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado del Módulo -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Directorio Unificado de Terceros</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">Party Model</span>
            </div>
            <p class="text-sm text-slate-400 mt-0.5 font-medium">
                Gestión integral de actores empresariales: una sola entidad con múltiples roles (Cliente, Proveedor, Empleado).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('terceros.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                <span>+ Registrar Tercero</span>
            </a>
        </div>
    </div>

    <!-- Pestañas de Filtrado por Rol de Negocio -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-800/80 pb-3">
        <a href="{{ route('terceros.index', ['filtro' => 'todos']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'todos' ? 'bg-slate-800 text-white shadow-md border border-slate-700' : 'bg-slate-900/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
            <span>👥 Todos</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'todos' ? 'bg-slate-700 text-white' : 'bg-slate-800 text-slate-300' }}">{{ $conteoTotal }}</span>
        </a>

        <a href="{{ route('terceros.index', ['filtro' => 'clientes']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'clientes' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 border border-indigo-500' : 'bg-slate-900/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
            <span>⭐ Clientes</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'clientes' ? 'bg-indigo-700 text-white' : 'bg-slate-800 text-slate-300' }}">{{ $conteoClientes }}</span>
        </a>

        <a href="{{ route('terceros.index', ['filtro' => 'empleados']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'empleados' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 border border-emerald-500' : 'bg-slate-900/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
            <span>✂️ Empleados / Especialistas</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'empleados' ? 'bg-emerald-700 text-white' : 'bg-slate-800 text-slate-300' }}">{{ $conteoEmpleados }}</span>
        </a>

        <a href="{{ route('terceros.index', ['filtro' => 'proveedores']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'proveedores' ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30 border border-cyan-500' : 'bg-slate-900/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
            <span>📦 Proveedores</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'proveedores' ? 'bg-cyan-700 text-white' : 'bg-slate-800 text-slate-300' }}">{{ $conteoProveedores }}</span>
        </a>
    </div>

    <!-- Tabla de Terceros (Dark Enterprise) -->
    <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 border-b border-slate-800/80 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Tercero / Identificación</th>
                        <th class="px-6 py-4">Roles de Negocio</th>
                        <th class="px-6 py-4">Contacto & Ubicación</th>
                        <th class="px-6 py-4">Fidelización / Métricas</th>
                        <th class="px-6 py-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                    @forelse($terceros as $t)
                    <tr class="hover:bg-slate-800/50 transition">
                        <!-- Nombre e Identificación -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-800 text-slate-200 border border-slate-700/80 flex items-center justify-center font-black text-sm">
                                    {{ strtoupper(substr($t->nombre_completo, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-white text-sm">
                                        {{ $t->nombre_completo }}
                                    </div>
                                    <span class="text-slate-400 text-[11px] block font-mono">
                                        {{ $t->tipo_documento }}: {{ $t->numero_documento }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Roles Activos (Party Model) -->
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @if($t->es_cliente)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                        Cliente
                                    </span>
                                @endif
                                @if($t->es_empleado)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        Empleado ({{ $t->cargo ?? 'Operativo' }})
                                    </span>
                                @endif
                                @if($t->es_proveedor)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                                        Proveedor
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Contacto -->
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">📞 {{ $t->telefono }}</div>
                            <span class="text-slate-400 text-[11px] block">📍 {{ $t->ciudad }}</span>
                        </td>

                        <!-- Métricas / Fidelización -->
                        <td class="px-6 py-4">
                            @if($t->es_cliente)
                                <div class="inline-flex items-center gap-1 font-bold text-indigo-300 bg-indigo-950/80 border border-indigo-800/60 px-2 py-0.5 rounded-md text-[11px]">
                                    ⭐ {{ $t->puntos_fidelidad }} pts CRM
                                </div>
                            @endif
                            @if($t->es_empleado)
                                <div class="text-slate-400 text-[11px] block mt-0.5">
                                    Comisión: <strong class="text-emerald-400">{{ $t->porcentaje_comision }}%</strong>
                                </div>
                            @endif
                            @if($t->es_proveedor && $t->limite_credito > 0)
                                <div class="text-slate-400 text-[11px] block mt-0.5">
                                    Crédito: <strong class="text-white">${{ number_format($t->limite_credito, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('terceros.show', $t) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700/80 rounded-xl text-xs font-bold transition shadow-sm">
                                <span>Perfil 360° →</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                            No se encontraron terceros registrados en esta categoría.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($terceros->hasPages())
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            {{ $terceros->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
