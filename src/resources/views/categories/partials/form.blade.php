<div class="space-y-6">
    <div>
        <label for="name" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
            Nombre de la Categoría <span class="text-indigo-400">*</span>
        </label>
        <input id="name" name="name" type="text" 
               class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl px-4 py-3 text-sm transition shadow-inner" 
               value="{{ old('name', $category->name ?? '') }}" 
               placeholder="Ej: Perfumería & Fragancias" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="description" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">
            Descripción (Opcional)
        </label>
        <textarea id="description" name="description" rows="4" 
                  class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 rounded-xl p-4 text-sm transition shadow-inner resize-y"
                  placeholder="Describe qué tipos de productos, servicios o fragancias pertenecen a este grupo...">{{ old('description', $category->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
        <label for="active" class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" id="active" name="active" value="1" 
                   class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0" 
                   @checked(old('active', $category->active ?? true))>
            <div>
                <span class="block text-sm font-semibold text-white">Categoría Activa</span>
                <span class="block text-xs text-slate-400">Si está activa, estará disponible en catálogo, ventas y reportes</span>
            </div>
        </label>
        <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold uppercase text-[10px]">
            Habilitada
        </span>
    </div>
</div>
