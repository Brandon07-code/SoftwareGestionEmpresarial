<x-app-layout>
    <div class="py-8 bg-slate-900 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-2">
                        ← Volver al Catálogo
                    </a>
                    <h1 class="text-2xl font-black text-white tracking-tight">Importación Masiva de Productos (Excel)</h1>
                    <p class="text-sm text-slate-400">Carga inventarios y catálogos rápidamente mediante archivos .xlsx o .csv.</p>
                </div>
            </div>

            <!-- Instrucciones de uso -->
            <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-4">
                <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Instrucciones de Importación
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-300">
                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700/60">
                        <span class="font-bold text-emerald-400 block mb-1">1. Descargar Plantilla</span>
                        Descargue el formato oficial con las cabeceras predefinidas.
                        <a href="{{ route('products.template') }}" class="mt-2 inline-flex items-center gap-1 text-indigo-400 hover:text-indigo-300 font-bold block">
                            📥 Descargar Plantilla (.xlsx)
                        </a>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700/60">
                        <span class="font-bold text-blue-400 block mb-1">2. Estructura de Datos</span>
                        Columnas requeridas: <code class="text-indigo-300">nombre</code>, <code class="text-indigo-300">descripcion</code>, <code class="text-indigo-300">precio</code>, <code class="text-indigo-300">stock</code> y <code class="text-indigo-300">categoria</code>.
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700/60">
                        <span class="font-bold text-purple-400 block mb-1">3. Regla de Negocio</span>
                        Si el producto ya existe (por nombre), actualiza precio y categoría sin afectar el stock físico auditado.
                    </div>
                </div>
            </div>

            <!-- Formulario de carga -->
            <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-sm">
                <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label for="file" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
                            Seleccione el Archivo de Excel (.xlsx, .xls, .csv, máx. 5 MB) <span class="text-indigo-400">*</span>
                        </label>
                        <input id="file" type="file" name="file" accept=".xlsx,.xls,.csv" 
                               class="w-full text-sm text-slate-400 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer cursor-pointer bg-slate-950/80 border border-slate-700 rounded-xl p-2 transition shadow-inner" required>
                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-700/80">
                        <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-700/60 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-600 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition transform hover:-translate-y-0.5">
                            Subir y Procesar Archivo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reporte de fallas (si las hay) -->
            @if (session('failures'))
                <div class="bg-slate-800/90 border border-rose-500/40 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-rose-400 flex items-center gap-2">
                            <span>⚠️</span> Filas omitidas por validación ({{ count(session('failures')) }})
                        </h3>
                        <span class="text-xs text-slate-400">Las filas válidas sí fueron importadas con éxito</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-700/80">
                        <table class="min-w-full text-xs text-left">
                            <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-700/80">
                                <tr>
                                    <th class="py-3 px-4">Fila</th>
                                    <th class="py-3 px-4">Columna</th>
                                    <th class="py-3 px-4">Error Detectado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/60 text-slate-300 bg-slate-900/60">
                                @foreach (session('failures') as $failure)
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="py-3 px-4 font-bold text-white font-mono">#{{ $failure['fila'] }}</td>
                                        <td class="py-3 px-4 font-mono text-indigo-300">{{ $failure['columna'] }}</td>
                                        <td class="py-3 px-4 text-rose-300">{{ implode(' ', (array) $failure['errores']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
