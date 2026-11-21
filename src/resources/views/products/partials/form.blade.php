<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="name" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
                Nombre del Producto <span class="text-indigo-400">*</span>
            </label>
            <input id="name" name="name" type="text" 
                   class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl px-4 py-3 text-sm transition shadow-inner" 
                   value="{{ old('name', $product->name ?? '') }}" 
                   placeholder="Ej: Cera Mate Fijación Fuerte Uppercut" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="category_id" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
                Categoría del Sistema <span class="text-indigo-400">*</span>
            </label>
            <select id="category_id" name="category_id" 
                    class="w-full bg-slate-950/80 border border-slate-700 text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl px-4 py-3 text-sm transition shadow-inner" required>
                <option value="" class="bg-slate-900 text-slate-400">Seleccione una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" class="bg-slate-900 text-white" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="price" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
                Precio de Venta (COP) <span class="text-indigo-400">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-sm">$</span>
                <input id="price" name="price" type="number" step="0.01" min="0" 
                       class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl pl-8 pr-4 py-3 text-sm transition shadow-inner font-mono" 
                       value="{{ old('price', $product->price ?? '') }}" 
                       placeholder="45000" required />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('price')" />
        </div>

        <div>
            @if ($product->exists)
                <label class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
                    Inventario Disponible (Solo lectura)
                </label>
                <div class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-4 py-3 flex items-center justify-between gap-3">
                    <span class="font-mono font-bold text-base text-white">{{ $product->stock }} uds</span>
                    <div class="flex items-center gap-2">
                        @can('ver-inventario')
                            <a href="{{ route('kardex.index', ['product_id' => $product->id]) }}" class="text-xs font-bold text-indigo-300 hover:text-indigo-200 underline">
                                @can('ajustar-inventario')
                                    Ajustar en Kárdex →
                                @else
                                    Ver Kárdex →
                                @endcan
                            </a>
                        @endcan
                    </div>
                </div>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    El stock solo se modifica mediante Compras, Ventas o Ajuste de Kárdex.
                </p>
            @else
                <label for="stock" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
                    Stock Inicial (Unidades) <span class="text-indigo-400">*</span>
                </label>
                <input id="stock" name="stock" type="number" min="0"
                       class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl px-4 py-3 text-sm transition shadow-inner font-mono"
                       value="{{ old('stock', $product->stock ?? 0) }}"
                       placeholder="10" required />
                <p class="mt-1.5 text-[11px] text-slate-400">
                    Si es mayor a 0, se registrará automáticamente un asiento <code class="text-indigo-300">AJUSTE_INICIAL</code> en Kárdex.
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('stock')" />
            @endif
        </div>
    </div>

    <div>
        <label for="description" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
            Descripción Detallada (Opcional)
        </label>
        <textarea id="description" name="description" rows="3" 
                  class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl p-4 text-sm transition shadow-inner resize-y"
                  placeholder="Detalles sobre presentación, fragancia, ingredientes o especificaciones técnicas...">{{ old('description', $product->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
        <label for="active" class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" id="active" name="active" value="1" 
                   class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0" 
                   @checked(old('active', $product->active ?? true))>
            <div>
                <span class="block text-sm font-semibold text-white">Producto Disponible para la Venta</span>
                <span class="block text-xs text-slate-400">Si está inactivo, no aparecerá en el punto de venta ni en las compras</span>
            </div>
        </label>
        <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold uppercase text-[10px]">
            Habilitado
        </span>
    </div>
</div>
