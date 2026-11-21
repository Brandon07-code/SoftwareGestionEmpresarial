<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-black text-2xl text-white tracking-tight">
                    Registrar Nueva Organización (Tenant)
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Incorporación de una nueva empresa al ecosistema Turnix ERP</p>
            </div>
            <a href="{{ route('empresas.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700 transition">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl backdrop-blur-sm space-y-6">

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                        <strong class="font-bold">Corrige los siguientes errores:</strong>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('empresas.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">NIT / Identificación Tributaria *</label>
                            <input type="text" name="nit" value="{{ old('nit') }}" placeholder="Ej: 901.234.567-8" required class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500 font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Giro / Tipo de Negocio *</label>
                            <select name="tipo_negocio" required class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500">
                                <option value="mixto" @selected(old('tipo_negocio') === 'mixto')>Mixto (Servicios + Comercio Minorista)</option>
                                <option value="servicios" @selected(old('tipo_negocio') === 'servicios')>Servicios (Barbería, Estética, Citas)</option>
                                <option value="retail" @selected(old('tipo_negocio') === 'retail')>Comercio Minorista (Perfumería, Cosméticos, POS)</option>
                                <option value="mayorista" @selected(old('tipo_negocio') === 'mayorista')>Distribución Mayorista</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Razón Social *</label>
                            <input type="text" name="razon_social" value="{{ old('razon_social') }}" placeholder="Ej: Inversiones del Café S.A.S." required class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Nombre Comercial *</label>
                            <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial') }}" placeholder="Ej: Café y Barba Express" required class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500 font-bold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Teléfono de Contacto</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 3158889900" class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Correo Electrónico Oficial</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="contacto@empresa.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Dirección Sede Principal</label>
                            <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Ej: Carrera 5 # 14-30" class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Ciudad / Departamento</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad') }}" placeholder="Ej: Pereira, Risaralda" class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Moneda Base *</label>
                            <select name="moneda" required class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs text-white px-3.5 py-2.5 focus:border-indigo-500 font-mono">
                                <option value="COP" @selected(old('moneda', 'COP') === 'COP')>COP - Peso Colombiano ($)</option>
                                <option value="USD" @selected(old('moneda') === 'USD')>USD - Dólar Estadounidense ($)</option>
                                <option value="EUR" @selected(old('moneda') === 'EUR')>EUR - Euro (€)</option>
                            </select>
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="activo" value="1" checked class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                <span class="text-xs font-bold text-slate-300">Activar Organización de Inmediato</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                        <a href="{{ route('empresas.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                            Crear Empresa
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
