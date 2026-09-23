@extends('layouts.app')

@section('title', 'Comprobante de Turno #' . $cita->id . ' - JyM ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Comprobante de Turno #{{ $cita->id }}</h1>
            <p class="text-sm text-slate-500 mt-1">Detalle del servicio y confirmación mediante QR de Pago.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('citas.edit', $cita) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition">
                ✏️ Editar
            </a>
            <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Tarjetas Principales Sin Bordes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Info del Servicio y Cliente -->
        <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cliente</span>
                    <h2 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $cita->cliente->nombre }}</h2>
                </div>
                <span class="text-xs px-3 py-1 bg-slate-100 text-slate-800 rounded-full font-bold">
                    ⭐ {{ $cita->cliente->puntos_fidelizacion }} Puntos
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Teléfono:</span>
                    <span class="font-bold text-slate-800">📞 {{ $cita->cliente->telefono }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Email:</span>
                    <span class="font-medium text-slate-800">{{ $cita->cliente->email ?? 'No registrado' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Dirección:</span>
                    <span class="font-medium text-slate-800">{{ $cita->cliente->direccion ?? 'Cartago, Valle' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Barbero / Estilista:</span>
                    <span class="font-bold text-slate-900">💈 {{ $cita->estilista }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Servicio Prestado</span>
                <div class="bg-slate-50 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $cita->servicio->nombre }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $cita->servicio->descripcion ?? 'Categoría: ' . $cita->servicio->categoria }} ({{ $cita->servicio->duracion_minutos }} min)</p>
                    </div>
                    <span class="text-lg font-black text-slate-900">
                        ${{ number_format($cita->total, 0, ',', '.') }} COP
                    </span>
                </div>
            </div>

            <div class="pt-2 text-xs text-slate-500">
                <span class="block"><strong>Fecha de Agendamiento:</strong> {{ $cita->fecha_hora->format('l, d \d\e F \d\e Y - h:i A') }}</span>
                @if($cita->notas)
                    <span class="block mt-1"><strong>Observaciones:</strong> {{ $cita->notas }}</span>
                @endif
            </div>
        </div>

        <!-- Resumen de Caja y Liquidación del Servicio -->
        <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 text-[11px] font-bold rounded-full uppercase tracking-wider mb-2">
                    💵 Liquidación en Caja
                </span>
                <h3 class="text-base font-bold text-slate-900">Estado del Turno</h3>
                <p class="text-xs text-slate-500 mt-1">Control de atención y registro de pago en el establecimiento.</p>

                <div class="mt-5 space-y-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl flex justify-between items-center">
                        <span class="text-slate-500">Estado actual:</span>
                        @if($cita->estado === 'completada')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold">✅ Atendida y Cobrada</span>
                        @elseif($cita->estado === 'confirmada')
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold">✓ Confirmada</span>
                        @else
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold">⏳ En Espera</span>
                        @endif
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl flex justify-between items-center">
                        <span class="text-slate-500">Método de pago:</span>
                        <span class="font-bold text-slate-800 uppercase">{{ $cita->metodo_pago === 'qr_fachada' ? 'Nequi / Transferencia' : ucfirst($cita->metodo_pago) }}</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl flex justify-between items-center">
                        <span class="text-slate-500">Total liquidado:</span>
                        <span class="text-sm font-black text-slate-900">${{ number_format($cita->total, 0, ',', '.') }} COP</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4">
                @if($cita->estado !== 'completada')
                <form action="{{ route('citas.update', $cita) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="cliente_id" value="{{ $cita->cliente_id }}">
                    <input type="hidden" name="servicio_id" value="{{ $cita->servicio_id }}">
                    <input type="hidden" name="estilista" value="{{ $cita->estilista }}">
                    <input type="hidden" name="fecha_hora" value="{{ $cita->fecha_hora->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="estado" value="completada">
                    <input type="hidden" name="metodo_pago" value="{{ $cita->metodo_pago === 'pendiente' ? 'efectivo' : $cita->metodo_pago }}">
                    <button type="submit" class="w-full py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow transition">
                        ✓ Marcar como Atendida y Cobrada
                    </button>
                </form>
                @else
                <div class="text-center py-2 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl">
                    ✓ Servicio facturado y cerrado en caja
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
