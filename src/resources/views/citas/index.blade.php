@extends('layouts.app')

@section('title', 'Listado de Citas - Quedó Pinta ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado con Métricas -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Agenda de Citas & Servicios</h1>
            <p class="text-sm text-slate-400 mt-1">Gestión operativa, control de turnos y cobros para barbería y estética.</p>
        </div>
        <div>
            <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-purple-600/30 transition transform hover:-translate-y-0.5">
                <span class="text-lg">➕</span>
                <span>Agendar Nueva Cita</span>
            </a>
        </div>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/60 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Citas</span>
                <span class="p-2 bg-purple-500/10 text-purple-400 rounded-lg text-lg">📋</span>
            </div>
            <p class="text-3xl font-extrabold text-white mt-3">{{ $totalCitas }}</p>
            <p class="text-xs text-purple-400 mt-1 font-medium">Registros en el sistema</p>
        </div>

        <div class="bg-slate-900/60 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Citas Hoy</span>
                <span class="p-2 bg-amber-500/10 text-amber-400 rounded-lg text-lg">⏳</span>
            </div>
            <p class="text-3xl font-extrabold text-amber-400 mt-3">{{ $citasHoy }}</p>
            <p class="text-xs text-slate-400 mt-1">Programadas para hoy</p>
        </div>

        <div class="bg-slate-900/60 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Por Atender</span>
                <span class="p-2 bg-blue-500/10 text-blue-400 rounded-lg text-lg">🔔</span>
            </div>
            <p class="text-3xl font-extrabold text-blue-400 mt-3">{{ $citasPendientes }}</p>
            <p class="text-xs text-slate-400 mt-1">Pendientes y confirmadas</p>
        </div>

        <div class="bg-slate-900/60 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ingresos Completados</span>
                <span class="p-2 bg-emerald-500/10 text-emerald-400 rounded-lg text-lg">💵</span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-400 mt-3">${{ number_format($ingresosTotales, 0, ',', '.') }} COP</p>
            <p class="text-xs text-emerald-400 mt-1">Facturados con éxito</p>
        </div>
    </div>

    <!-- Tabla Principal de Citas -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-900/90">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <span>💈 Listado Principal de Turnos</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-normal">Paginado</span>
            </h2>
            <span class="text-xs text-slate-400">Optimizado con Eager Loading (evita problema N+1)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase text-slate-400 font-semibold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4"># ID</th>
                        <th class="py-3.5 px-4">Cliente</th>
                        <th class="py-3.5 px-4">Servicio & Categoría</th>
                        <th class="py-3.5 px-4">Estilista</th>
                        <th class="py-3.5 px-4">Fecha & Hora</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Estado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($citas as $cita)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-4 px-4 font-mono font-bold text-purple-400">#{{ $cita->id }}</td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-white">{{ $cita->cliente->nombre ?? 'Cliente no encontrado' }}</div>
                                <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span>📞 {{ $cita->cliente->telefono ?? 'S/N' }}</span>
                                    <span class="text-amber-400">⭐ {{ $cita->cliente->puntos_fidelizacion ?? 0 }} pts</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-slate-200">{{ $cita->servicio->nombre ?? 'Servicio' }}</div>
                                <span class="inline-block mt-0.5 text-[11px] px-2 py-0.5 rounded-md bg-purple-900/40 text-purple-300 border border-purple-700/30">
                                    {{ $cita->servicio->categoria ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-300">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs">✂️</span>
                                    <span class="font-medium">{{ $cita->estilista }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-xs text-slate-300">
                                {{ $cita->fecha_hora->format('d/m/Y - h:i A') }}
                            </td>
                            <td class="py-4 px-4 font-bold text-emerald-400">
                                ${{ number_format($cita->total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                @if($cita->estado === 'completada')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        ● Completada
                                    </span>
                                @elseif($cita->estado === 'confirmada')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        ● Confirmada
                                    </span>
                                @elseif($cita->estado === 'en_atencion')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                        ● En Atención
                                    </span>
                                @elseif($cita->estado === 'cancelada')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        ● Cancelada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-500/10 text-slate-300 border border-slate-500/20">
                                        ● Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Ver / QR -->
                                    <a href="{{ route('citas.show', $cita) }}" title="Ver Detalle & QR" class="p-1.5 rounded-lg bg-slate-800 text-purple-300 hover:bg-purple-600 hover:text-white transition">
                                        📱
                                    </a>
                                    <!-- Editar -->
                                    <a href="{{ route('citas.edit', $cita) }}" title="Editar" class="p-1.5 rounded-lg bg-slate-800 text-blue-300 hover:bg-blue-600 hover:text-white transition">
                                        ✏️
                                    </a>
                                    <!-- Eliminar -->
                                    <form action="{{ route('citas.destroy', $cita) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar la cita #{{ $cita->id }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar" class="p-1.5 rounded-lg bg-slate-800 text-rose-400 hover:bg-rose-600 hover:text-white transition">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">
                                No se encontraron citas agendadas en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citas->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $citas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
