@extends('layouts.app')

@section('title', 'Editar Turno #' . $cita->id . ' - ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('citas.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-1">
                ← Volver al Listado
            </a>
            <h1 class="text-2xl font-black text-white tracking-tight">Editar Turno de Servicio #{{ $cita->id }}</h1>
            <p class="text-sm text-slate-400 mt-0.5">Modificación de asignación de Terceros, procedimiento y pasarela de pago.</p>
        </div>
    </div>

    <!-- Formulario en Tarjeta Dark Slate -->
    <form action="{{ route('citas.update', $cita) }}" method="POST" class="bg-slate-800/90 border border-slate-700/80 p-6 sm:p-8 rounded-2xl shadow-xl backdrop-blur-sm space-y-5">
        @csrf
        @method('PUT')

        <!-- Selección de Cliente (Tercero) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                👤 Cliente (Tercero Registrado) *
            </label>
            <select name="tercero_cliente_id" required class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ (old('tercero_cliente_id', $cita->tercero_cliente_id) == $cliente->id) ? 'selected' : '' }} class="bg-slate-900 text-white">
                        {{ $cliente->nombre_completo }} — CC: {{ $cliente->numero_documento }} (⭐ {{ $cliente->puntos_fidelidad }} pts)
                    </option>
                @endforeach
            </select>
            @error('tercero_cliente_id')
                <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Selección de Especialista (Tercero Empleado) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                ✂️ Especialista / Profesional Responsable (Empleado) *
            </label>
            <select name="tercero_especialista_id" required class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                @foreach($especialistas as $esp)
                    <option value="{{ $esp->id }}" {{ (old('tercero_especialista_id', $cita->tercero_especialista_id) == $esp->id) ? 'selected' : '' }} class="bg-slate-900 text-white">
                        {{ $esp->nombre_completo }} — {{ $esp->cargo ?? 'Operativo' }} (Comisión: {{ $esp->porcentaje_comision }}%)
                    </option>
                @endforeach
            </select>
            @error('tercero_especialista_id')
                <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Procedimiento / Servicio -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                💈 Procedimiento Seleccionado *
            </label>
            <select name="servicio_id" required class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                @foreach($servicios as $serv)
                    <option value="{{ $serv->id }}" {{ (old('servicio_id', $cita->servicio_id) == $serv->id) ? 'selected' : '' }} class="bg-slate-900 text-white">
                        {{ $serv->nombre }} — ${{ number_format($serv->precio, 0, ',', '.') }} COP (Duración: {{ $serv->duracion_minutos }} min)
                    </option>
                @endforeach
            </select>
            @error('servicio_id')
                <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fecha, Hora y Estado -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    📅 Fecha y Hora de Reserva *
                </label>
                <input type="datetime-local" name="fecha_hora" required value="{{ old('fecha_hora', \Carbon\Carbon::parse($cita->fecha_hora)->format('Y-m-d\TH:i')) }}" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                @error('fecha_hora')
                    <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    🏷️ Estado Operativo del Turno *
                </label>
                <select name="estado" required class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                    <option value="PROGRAMADA" {{ (old('estado', $cita->estado) == 'PROGRAMADA') ? 'selected' : '' }} class="bg-slate-900">🕒 PROGRAMADA (Pendiente)</option>
                    <option value="EN_ATENCION" {{ (old('estado', $cita->estado) == 'EN_ATENCION') ? 'selected' : '' }} class="bg-slate-900">💈 EN ATENCIÓN (En curso)</option>
                    <option value="COMPLETADA" {{ (old('estado', $cita->estado) == 'COMPLETADA') ? 'selected' : '' }} class="bg-slate-900">✅ COMPLETADA (Finalizada)</option>
                    <option value="CANCELADA" {{ (old('estado', $cita->estado) == 'CANCELADA') ? 'selected' : '' }} class="bg-slate-900">❌ CANCELADA</option>
                </select>
                @error('estado')
                    <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Pasarela de Cobro y Valor Total -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    💳 Pasarela / Método de Cobro *
                </label>
                <select name="pasarela" required class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                    <option value="EFECTIVO" {{ (old('pasarela', $cita->transaccionPrincipal?->pasarela) == 'EFECTIVO') ? 'selected' : '' }} class="bg-slate-900">💵 Pago en Caja Mostrador (Efectivo)</option>
                    <option value="NEQUI" {{ (old('pasarela', $cita->transaccionPrincipal?->pasarela) == 'NEQUI') ? 'selected' : '' }} class="bg-slate-900">🟣 NEQUI (QR Dinámico Push)</option>
                    <option value="WOMPI" {{ (old('pasarela', $cita->transaccionPrincipal?->pasarela) == 'WOMPI') ? 'selected' : '' }} class="bg-slate-900">💳 WOMPI Bancolombia (Tarjeta / PSE)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    💰 Monto Total (COP) *
                </label>
                <input type="number" step="500" min="0" name="total" required value="{{ old('total', (int)$cita->total) }}" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner font-mono font-bold">
            </div>
        </div>

        <!-- Notas de seguimiento -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                📝 Notas / Instrucciones Técnicas
            </label>
            <textarea name="notas" rows="2" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl p-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner resize-y">{{ old('notas', $cita->notas) }}</textarea>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-700/80">
            <a href="{{ route('citas.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-700/60 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-600 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                Guardar Modificaciones
            </button>
        </div>

    </form>

</div>
@endsection
