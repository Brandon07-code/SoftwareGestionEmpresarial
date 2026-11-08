<x-app-layout>
    <div class="py-8 bg-slate-900 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-2">
                        ← Volver al Catálogo
                    </a>
                    <h1 class="text-2xl font-black text-white tracking-tight">Editar Producto: {{ $product->name }}</h1>
                    <p class="text-sm text-slate-400">Modifica precio, categoría o especificaciones del artículo.</p>
                </div>
            </div>

            <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-sm">
                <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('products.partials.form')

                    <div class="flex items-center justify-between pt-4 border-t border-slate-700/80">
                        <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-700/60 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-600 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
