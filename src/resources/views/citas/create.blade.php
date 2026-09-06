@extends('layouts.app')

@section('title', 'Agendar Nueva Cita - Quedó Pinta ERP')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Agendar Nueva Cita</h1>
            <p class="text-sm text-slate-400 mt-1">Registra un nuevo turno asignando cliente, servicio y profesional.</p>
        </div>
        <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-xl transition">
            ← Volver al Listado
        </a>
    </div>

    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="{{ route('citas.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Cliente -->
                <div>
                    <label for="cliente_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        👤 Cliente <span class="text-rose-500">*</span>
                    </label>
                    <select name="cliente_id" id="cliente_id" class="w-full bg-slate-950 border @error('cliente_id') border-rose-500 @else border-slate-700 @enderror rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} (📞 {{ $cliente->telefono }}) - ⭐ {{ $cliente->puntos_fidelizacion }} pts
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Servicio -->
                <div>
                    <label for="servicio_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        ✂️ Servicio <span class="text-rose-500">*</span>
                    </label>
                    <select name="servicio_id" id="servicio_id" class="w-full bg-slate-950 border @error('servicio_id') border-rose-500 @else border-slate-700 @enderror rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <option value="">-- Seleccione un servicio --</option>
                        @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}" {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}>
                                {{ $servicio->nombre }} [{{ $servicio->categoria }}] - ${{ number_format($servicio->precio, 0, ',', '.') }} COP ({{ $servicio->duracion_minutos }} min)
                            </option>
                        @endforeach
                    </select>
                    @error('servicio_id')
                        <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estilista -->
                <div>
                    <label for="estilista" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        💈 Profesional / Barbero / Estilista <span class="text-rose-500">*</span>
                    </label>
                    <select name="estilista" id="estilista" class="w-full bg-slate-950 border @error('estilista') border-rose-500 @else border-slate-700 @enderror rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <option value="">-- Seleccione el profesional --</option>
                        @foreach($estilistas as $estilista)
                            <option value="{{ $estilista }}" {{ old('estilista') == $estilista ? 'selected' : '' }}>
                                {{ $estilista }}
                            </option>
                        @endforeach
                    </select>
                    @error('estilista')
                        <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha y Hora -->
                <div>
                    <label for="fecha_hora" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        📅 Fecha y Hora de la Cita <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_hora" id="fecha_hora" value="{{ old('fecha_hora', now()->addHours(2)->format('Y-m-d\TH:i')) }}" class="w-full bg-slate-950 border @error('fecha_hora') border-rose-500 @else border-slate-700 @enderror rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    @error('fecha_hora')
                        <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado Inicial -->
                <div>
                    <label for="estado" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        📊 Estado Inicial <span class="text-rose-500">*</span>
                    </label>
                    <select name="estado" id="estado" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ old('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="en_atencion" {{ old('estado') == 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                        <option value="completada" {{ old('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    </select>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label for="metodo_pago" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        💳 Método de Pago <span class="text-rose-500">*</span>
                    </label>
                    <select name="metodo_pago" id="metodo_pago" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo en Caja</option>
                        <option value="qr_fachada" {{ old('metodo_pago') == 'qr_fachada' ? 'selected' : '' }}>📱 QR de Pago (Escaneo)</option>
                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia Bancaria / Nequi / Daviplata</option>
                        <option value="pendiente" {{ old('metodo_pago') == 'pendiente' ? 'selected' : '' }}>Pendiente por Pagar</option>
                    </select>
                </div>

            </div>

            <!-- Notas -->
            <div>
                <label for="notas" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    📝 Observaciones / Preferencias del Cliente
                </label>
                <textarea name="notas" id="notas" rows="3" placeholder="Ej: Prefiere corte con tijera, requiere lavado extra, etc." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition">{{ old('notas') }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('citas.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-purple-600/30 transition">
                    💾 Guardar Cita
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
