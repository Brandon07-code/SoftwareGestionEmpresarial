<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-white tracking-tight leading-tight">
            Registrar Nuevo Cliente
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 p-6 sm:p-8 backdrop-blur-sm">
                <form method="POST" action="{{ route('clients.store') }}" class="space-y-5">
                    @csrf
                    @include('clients.partials.form')

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-800">
                        <x-primary-button>
                            Guardar Cliente
                        </x-primary-button>
                        <a href="{{ route('clients.index') }}" class="text-xs text-slate-400 hover:text-slate-200 underline font-semibold transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
