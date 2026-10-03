@extends('layouts.app')

@section('title', 'Agenda de Turnos - ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado con Botón Principal -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2.5 py-1 bg-indigo-100 text-indigo-800 rounded-full font-bold">Módulo de Servicios</span>
                <span class="text-xs text-slate-500 font-medium">Barbería y Perfumería JyM (Cartago)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Agenda de Citas & Turnos</h1>
            <p class="text-sm text-slate-500">Gestión de turnos transaccionales vinculada a Terceros y Pasarelas de Pago.</p>
        </div>
        <div>
            <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                <span>➕ Agendar Turno</span>
            </a>
        </div>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Turnos</span>
                <span class="p-2 bg-slate-100 text-slate-800 rounded-xl text-lg">📋</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $totalCitas }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Histórico registrado</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Turnos Hoy</span>
                <span class="p-2 bg-slate-100 text-slate-800 rounded-xl text-lg">📅</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $citasHoy }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Jornada actual</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">En Sala / Espera</span>
                <span class="p-2 bg-amber-50 text-amber-800 rounded-xl text-lg">⏳</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-3">{{ $citasPendientes }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Programadas y en atención</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Recaudo Liquidado</span>
                <span class="p-2 bg-emerald-50 text-emerald-800 rounded-xl text-lg">💵</span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">${{ number_format($ingresosTotales, 0, ',', '.') }} COP</p>
            <p class="text-xs text-emerald-700 mt-1 font-semibold">Servicios completados</p>
        </div>
    </div>

    <!-- Contenedor Principal de la Tabla -->
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/70">
            <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>💈 Turnos Registrados</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-bold">Paginado</span>
            </h2>
            <span class="text-xs text-slate-500 font-medium">Optimizado con Eager Loading <code>with(['cliente', 'especialista', 'servicio'])</code></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-100/80 text-xs uppercase text-slate-700 font-bold tracking-wider">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($citas as $cita)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-5 font-mono font-extrabold text-slate-900">#{{ $cita->id }}</td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-900">{{ $cita->cliente->nombre_completo ?? 'Cliente no registrado' }}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span>🆔 {{ $cita->cliente->numero_documento ?? 'S/D' }}</span>
                                    <span>📞 {{ $cita->cliente->telefono ?? 'S/N' }}</span>
                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded text-[11px]">⭐ {{ $cita->cliente->puntos_fidelidad ?? 0 }} pts</span>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-800">{{ $cita->servicio->nombre ?? 'Servicio' }}</div>
                                <span class="inline-block mt-0.5 text-[11px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold">
                                    ⏱️ {{ $cita->servicio->duracion_minutos ?? 30 }} min
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-semibold text-slate-900">{{ $cita->especialista->nombre_completo ?? 'No asignado' }}</span>
                                <span class="block text-[11px] text-slate-400 font-medium">Comisión: {{ $cita->especialista->porcentaje_comision ?? 50 }}%</span>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs text-slate-600">
                                <div>{{ $cita->fecha_hora->format('d/m/Y') }}</div>
                                <div class="font-bold text-slate-900">{{ $cita->fecha_hora->format('h:i A') }}</div>
                            </td>
                            <td class="py-4 px-5 font-mono font-bold text-slate-900">
                                ${{ number_format($cita->total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5">
                                @if($cita->transaccionPago)
                                    @php
                                        $pasarela = $cita->transaccionPago->pasarela;
                                        $estadoTrx = $cita->transaccionPago->estado;
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold font-mono {{ $pasarela === 'WOMPI' ? 'bg-purple-100 text-purple-800' : ($pasarela === 'NEQUI' ? 'bg-pink-100 text-pink-800' : 'bg-slate-100 text-slate-800') }}">
                                            {{ $pasarela }}
                                        </span>
                                        @if($estadoTrx === 'APPROVED')
                                            <span class="text-xs text-emerald-600 font-bold" title="Aprobado">✓</span>
                                        @else
                                            <a href="{{ route('pagos.checkout', $cita->transaccionPago->id) }}" class="text-[11px] text-blue-600 hover:underline font-semibold" title="Abrir Pasarela">Pagar ➔</a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Sin registro</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                @php
                                    $estado = $cita->estado;
                                    $badge = match($estado) {
                                        'PROGRAMADA' => 'bg-amber-100 text-amber-800',
                                        'EN_ATENCION' => 'bg-blue-100 text-blue-800',
                                        'COMPLETADA' => 'bg-emerald-100 text-emerald-800',
                                        'CANCELADA' => 'bg-rose-100 text-rose-800',
                                        default => 'bg-slate-100 text-slate-800'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('citas.show', $cita->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Ver Detalle y Liquidación">
                                        👁️
                                    </a>
                                    <a href="{{ route('citas.edit', $cita->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Editar">
                                        ✏️
                                    </a>
                                    <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta cita?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Eliminar">
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
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $citas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
