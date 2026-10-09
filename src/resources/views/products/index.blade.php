<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-black text-2xl text-white tracking-tight">
                    Inventario de Productos {{ $showTrashed ? '— Papelera de Reciclaje' : '' }}
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Catálogo comercial de ceras, perfumes, navajas y cuidado personal</p>
            </div>
            <div class="flex items-center gap-2">
                @can('crear-productos')
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo Producto
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

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
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

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o descripción..." class="text-xs bg-slate-900 border-slate-700/80 text-white placeholder-slate-500 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 w-48 lg:w-64">

                        <select name="category" class="text-xs bg-slate-900 border-slate-700/80 text-slate-200 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas las categorías</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="text-xs bg-slate-900 border-slate-700/80 text-slate-200 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los estados</option>
                            <option value="active" @selected(request('status') === 'active')>Activos</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                        </select>

                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-md shadow-indigo-600/20">
                            Filtrar
                        </button>
                    </form>
                </div>

                <!-- Tabla de Productos -->
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left divide-y divide-slate-800/80">
                        <thead class="bg-slate-950 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Producto</th>
                                <th class="py-3.5 px-4">Línea / Categoría</th>
                                <th class="py-3.5 px-4 text-right">Precio Unitario</th>
                                <th class="py-3.5 px-4 text-center">Stock</th>
                                <th class="py-3.5 px-4 text-center">Estado</th>
                                <th class="py-3.5 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40">
                            @forelse ($products as $product)
                                <tr class="hover:bg-slate-800/60 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-white">{{ $product->name }}</div>
                                        @if ($product->description)
                                            <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5">{{ $product->description }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700/60">
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
</x-app-layout>
