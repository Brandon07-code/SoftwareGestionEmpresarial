@extends('layouts.app')

@section('title', 'Agenda de Turnos - ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado con Botón Principal -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2.5 py-1 bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 rounded-full font-bold">Módulo de Servicios</span>
                <span class="text-xs text-slate-400 font-medium">Barbería y Perfumería JyM (Cartago)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">Agenda de Citas & Turnos</h1>
            <p class="text-sm text-slate-400">Gestión de turnos transaccionales vinculada a Terceros y Pasarelas de Pago.</p>
        </div>
        <div>
            <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                <span>➕ Agendar Turno</span>
            </a>
        </div>
    </div>

    <!-- Tarjetas de Métricas (KPIs sin bordes, tono oscuro) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 rounded-2xl p-6 shadow-xl backdrop-blur-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Turnos</span>
                <span class="p-2.5 bg-slate-800/80 text-slate-200 rounded-xl text-lg">📋</span>
            </div>
            <p class="text-3xl font-black text-white mt-3">{{ $totalCitas }}</p>
            <p class="text-xs text-slate-400 mt-1 font-medium">Histórico registrado</p>
        </div>

        <div class="bg-slate-900/90 rounded-2xl p-6 shadow-xl backdrop-blur-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Turnos Hoy</span>
                <span class="p-2.5 bg-slate-800/80 text-slate-200 rounded-xl text-lg">📅</span>
            </div>
            <p class="text-3xl font-black text-white mt-3">{{ $citasHoy }}</p>
            <p class="text-xs text-slate-400 mt-1 font-medium">Jornada actual</p>
        </div>

        <div class="bg-slate-900/90 rounded-2xl p-6 shadow-xl backdrop-blur-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">En Sala / Espera</span>
                <span class="p-2.5 bg-indigo-950/70 text-indigo-300 rounded-xl text-lg">⏳</span>
            </div>
            <p class="text-3xl font-black text-white mt-3">{{ $citasPendientes }}</p>
            <p class="text-xs text-indigo-400/80 mt-1 font-medium">Programadas y en atención</p>
        </div>

        <div class="bg-slate-900/90 rounded-2xl p-6 shadow-xl backdrop-blur-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Recaudo Liquidado</span>
                <span class="p-2.5 bg-emerald-950/70 text-emerald-300 rounded-xl text-lg">💵</span>
            </div>
            <p class="text-2xl font-black text-white mt-3">${{ number_format($ingresosTotales, 0, ',', '.') }} COP</p>
            <p class="text-xs text-emerald-400 mt-1 font-semibold">Servicios completados</p>
        </div>
    </div>

    <!-- Contenedor Principal de la Tabla (Dark Enterprise) -->
    <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-950/60 border-b border-slate-800/80">
            <h2 class="text-base font-black text-white flex items-center gap-2">
                <span>💈 Turnos Registrados</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-bold border border-slate-700/80">Paginado</span>
            </h2>
            <span class="text-xs text-slate-400 font-medium">Optimizado con Eager Loading <code class="text-indigo-300 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">with(['cliente', 'especialista', 'servicio'])</code></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-[11px] uppercase text-slate-400 font-bold tracking-wider border-b border-slate-800/80">
                    <tr>
                        <th class="py-4 px-5"># ID</th>
                        <th class="py-4 px-5">Cliente (Tercero)</th>
                        <th class="py-4 px-5">Servicio</th>
                        <th class="py-4 px-5">Especialista (Empleado)</th>
                        <th class="py-4 px-5">Fecha & Hora</th>
                        <th class="py-4 px-5">Monto</th>
                        <th class="py-4 px-5">Pago / Pasarela</th>
                        <th class="py-4 px-5">Estado</th>
                        <th class="py-4 px-5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                    @forelse($citas as $cita)
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="py-4 px-5 font-mono font-black text-indigo-400">#{{ $cita->id }}</td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-white text-sm">{{ $cita->cliente->nombre_completo ?? 'Cliente no registrado' }}</div>
                                <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span>🆔 {{ $cita->cliente->numero_documento ?? 'S/D' }}</span>
                                    <span>📞 {{ $cita->cliente->telefono ?? 'S/N' }}</span>
                                    <span class="font-bold text-indigo-300 bg-indigo-950/80 border border-indigo-800/60 px-1.5 py-0.5 rounded text-[11px]">⭐ {{ $cita->cliente->puntos_fidelidad ?? 0 }} pts</span>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-200">{{ $cita->servicio->nombre ?? 'Servicio' }}</div>
                                <span class="inline-block mt-0.5 text-[11px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 font-semibold border border-slate-700/80">
                                    ⏱️ {{ $cita->servicio->duracion_minutos ?? 30 }} min
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-semibold text-white">{{ $cita->especialista->nombre_completo ?? 'No asignado' }}</span>
                                <span class="block text-[11px] text-slate-400 font-medium">Comisión: {{ $cita->especialista->porcentaje_comision ?? 50 }}%</span>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs text-slate-400">
                                <div>{{ $cita->fecha_hora->format('d/m/Y') }}</div>
                                <div class="font-bold text-white">{{ $cita->fecha_hora->format('h:i A') }}</div>
                            </td>
                            <td class="py-4 px-5 font-mono font-black text-white text-sm">
                                ${{ number_format($cita->total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5">
                                @if($cita->transaccionPago)
                                    @php
                                        $pasarela = $cita->transaccionPago->pasarela;
                                        $estadoTrx = $cita->transaccionPago->estado;
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold font-mono border {{ $pasarela === 'WOMPI' ? 'bg-purple-950/80 text-purple-300 border-purple-800/60' : ($pasarela === 'NEQUI' ? 'bg-pink-950/80 text-pink-300 border-pink-800/60' : 'bg-slate-800 text-slate-300 border-slate-700/80') }}">
                                            {{ $pasarela }}
                                        </span>
                                        @if($estadoTrx === 'APPROVED')
                                            <span class="text-xs text-emerald-400 font-bold" title="Aprobado">✓</span>
                                        @else
                                            <a href="{{ route('pagos.checkout', $cita->transaccionPago->id) }}" class="text-[11px] text-cyan-400 hover:text-cyan-300 hover:underline font-semibold" title="Abrir Pasarela">Pagar ➔</a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500 font-medium">Sin registro</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                @php
                                    $estado = $cita->estado;
                                    $badge = match($estado) {
                                        'PROGRAMADA' => 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30',
                                        'EN_ATENCION' => 'bg-blue-500/15 text-blue-300 border border-blue-500/30',
                                        'COMPLETADA' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30',
                                        'CANCELADA' => 'bg-rose-500/15 text-rose-300 border border-rose-500/30',
                                        default => 'bg-slate-800 text-slate-300 border border-slate-700/80'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold tracking-wide {{ $badge }}">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('citas.show', $cita->id) }}" class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700/80 transition shadow-sm" title="Ver Detalle y Liquidación">
                                        👁️
                                    </a>
                                    <a href="{{ route('citas.edit', $cita->id) }}" class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700/80 transition shadow-sm" title="Editar">
                                        ✏️
                                    </a>
                                    <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta cita?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-950/40 hover:bg-rose-900/60 text-rose-400 border border-rose-900/40 transition shadow-sm" title="Eliminar">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 font-medium">
                                No hay turnos registrados en este momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citas->hasPages())
            <div class="p-4 bg-slate-950/60 border-t border-slate-800/80">
                {{ $citas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
