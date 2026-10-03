@extends('layouts.app')

@section('title', 'Agendar Turno - ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Agendar Nuevo Turno de Servicio</h1>
            <p class="text-sm text-slate-500 mt-0.5">Asociación formal de Cliente (Tercero), Especialista y Pasarela de Pago.</p>
        </div>
        <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition">
            ← Volver al Listado
        </a>
    </div>

    <!-- Formulario en Tarjeta Blanca -->
    <form action="{{ route('citas.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm space-y-5">
        @csrf

        <!-- Selección de Cliente (Tercero) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                👤 Cliente (Tercero Categorizado) *
            </label>
            <select name="tercero_cliente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                <option value="">-- Seleccione un cliente registrado --</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('tercero_cliente_id') == $cliente->id ? 'selected' : '' }}>
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
                ✂️ Barbero / Especialista Asignado (Empleado) *
            </label>
            <select name="tercero_especialista_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                <option value="">-- Seleccione el profesional responsable --</option>
                @foreach($especialistas as $esp)
                    <option value="{{ $esp->id }}" {{ old('tercero_especialista_id') == $esp->id ? 'selected' : '' }}>
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
            <select name="servicio_id" id="servicio_select" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                <option value="">-- Seleccione el procedimiento a realizar --</option>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio_venta }}" {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}>
                        {{ $servicio->nombre }} — ${{ number_format($servicio->precio_venta, 0, ',', '.') }} COP ({{ $servicio->duracion_minutos }} min)
                    </option>
                @endforeach
            </select>
            @error('servicio_id')
                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Fecha y Hora -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    📅 Fecha y Hora Programada *
                </label>
                <input type="datetime-local" name="fecha_hora" required value="{{ old('fecha_hora', now()->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                @error('fecha_hora')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pasarela de Pago -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    💳 Pasarela de Pago / Método *
                </label>
                <select name="pasarela" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                    <option value="EFECTIVO" {{ old('pasarela') == 'EFECTIVO' ? 'selected' : '' }}>💵 Efectivo en Caja Mostrador</option>
                    <option value="NEQUI" {{ old('pasarela') == 'NEQUI' ? 'selected' : '' }}>📱 Nequi (QR Dinámico / Push)</option>
                    <option value="WOMPI" {{ old('pasarela') == 'WOMPI' ? 'selected' : '' }}>💳 WOMPI (Bancolombia / PSE / Tarjeta)</option>
                    <option value="TRANSFERENCIA" {{ old('pasarela') == 'TRANSFERENCIA' ? 'selected' : '' }}>🏦 Transferencia Directa</option>
                </select>
                @error('pasarela')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Estado Inicial -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    🚦 Estado del Turno *
                </label>
                <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                    <option value="PROGRAMADA" selected>Programada (En Agenda)</option>
                    <option value="EN_ATENCION">En Atención (En Sillón)</option>
                    <option value="COMPLETADA">Completada (Cobrada)</option>
                </select>
            </div>

            <!-- Observaciones -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    📝 Notas u Observaciones
                </label>
                <input type="text" name="notas" value="{{ old('notas') }}" placeholder="Ej: Degradado alto, cliente alérgico..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
            </div>
        </div>

        <!-- Botón Enviar -->
        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('citas.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                Confirmar y Agendar Turno
            </button>
        </div>
    </form>

</div>
@endsection
