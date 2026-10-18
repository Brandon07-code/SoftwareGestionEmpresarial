<x-app-layout>
    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div>
                <a href="{{ route('providers.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-2">
                    ← Volver al directorio
                </a>
                <h1 class="text-2xl font-black text-white tracking-tight">Editar Proveedor</h1>
                <p class="text-sm text-slate-400">Actualiza los datos de «{{ $provider->name }}».</p>
            </div>

            <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                <form method="POST" action="{{ route('providers.update', $provider) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @include('providers.partials.form')

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-800">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                            Actualizar Proveedor
                        </button>
                        <a href="{{ route('providers.index') }}" class="text-sm text-slate-400 hover:text-slate-200 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
