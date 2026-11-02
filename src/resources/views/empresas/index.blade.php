<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-black text-2xl text-white tracking-tight">
                    Gestión de Organizaciones & Multi-Tenant
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Configuración corporativa, datos tributarios y parametrización de empresas del sistema</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('empresas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nueva Organización
                </a>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($empresas as $empresa)
                    <div class="rounded-2xl bg-slate-900/90 border border-slate-800 p-6 shadow-2xl backdrop-blur-sm space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-blue-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 font-black text-lg">
                                        {{ strtoupper(substr($empresa->nombre_comercial, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-base text-white leading-tight">{{ $empresa->nombre_comercial }}</h3>
                                        <p class="text-xs text-slate-400">{{ $empresa->razon_social }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $empresa->activo ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                    {{ $empresa->activo ? '● Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3 text-xs border-y border-slate-800/80 py-3.5">
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block">NIT / Tributario</span>
                                    <span class="font-mono font-bold text-slate-200">{{ $empresa->nit }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Giro de Negocio</span>
                                    <span class="font-semibold text-cyan-400 uppercase text-[11px]">{{ $empresa->tipo_negocio }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Ubicación</span>
                                    <span class="text-slate-300">{{ $empresa->ciudad ?? 'No asignada' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Moneda Base</span>
                                    <span class="font-mono font-bold text-amber-400">{{ $empresa->moneda }}</span>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center gap-4 text-xs text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span><strong>{{ $empresa->sucursales_count }}</strong> Sedes / Sucursales</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span><strong>{{ $empresa->cajas_count }}</strong> Cajas POS</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                            <span class="text-[11px] text-slate-500">ID Tenant: #{{ $empresa->id }}</span>
                            <a href="{{ route('empresas.edit', $empresa) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition">
                                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Configurar Empresa
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
