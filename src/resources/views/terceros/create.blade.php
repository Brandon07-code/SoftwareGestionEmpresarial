@extends('layouts.app')

@section('title', 'Nuevo Tercero - Modelo Unificado de Terceros ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('terceros.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-1">
                    ← Volver a Terceros
                </a>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Registrar Nuevo Tercero</h1>
            <p class="text-sm text-slate-400 mt-0.5">Creación de persona natural o jurídica bajo el modelo empresarial Modelo Unificado de Terceros.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/15 border border-indigo-500/30 text-indigo-300">
            Modelo Unificado de Terceros
        </span>
    </div>

    <!-- Formulario -->
    <form action="{{ route('terceros.store') }}" method="POST" class="bg-slate-800/90 border border-slate-700/80 p-6 sm:p-8 rounded-2xl shadow-xl backdrop-blur-sm space-y-6">
        @csrf

        <!-- 1. Roles de Negocio (El Corazón del Modelo Unificado de Terceros) -->
        <div class="p-4 bg-slate-900/60 border border-slate-700/60 rounded-xl space-y-2">
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-300">
                1. Clasificación / Roles de Negocio * (Selecciona al menos uno)
            </span>
            <p class="text-[11px] text-slate-400">Un mismo tercero puede operar como cliente, proveedor y empleado al mismo tiempo sin duplicar registros.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-950/80 border border-slate-700 cursor-pointer hover:border-indigo-500 transition">
                    <input type="checkbox" name="es_cliente" value="1" {{ old('es_cliente', 1) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">⭐ Cliente</span>
                        <span class="block text-[10px] text-slate-400">Consume bienes / servicios</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-950/80 border border-slate-700 cursor-pointer hover:border-emerald-500 transition">
                    <input type="checkbox" name="es_empleado" value="1" {{ old('es_empleado') ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-600 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="block text-xs font-bold text-white">✂️ Empleado</span>
                        <span class="block text-[10px] text-slate-400">Especialista / Operativo</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-950/80 border border-slate-700 cursor-pointer hover:border-cyan-500 transition">
                    <input type="checkbox" name="es_proveedor" value="1" {{ old('es_proveedor') ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-600 text-cyan-600 focus:ring-cyan-500">
                    <div>
                        <span class="block text-xs font-bold text-white">📦 Proveedor</span>
                        <span class="block text-[10px] text-slate-400">Suministro de insumos</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- 2. Datos de Identificación -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-300 mb-3">
                2. Datos Principales & Fiscales
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Tipo Documento *</label>
                    <select name="tipo_documento" required class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                        <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }} class="bg-slate-900">Cédula de Ciudadanía (CC)</option>
                        <option value="NIT" {{ old('tipo_documento') == 'NIT' ? 'selected' : '' }} class="bg-slate-900">NIT (Persona Jurídica)</option>
                        <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }} class="bg-slate-900">Cédula de Extranjería (CE)</option>
                        <option value="TI" {{ old('tipo_documento') == 'TI' ? 'selected' : '' }} class="bg-slate-900">Tarjeta de Identidad (TI)</option>
                        <option value="Pasaporte" {{ old('tipo_documento') == 'Pasaporte' ? 'selected' : '' }} class="bg-slate-900">Pasaporte</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Número de Documento / NIT *</label>
                    <input type="text" name="numero_documento" required value="{{ old('numero_documento') }}" placeholder="Ej: 1112456789 ó 900.123.456-1" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                    @error('numero_documento')
                        <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Nombre Completo o Razón Social *</label>
                <input type="text" name="nombre_completo" required value="{{ old('nombre_completo') }}" placeholder="Ej: Distribuciones del Café S.A.S. o Juan Carlos Pérez" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                @error('nombre_completo')
                    <p class="text-xs text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 3. Información de Contacto -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-300 mb-3">
                3. Información de Contacto & Ubicación
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Teléfono Principal *</label>
                    <input type="text" name="telefono" required value="{{ old('telefono') }}" placeholder="315 123 4567" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="315 123 4567" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Ciudad *</label>
                    <input type="text" name="ciudad" required value="{{ old('ciudad', 'Cartago') }}" placeholder="Cartago, Pereira, Cali..." class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contacto@empresa.com" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Dirección Física</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Carrera 4 # 12-34 Centro" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>
            </div>
        </div>

        <!-- 4. Parámetros Comerciales y Laborales -->
        <div>
            <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-300 mb-3">
                4. Parámetros Comerciales y Comisiones
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Cargo (Si es empleado)</label>
                    <input type="text" name="cargo" value="{{ old('cargo') }}" placeholder="Barbero Senior / Administrador" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">% Comisión por Servicio</label>
                    <input type="number" step="0.01" min="0" max="100" name="porcentaje_comision" value="{{ old('porcentaje_comision', '0') }}" placeholder="Ej: 50.00" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-300 mb-1">Límite de Crédito COP</label>
                    <input type="number" step="1000" min="0" name="limite_credito" value="{{ old('limite_credito', '0') }}" placeholder="0" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-inner">
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-700/80">
            <a href="{{ route('terceros.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-700/60 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-600 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                Guardar Tercero en ERP
            </button>
        </div>

    </form>

</div>
@endsection
