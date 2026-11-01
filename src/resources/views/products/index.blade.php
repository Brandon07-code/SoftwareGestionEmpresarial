<x-app-layout>
    <div x-data="{ openImportModal: false }">
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="font-black text-2xl text-white tracking-tight">
                        Inventario de Productos {{ $showTrashed ? '— Papelera de Reciclaje' : '' }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Catálogo comercial de ceras, perfumes, navajas y cuidado personal</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('kardex.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-indigo-300 font-bold text-xs rounded-xl border border-slate-700 transition" title="Consultar bitácora de movimientos físicos">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Kárdex
                    </a>

                    <a href="{{ route('products.export.excel') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600/15 hover:bg-emerald-600/25 text-emerald-300 font-bold text-xs rounded-xl border border-emerald-500/30 transition shadow-sm" title="Exportar catálogo completo a formato Excel (.xlsx)">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Exportar Excel
                    </a>

                    @can('crear-productos')
                        <button type="button" @click="openImportModal = true" class="inline-flex items-center gap-1.5 px-3 py-2 bg-teal-600/15 hover:bg-teal-600/25 text-teal-300 font-bold text-xs rounded-xl border border-teal-500/30 transition shadow-sm" title="Importar catálogo masivamente desde archivo Excel o CSV">
                            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Importar Excel
                        </button>

                        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nuevo
                        </a>
                    @endcan
                </div>
            </div>
        </x-slot>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                @if (session('success'))
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                        <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Reporte de Errores de Validación Fila por Fila (Clase 13 - Requisito 13) --}}
                @if (session('import_failures'))
                    <div class="p-5 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-200 shadow-xl space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="font-bold text-sm text-rose-300">Reporte de Inconsistencias Fila por Fila (Validación Excel)</h3>
                        </div>
                        <p class="text-xs text-rose-300/80">Las siguientes filas presentaron errores de validación y fueron omitidas para resguardar la integridad del catálogo:</p>
                        <div class="overflow-x-auto rounded-xl border border-rose-500/30 bg-slate-950/70">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-rose-950/60 text-[10px] uppercase font-bold text-rose-300 border-b border-rose-500/30">
                                    <tr>
                                        <th class="py-2.5 px-4">Fila Excel</th>
                                        <th class="py-2.5 px-4">Campo con Inconsistencia</th>
                                        <th class="py-2.5 px-4">Detalle del Error</th>
                                        <th class="py-2.5 px-4">Valores Leídos</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-500/20 text-slate-300">
                                    @foreach (session('import_failures') as $failure)
                                        <tr class="hover:bg-rose-500/5">
                                            <td class="py-2.5 px-4 font-mono font-bold text-rose-400">Fila {{ $failure['row'] }}</td>
                                            <td class="py-2.5 px-4 font-semibold text-white capitalize">{{ $failure['attribute'] }}</td>
                                            <td class="py-2.5 px-4 text-rose-300">
                                                @foreach ($failure['errors'] as $err)
                                                    <div>• {{ $err }}</div>
                                                @endforeach
                                            </td>
                                            <td class="py-2.5 px-4 font-mono text-[11px] text-slate-400">
                                                {{ json_encode($failure['values'] ?? [], JSON_UNESCAPED_UNICODE) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
                    <!-- Toolbar y Filtros -->
                    <div class="p-5 border-b border-slate-800/80 bg-slate-950/60 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-2">
                            @can('eliminar-productos')
                                @if ($showTrashed)
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-200 transition">
                                        ← Volver a productos activos
                                    </a>
                                @else
                                    <a href="{{ route('products.index', ['trashed' => 1]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-850 border border-slate-800 hover:bg-rose-500/10 hover:text-rose-300 hover:border-rose-500/30 text-slate-400 transition">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Ver Papelera ({{ $trashedCount }})
                                    </a>
                                @endif
                            @endcan
                        </div>

                        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-2">
                            @if ($showTrashed)
                                <input type="hidden" name="trashed" value="1">
                            @endif

                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o descripción..." class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 px-3 py-2 w-56">

                            <select name="category" class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs px-2.5 py-2 focus:border-indigo-500">
                                <option value="">Todas las categorías</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="status" class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs px-2.5 py-2 focus:border-indigo-500">
                                <option value="">Todos los estados</option>
                                <option value="active" @selected(request('status') === 'active')>Activos</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                            </select>

                            <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition">
                                Filtrar
                            </button>
                        </form>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/80 text-[11px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-800/80">
                                <tr>
                                    <th class="py-3.5 px-4">Producto</th>
                                    <th class="py-3.5 px-4">Categoría</th>
                                    <th class="py-3.5 px-4 text-right">Precio</th>
                                    <th class="py-3.5 px-4 text-center">Stock</th>
                                    <th class="py-3.5 px-4 text-center">Estado</th>
                                    <th class="py-3.5 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                @forelse ($products as $product)
                                    <tr class="hover:bg-slate-850/50 transition">
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-white text-sm">{{ $product->name }}</div>
                                            @if ($product->description)
                                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $product->description }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 border border-slate-700 text-[11px]">
                                                {{ $product->category?->name ?? 'Sin categoría' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                                            $ {{ number_format((float) $product->price, 2) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="font-mono font-semibold {{ $product->stock <= 5 ? 'text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded' : 'text-slate-300' }}">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $product->active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                                {{ $product->active ? '● Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                            @if ($product->trashed())
                                                <form method="POST" action="{{ route('products.restore', $product) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/30 transition">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('products.kardex', $product) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 border border-indigo-500/30 transition" title="Consultar Kárdex de este producto">
                                                    Kárdex
                                                </a>
                                                @can('editar-productos')
                                                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white border border-slate-700 transition">
                                                        Editar
                                                    </a>
                                                @endcan
                                                @can('eliminar-productos')
                                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('¿Enviar este producto a la papelera?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 transition">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                            No se encontraron productos registrados en esta vista.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
                        {{ $products->links() }}
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal de Importación Masiva Excel (Clase 13 - Requisito 13) -->
        <div x-show="openImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="openImportModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-5" @click.stop>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-white">Importación Masiva de Productos</h3>
                                <p class="text-xs text-slate-400">Carga por lotes desde Excel o CSV con validación fila por fila</p>
                            </div>
                        </div>
                        <button type="button" @click="openImportModal = false" class="text-slate-400 hover:text-white font-bold text-lg">✕</button>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2 text-xs text-slate-300">
                        <p class="font-semibold text-slate-200">Estructura requerida de encabezados:</p>
                        <div class="flex flex-wrap gap-1.5 font-mono text-[11px]">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700">nombre</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700">descripcion</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700">precio</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700">stock</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700">categoria</span>
                        </div>
                        <div class="pt-2 flex items-center justify-between border-t border-slate-800/60">
                            <span class="text-slate-400">¿Necesitas un modelo?</span>
                            <a href="{{ route('products.import.template') }}" class="font-bold text-emerald-400 hover:text-emerald-300 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Descargar Plantilla (.xlsx)
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('products.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Seleccionar archivo Excel / CSV</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer bg-slate-950 border border-slate-800 rounded-xl">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                            <button type="button" @click="openImportModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-600/30 transition">
                                Procesar e Importar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
