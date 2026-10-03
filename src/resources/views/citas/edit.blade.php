@extends('layouts.app')

@section('title', 'Editar Turno #' . $cita->id . ' - ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Editar Turno de Servicio #{{ $cita->id }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Modificación de asignación de Terceros, procedimiento y pasarela de pago.</p>
        </div>
        <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition">
            ← Volver al Listado
        </a>
    </div>

    <!-- Formulario en Tarjeta Blanca -->
    <form action="{{ route('citas.update', $cita) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <!-- Selección de Cliente (Tercero) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                👤 Cliente (Tercero Registrado) *
            </label>
            <select name="tercero_cliente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ (old('tercero_cliente_id', $cita->tercero_cliente_id) == $cliente->id) ? 'selected' : '' }}>
                        {{ $cliente->nombre_completo }} — CC: {{ $cliente->numero_documento }} (⭐ {{ $cliente->puntos_fidelidad }} pts)
                    </option>
                @endforeach
            </select>
            @error('tercero_cliente_id')
                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Selección de Especialista (Tercero Empleado) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                ✂️ Especialista / Profesional Responsable (Empleado) *
            </label>
            <select name="tercero_especialista_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                @foreach($especialistas as $esp)
                    <option value="{{ $esp->id }}" {{ (old('tercero_especialista_id', $cita->tercero_especialista_id) == $esp->id) ? 'selected' : '' }}>
                        {{ $esp->nombre_completo }} — {{ $esp->cargo ?? 'Operativo' }} (Comisión: {{ $esp->porcentaje_comision }}%)
                    </option>
                @endforeach
            </select>
            @error('tercero_especialista_id')
                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Selección de Servicio -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                💈 Servicio del Catálogo Oficial *
            </label>
            <select name="servicio_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}" {{ (old('servicio_id', $cita->servicio_id) == $servicio->id) ? 'selected' : '' }}>
                        {{ $servicio->nombre }} — ${{ number_format($servicio->precio_venta, 0, ',', '.') }} COP ({{ $servicio->duracion_minutos }} min)
                    </option>
                @endforeach
            </select>
            @error('servicio_id')
                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Fecha y Hora -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    📅 Fecha y Hora *
                </label>
                <input type="datetime-local" name="fecha_hora" required value="{{ old('fecha_hora', $cita->fecha_hora ? $cita->fecha_hora->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                @error('fecha_hora')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    📊 Estado del Turno *
                </label>
                <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                    <option value="PROGRAMADA" {{ old('estado', $cita->estado) == 'PROGRAMADA' ? 'selected' : '' }}>⏳ Programada</option>
                    <option value="EN_ATENCION" {{ old('estado', $cita->estado) == 'EN_ATENCION' ? 'selected' : '' }}>✂️ En Atención</option>
                    <option value="COMPLETADA" {{ old('estado', $cita->estado) == 'COMPLETADA' ? 'selected' : '' }}>✅ Completada & Cobrada</option>
                    <option value="CANCELADA" {{ old('estado', $cita->estado) == 'CANCELADA' ? 'selected' : '' }}>❌ Cancelada</option>
                </select>
            </div>

            <!-- Pasarela de Pago -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    💳 Pasarela de Pago *
                </label>
                @php
                    $pasarelaActual = $cita->transaccionPago->pasarela ?? 'EFECTIVO';
                @endphp
                <select name="pasarela" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                    <option value="EFECTIVO" {{ old('pasarela', $pasarelaActual) == 'EFECTIVO' ? 'selected' : '' }}>💵 Efectivo en Caja</option>
                    <option value="NEQUI" {{ old('pasarela', $pasarelaActual) == 'NEQUI' ? 'selected' : '' }}>📱 Nequi Dinámico</option>
                    <option value="WOMPI" {{ old('pasarela', $pasarelaActual) == 'WOMPI' ? 'selected' : '' }}>💳 WOMPI (PSE / Tarjeta)</option>
                    <option value="TRANSFERENCIA" {{ old('pasarela', $pasarelaActual) == 'TRANSFERENCIA' ? 'selected' : '' }}>🏦 Transferencia</option>
                </select>
            </div>
        </div>

        <!-- Notas / Observaciones -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                📝 Notas de Atención / Requerimientos Técnicos
            </label>
            <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium" placeholder="Detalles de diseño, corte, productos a utilizar...">{{ old('notas', $cita->notas) }}</textarea>
        </div>

        <!-- Acciones -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('citas.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                🔄 Guardar Cambios
            </button>
        </div>

    </form>

</div>
@endsection
