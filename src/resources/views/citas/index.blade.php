@extends('layouts.app')

@section('title', 'Listado de Citas - JyM ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado con Botón Principal Oscuro -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Agenda de Citas & Servicios</h1>
            <p class="text-sm text-slate-500 mt-1">Control de turnos, profesionales y liquidación de servicios.</p>
        </div>
        <div>
            <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                <span class="text-base">➕</span>
                <span>Agendar Nueva Cita</span>
            </a>
        </div>
    </div>

    <!-- Tarjetas de Métricas Sin Bordes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Citas</span>
                <span class="p-2 bg-slate-100 text-slate-800 rounded-xl text-lg">📋</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $totalCitas }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Registros históricos</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Citas Hoy</span>
                <span class="p-2 bg-slate-100 text-slate-800 rounded-xl text-lg">⏳</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $citasHoy }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Programadas para hoy</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Por Atender</span>
                <span class="p-2 bg-slate-100 text-slate-800 rounded-xl text-lg">🔔</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $citasPendientes }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Pendientes y confirmadas</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ingresos Facturados</span>
                <span class="p-2 bg-emerald-50 text-emerald-800 rounded-xl text-lg">💵</span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">${{ number_format($ingresosTotales, 0, ',', '.') }} COP</p>
            <p class="text-xs text-emerald-700 mt-1 font-semibold">Servicios completados</p>
        </div>
    </div>

    <!-- Contenedor Principal de la Tabla (Sin Bordes, con Sombra Suave) -->
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/70">
            <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>💈 Turnos Registrados</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-bold">Paginado</span>
            </h2>
            <span class="text-xs text-slate-500 font-medium">Optimizado con Eager Loading</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-100/80 text-xs uppercase text-slate-700 font-bold tracking-wider">
                    <tr>
                        <th class="py-4 px-5"># ID</th>
                        <th class="py-4 px-5">Cliente</th>
                        <th class="py-4 px-5">Servicio</th>
                        <th class="py-4 px-5">Barbero / Estilista</th>
                        <th class="py-4 px-5">Fecha & Hora</th>
                        <th class="py-4 px-5">Total</th>
                        <th class="py-4 px-5">Estado</th>
                        <th class="py-4 px-5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($citas as $cita)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-5 font-mono font-extrabold text-slate-900">#{{ $cita->id }}</td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-900">{{ $cita->cliente->nombre ?? 'Cliente' }}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span>📞 {{ $cita->cliente->telefono ?? 'S/N' }}</span>
                                    <span class="font-bold text-slate-700">⭐ {{ $cita->cliente->puntos_fidelizacion ?? 0 }} pts</span>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-800">{{ $cita->servicio->nombre ?? 'Servicio' }}</div>
                                <span class="inline-block mt-0.5 text-[11px] px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold">
                                    {{ $cita->servicio->categoria ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-slate-800">
                                <div class="flex items-center gap-1.5 font-semibold">
                                    <span class="text-xs">✂️</span>
                                    <span>{{ $cita->estilista }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs text-slate-600 font-medium">
                                {{ $cita->fecha_hora->format('d/m/Y - h:i A') }}
                            </td>
                            <td class="py-4 px-5 font-black text-slate-900">
                                ${{ number_format($cita->total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5">
                                @if($cita->estado === 'completada')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900">
                                        ● Completada
                                    </span>
                                @elseif($cita->estado === 'confirmada')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900">
                                        ● Confirmada
                                    </span>
                                @elseif($cita->estado === 'en_atencion')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-900">
                                        ● En Atención
                                    </span>
                                @elseif($cita->estado === 'cancelada')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-900">
                                        ● Cancelada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                        ● Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Ver / QR -->
                                    <a href="{{ route('citas.show', $cita) }}" title="Ver Detalle & QR" class="p-2 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-900 hover:text-white transition">
                                        📱
                                    </a>
                                    <!-- Editar -->
                                    <a href="{{ route('citas.edit', $cita) }}" title="Editar" class="p-2 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-900 hover:text-white transition">
                                        ✏️
                                    </a>
                                    <!-- Eliminar -->
                                    <form action="{{ route('citas.destroy', $cita) }}" method="POST" onsubmit="return confirm('¿Eliminar la cita #{{ $cita->id }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar" class="p-2 rounded-xl bg-slate-100 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500 font-medium">
                                No se encontraron citas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citas->hasPages())
            <div class="p-4 bg-slate-50/70">
                {{ $citas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
