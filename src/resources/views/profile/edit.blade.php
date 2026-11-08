<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded-full border border-indigo-500/20">
                    Configuración de Cuenta
                </span>
                <h2 class="font-black text-2xl text-white tracking-tight leading-tight mt-1">
                    Mi Perfil — {{ auth()->user()->name }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs font-mono text-slate-300">
                    Rol: <strong class="text-indigo-300">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</strong>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="p-6 sm:p-8 bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl backdrop-blur-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl backdrop-blur-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl backdrop-blur-sm">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
