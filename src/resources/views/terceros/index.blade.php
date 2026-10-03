@extends('layouts.app')

@section('title', 'Directorio de Terceros - Party Model ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado del Módulo -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Directorio Unificado de Terceros</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Party Model</span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5">
                Gestión integral de actores empresariales: una sola entidad con múltiples roles (Cliente, Proveedor, Empleado).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('terceros.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow transition">
                <span>+ Registrar Tercero</span>
            </a>
        </div>
    </div>

    <!-- Pestañas de Filtrado por Rol de Negocio -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('terceros.index', ['filtro' => 'todos']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'todos' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>👥 Todos</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'todos' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $conteoTotal }}</span>
        </a>

        <a href="{{ route('terceros.index', ['filtro' => 'clientes']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'clientes' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>⭐ Clientes</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'clientes' ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $conteoClientes }}</span>
        </a>

        <a href="{{ route('terceros.index', ['filtro' => 'empleados']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'empleados' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>✂️ Empleados / Especialistas</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'empleados' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $conteoEmpleados }}</span>
        </a>

        <a href="{{ route('terceros.index', ['filtro' => 'proveedores']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $categoria === 'proveedores' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>📦 Proveedores</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $categoria === 'proveedores' ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $conteoProveedores }}</span>
        </a>
    </div>

    <!-- Tabla de Terceros -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tercero / Identificación</th>
                        <th class="px-6 py-4">Roles de Negocio</th>
                        <th class="px-6 py-4">Contacto & Ubicación</th>
                        <th class="px-6 py-4">Fidelización / Métricas</th>
                        <th class="px-6 py-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($terceros as $t)
                    <tr class="hover:bg-slate-50/70 transition">
                        <!-- Nombre e Identificación -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($t->nombre_completo, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">
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
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Cliente
                                    </span>
                                @endif
                                @if($t->es_empleado)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Empleado ({{ $t->cargo ?? 'Operativo' }})
                                    </span>
                                @endif
                                @if($t->es_proveedor)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                        Proveedor
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Contacto -->
                        <td class="px-6 py-4">
                            <div class="text-slate-800 font-medium">📞 {{ $t->telefono }}</div>
                            <span class="text-slate-400 text-[11px] block">📍 {{ $t->ciudad }}</span>
                        </td>

                        <!-- Métricas / Fidelización -->
                        <td class="px-6 py-4">
                            @if($t->es_cliente)
                                <div class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md text-[11px]">
                                    ⭐ {{ $t->puntos_fidelidad }} pts CRM
                                </div>
                            @endif
                            @if($t->es_empleado)
                                <div class="text-slate-500 text-[11px] block mt-0.5">
                                    Comisión: <strong class="text-emerald-700">{{ $t->porcentaje_comision }}%</strong>
                                </div>
                            @endif
                            @if($t->es_proveedor && $t->limite_credito > 0)
                                <div class="text-slate-500 text-[11px] block mt-0.5">
                                    Crédito: <strong>${{ number_format($t->limite_credito, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('terceros.show', $t) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition">
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
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $terceros->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
