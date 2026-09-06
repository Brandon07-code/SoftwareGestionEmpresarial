@extends('layouts.app')

@section('title', 'Editar Cita #' . $cita->id . ' - JyM ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Editar Cita #{{ $cita->id }}</h1>
            <p class="text-sm text-slate-500 mt-1">Actualiza los datos del turno, estado o método de pago.</p>
        </div>
        <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition">
            ← Volver al Listado
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm">
        <form action="{{ route('citas.update', $cita) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Cliente -->
                <div>
                    <label for="cliente_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        👤 Cliente <span class="text-rose-500">*</span>
                    </label>
                    <select name="cliente_id" id="cliente_id" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ (old('cliente_id', $cita->cliente_id) == $cliente->id) ? 'selected' : '' }}>
                                {{ $cliente->nombre }} (📞 {{ $cliente->telefono }})
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Servicio -->
                <div>
                    <label for="servicio_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        ✂️ Servicio <span class="text-rose-500">*</span>
                    </label>
                    <select name="servicio_id" id="servicio_id" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                        @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}" {{ (old('servicio_id', $cita->servicio_id) == $servicio->id) ? 'selected' : '' }}>
                                {{ $servicio->nombre }} [{{ $servicio->categoria }}] - ${{ number_format($servicio->precio, 0, ',', '.') }} COP
                            </option>
                        @endforeach
                    </select>
                    @error('servicio_id')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estilista -->
                <div>
                    <label for="estilista" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        💈 Barbero / Estilista <span class="text-rose-500">*</span>
                    </label>
                    <select name="estilista" id="estilista" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                        @foreach($estilistas as $estilista)
                            <option value="{{ $estilista }}" {{ (old('estilista', $cita->estilista) == $estilista) ? 'selected' : '' }}>
                                {{ $estilista }}
                            </option>
                        @endforeach
                    </select>
                    @error('estilista')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha y Hora -->
                <div>
                    <label for="fecha_hora" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        📅 Fecha y Hora <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_hora" id="fecha_hora" value="{{ old('fecha_hora', $cita->fecha_hora->format('Y-m-d\TH:i')) }}" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                    @error('fecha_hora')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        📊 Estado del Turno <span class="text-rose-500">*</span>
                    </label>
                    <select name="estado" id="estado" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                        <option value="pendiente" {{ old('estado', $cita->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ old('estado', $cita->estado) == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="en_atencion" {{ old('estado', $cita->estado) == 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                        <option value="completada" {{ old('estado', $cita->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ old('estado', $cita->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label for="metodo_pago" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        💳 Método de Pago <span class="text-rose-500">*</span>
                    </label>
                    <select name="metodo_pago" id="metodo_pago" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                        <option value="efectivo" {{ old('metodo_pago', $cita->metodo_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo en Caja</option>
                        <option value="qr_fachada" {{ old('metodo_pago', $cita->metodo_pago) == 'qr_fachada' ? 'selected' : '' }}>📱 QR de Pago (Escaneo)</option>
                        <option value="transferencia" {{ old('metodo_pago', $cita->metodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia Bancaria / Nequi / Daviplata</option>
                        <option value="pendiente" {{ old('metodo_pago', $cita->metodo_pago) == 'pendiente' ? 'selected' : '' }}>Pendiente por Pagar</option>
                    </select>
                </div>

            </div>

            <!-- Notas -->
            <div>
                <label for="notas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    📝 Observaciones / Notas
                </label>
                <textarea name="notas" id="notas" rows="3" class="w-full bg-slate-50 rounded-xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">{{ old('notas', $cita->notas) }}</textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('citas.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                    🔄 Actualizar Cita
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
