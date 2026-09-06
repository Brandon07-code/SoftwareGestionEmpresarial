@extends('layouts.app')

@section('title', 'Detalle de Cita #' . $cita->id . ' - Quedó Pinta ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Comprobante de Turno #{{ $cita->id }}</h1>
            <p class="text-sm text-slate-400 mt-1">Detalle del servicio y confirmación mediante QR de Pago.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('citas.edit', $cita) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-xl transition">
                ✏️ Editar
            </a>
            <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-xl transition">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Tarjeta Principal de Detalle y QR -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Info del Servicio y Cliente -->
        <div class="md:col-span-2 bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Cliente</span>
                    <h2 class="text-xl font-bold text-white mt-0.5">{{ $cita->cliente->nombre }}</h2>
                </div>
                <span class="text-xs px-3 py-1 bg-purple-500/10 text-purple-300 border border-purple-500/20 rounded-full font-medium">
                    ⭐ {{ $cita->cliente->puntos_fidelizacion }} Puntos
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Teléfono:</span>
                    <span class="font-medium text-slate-200">📞 {{ $cita->cliente->telefono }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Email:</span>
                    <span class="font-medium text-slate-200">{{ $cita->cliente->email ?? 'No registrado' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Dirección:</span>
                    <span class="font-medium text-slate-200">{{ $cita->cliente->direccion ?? 'Cartago, Valle' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Profesional / Estilista:</span>
                    <span class="font-medium text-purple-300">💈 {{ $cita->estilista }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800">
                <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider block mb-2">Servicio Solicitado</span>
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-white">{{ $cita->servicio->nombre }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $cita->servicio->descripcion ?? 'Categoría: ' . $cita->servicio->categoria }} ({{ $cita->servicio->duracion_minutos }} min)</p>
                    </div>
                    <span class="text-lg font-extrabold text-emerald-400">
                        ${{ number_format($cita->total, 0, ',', '.') }} COP
                    </span>
                </div>
            </div>

            <div class="pt-2 text-xs text-slate-400">
                <span class="block"><strong>Fecha de Agendamiento:</strong> {{ $cita->fecha_hora->format('l, d \d\e F \d\e Y - h:i A') }}</span>
                @if($cita->notas)
                    <span class="block mt-1"><strong>Observaciones:</strong> {{ $cita->notas }}</span>
                @endif
            </div>
        </div>

        <!-- Módulo QR Fachada de Pago -->
        <div class="bg-gradient-to-b from-purple-900/30 to-slate-900/80 border border-purple-500/30 rounded-2xl p-6 shadow-xl flex flex-col items-center justify-between text-center">
            <div>
                <span class="inline-block px-3 py-1 bg-purple-500/20 text-purple-300 text-xs font-bold rounded-full uppercase tracking-wider mb-3">
                    📱 QR de Pago
                </span>
                <h3 class="text-sm font-bold text-white">Escanea para Confirmar</h3>
                <p class="text-[11px] text-slate-400 mt-1">El cliente escanea con la cámara de su celular.</p>
            </div>

            <!-- Código QR generado dinámicamente -->
            <div class="my-4 p-3 bg-white rounded-2xl shadow-lg shadow-purple-950/50">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(url('/citas/' . $cita->id)) }}" alt="QR de Pago Cita #{{ $cita->id }}" class="w-36 h-36 mx-auto rounded-lg">
            </div>

            <div class="w-full">
                <div class="text-xs text-slate-400 mb-2">
                    Estado: 
                    @if($cita->estado === 'completada')
                        <span class="font-bold text-emerald-400">✅ Pagado & Finalizado</span>
                    @else
                        <span class="font-bold text-amber-400">⏳ En Espera</span>
                    @endif
                </div>
                <form action="{{ route('citas.update', $cita) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="cliente_id" value="{{ $cita->cliente_id }}">
                    <input type="hidden" name="servicio_id" value="{{ $cita->servicio_id }}">
                    <input type="hidden" name="estilista" value="{{ $cita->estilista }}">
                    <input type="hidden" name="fecha_hora" value="{{ $cita->fecha_hora->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="estado" value="completada">
                    <input type="hidden" name="metodo_pago" value="qr_fachada">
                    <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                        ✓ Confirmar Pago (Simulación)
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
