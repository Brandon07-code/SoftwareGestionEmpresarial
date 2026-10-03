@extends('layouts.app')

@section('title', 'Nuevo Tercero - Party Model ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Registrar Nuevo Tercero</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Party Model</span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5">Creación de persona natural o jurídica con asignación de múltiples roles de negocio.</p>
        </div>
        <a href="{{ route('terceros.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition">
            ← Volver
        </a>
    </div>

    <!-- Formulario -->
    <form action="{{ route('terceros.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm space-y-6">
        @csrf

        <!-- 1. Roles de Negocio (El Corazón del Party Model) -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
                1. Clasificación / Roles de Negocio * (Selecciona al menos uno)
            </span>
            <p class="text-[11px] text-slate-500">Un mismo tercero puede ser cliente, proveedor y empleado al mismo tiempo sin duplicar registros.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-indigo-500 transition">
                    <input type="checkbox" name="es_cliente" value="1" {{ old('es_cliente', 1) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">⭐ Cliente</span>
                        <span class="block text-[10px] text-slate-400">Consume bienes / servicios</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-500 transition">
                    <input type="checkbox" name="es_empleado" value="1" {{ old('es_empleado') ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">✂️ Empleado</span>
                        <span class="block text-[10px] text-slate-400">Especialista / Operativo</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-amber-500 transition">
                    <input type="checkbox" name="es_proveedor" value="1" {{ old('es_proveedor') ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">📦 Proveedor</span>
                        <span class="block text-[10px] text-slate-400">Suministro de insumos</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- 2. Datos de Identificación -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-3">
                2. Datos Principales & Fiscales
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Tipo Documento *</label>
                    <select name="tipo_documento" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                        <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía (CC)</option>
                        <option value="NIT" {{ old('tipo_documento') == 'NIT' ? 'selected' : '' }}>NIT (Persona Jurídica)</option>
                        <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería (CE)</option>
                        <option value="TI" {{ old('tipo_documento') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad (TI)</option>
                        <option value="Pasaporte" {{ old('tipo_documento') == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Número de Documento / NIT *</label>
                    <input type="text" name="numero_documento" required value="{{ old('numero_documento') }}" placeholder="Ej: 1112456789 ó 900.123.456-1" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                    @error('numero_documento')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nombre Completo o Razón Social *</label>
                <input type="text" name="nombre_completo" required value="{{ old('nombre_completo') }}" placeholder="Ej: Distribuciones del Café S.A.S. o Juan Carlos Pérez" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                @error('nombre_completo')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 3. Información de Contacto -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-3">
                3. Información de Contacto & Ubicación
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Teléfono Principal *</label>
                    <input type="text" name="telefono" required value="{{ old('telefono') }}" placeholder="315 123 4567" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="315 123 4567" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Ciudad *</label>
                    <input type="text" name="ciudad" required value="{{ old('ciudad', 'Cartago') }}" placeholder="Cartago, Pereira, Cali..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contacto@empresa.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Dirección Física</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Carrera 4 # 12-34 Centro" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>
            </div>
        </div>

        <!-- 4. Parámetros Comerciales y Laborales -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-3">
                4. Parámetros Comerciales y Comisiones
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Cargo (Si es empleado)</label>
                    <input type="text" name="cargo" value="{{ old('cargo') }}" placeholder="Barbero Senior / Administrador" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">% Comisión por Servicio</label>
                    <input type="number" step="0.01" min="0" max="100" name="porcentaje_comision" value="{{ old('porcentaje_comision', '0') }}" placeholder="Ej: 50.00" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Límite de Crédito COP</label>
                    <input type="number" step="1000" min="0" name="limite_credito" value="{{ old('limite_credito', '0') }}" placeholder="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 font-medium">
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('terceros.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow transition">
                ✓ Guardar Tercero en ERP
            </button>
        </div>

    </form>

</div>
@endsection
