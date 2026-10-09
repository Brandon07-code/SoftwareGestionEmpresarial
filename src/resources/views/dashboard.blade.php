<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-black text-2xl text-white tracking-tight">Panel de Gestión ERP</h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Sede Principal Cartago • Sistema Centralizado de Operaciones</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-950/80 text-indigo-300 border border-indigo-700/50 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    Rol Activo: {{ $roles->isNotEmpty() ? ucfirst($roles->first()) : 'Sin rol' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Banner de Bienvenida Ejecutivo con Iluminación Ambiental -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white p-6 md:p-8 shadow-2xl border border-indigo-900/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <!-- Orbes de luz ambiental de fondo -->
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="space-y-2.5 relative z-10">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-blue-500/20 text-blue-300 border border-blue-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                            ERP EN LÍNEA
                        </span>
                        <span class="text-xs text-slate-400">• {{ ucfirst(\Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                        Bienvenido, {{ $user->name }}
                    </h2>
                    <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Control integral de servicios de belleza, catálogo comercial, inventario con auditoría segura y pasarelas de recaudo digital.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 relative z-10">
                    <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Agendar Cita
                    </a>
                    @can('crear-productos')
                        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white border border-white/15 font-semibold text-xs tracking-wider rounded-xl backdrop-blur-sm transition">
                            + Producto
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Tarjetas KPI de Métricas Profesionales (Sin Bordes) -->
            @if ($stats->isNotEmpty())
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Métricas Clave del Negocio</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($stats as $stat)
                            @php
                                $isProduct = str_contains(strtolower($stat['label']), 'producto');
                                $isStock = str_contains(strtolower($stat['label']), 'stock');
                                $isValue = str_contains(strtolower($stat['label']), 'valor');
                                $isCategory = str_contains(strtolower($stat['label']), 'categor');

                                if ($isProduct) {
                                    $iconBg = 'bg-blue-500/15 text-blue-400';
                                    $pillBg = 'bg-blue-500/10 text-blue-300 border border-blue-500/20';
                                    $pillText = 'Catálogo';
                                } elseif ($isStock) {
                                    $isAlert = (int) $stat['value'] > 0;
                                    $iconBg = $isAlert ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/15 text-emerald-400';
                                    $pillBg = $isAlert ? 'bg-rose-500/15 text-rose-300 border border-rose-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20';
                                    $pillText = $isAlert ? 'Alerta' : 'En orden';
                                } elseif ($isValue) {
                                    $iconBg = 'bg-emerald-500/15 text-emerald-400';
                                    $pillBg = 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20';
                                    $pillText = 'Valuado';
                                } else {
                                    $iconBg = 'bg-indigo-500/15 text-indigo-400';
                                    $pillBg = 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20';
                                    $pillText = 'Departamentos';
                                }
                            @endphp
                            <div class="bg-slate-900/90 rounded-2xl p-5 shadow-lg hover:shadow-2xl hover:bg-slate-900 transition duration-200 group backdrop-blur-sm">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                                    <span class="w-9 h-9 rounded-xl {{ $iconBg }} flex items-center justify-center transition-transform group-hover:scale-110">
                                        @if ($isProduct)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        @elseif ($isStock)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        @elseif ($isValue)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11-0.208-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        @endif
                                    </span>
                                </div>
                                <p class="text-2xl md:text-3xl font-black text-white mt-2 tracking-tight">{{ $stat['value'] }}</p>
                                <div class="mt-2.5 flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="flex items-center gap-1 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Datos en vivo
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $pillBg }}">
                                        {{ $pillText }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Cuadrícula Modular de Operaciones ERP -->
            <div class="bg-slate-900/80 rounded-2xl p-6 md:p-8 shadow-xl border border-slate-800/80 space-y-6 backdrop-blur-sm">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                    <div>
                        <h3 class="font-bold text-lg text-white">Módulos del Sistema ERP</h3>
                        <p class="text-xs text-slate-400">Accede directamente a los módulos operacionales según tus permisos asignados.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @can('ver-productos')
                        <a href="{{ route('products.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-blue-500/50 hover:shadow-xl hover:shadow-blue-500/5 transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center font-bold group-hover:bg-blue-600 group-hover:text-white transition duration-200 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-blue-300 flex items-center gap-1">
                                    Ir al módulo <span class="group-hover:translate-x-1 transition-transform">→</span>
                                </span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-blue-300 transition">Inventario de Productos</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Administración de existencias, precios de lociones, ceras, navajas y papelera con Soft Delete.</p>
                        </a>
                    @endcan

                    @can('ver-categorias')
                        <a href="{{ route('categories.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-indigo-500/50 hover:shadow-xl hover:shadow-indigo-500/5 transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold group-hover:bg-indigo-600 group-hover:text-white transition duration-200 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-indigo-300 flex items-center gap-1">
                                    Ir al módulo <span class="group-hover:translate-x-1 transition-transform">→</span>
                                </span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-indigo-300 transition">Categorías de Catálogo</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Organización por líneas de negocio (Perfumería, Barbería & Afeitado, Cuidado Capilar).</p>
                        </a>
                    @endcan

                    <a href="{{ route('citas.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/5 transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold group-hover:bg-emerald-600 group-hover:text-white transition duration-200 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 group-hover:text-emerald-300 flex items-center gap-1">
                                Ir al módulo <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </span>
                        </div>
                        <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-emerald-300 transition">Agenda de Turnos</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Programación de citas para cortes, degradés, rituales de barba y asignación de barberos.</p>
                    </a>

                    <a href="{{ route('terceros.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-violet-500/50 hover:shadow-xl hover:shadow-violet-500/5 transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="w-10 h-10 rounded-xl bg-violet-500/15 text-violet-400 flex items-center justify-center font-bold group-hover:bg-violet-600 group-hover:text-white transition duration-200 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 group-hover:text-violet-300 flex items-center gap-1">
                                Ir al módulo <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </span>
                        </div>
                        <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-violet-300 transition">Gestión de Terceros</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Directorio unificado de Clientes frecuentes, Proveedores de insumos y Personal de salón.</p>
                    </a>

                    <a href="{{ route('pagos.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-sky-500/50 hover:shadow-xl hover:shadow-sky-500/5 transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center font-bold group-hover:bg-sky-600 group-hover:text-white transition duration-200 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 group-hover:text-sky-300 flex items-center gap-1">
                                Ir al módulo <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </span>
                        </div>
                        <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-sky-300 transition">Pasarelas de Pago QR</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Cobros rápidos mediante Nequi y transferencias con generación de código QR dinámico.</p>
                    </a>

                    <a href="{{ route('crm.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-rose-500/50 hover:shadow-xl hover:shadow-rose-500/5 transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="w-10 h-10 rounded-xl bg-rose-500/15 text-rose-400 flex items-center justify-center font-bold group-hover:bg-rose-600 group-hover:text-white transition duration-200 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 group-hover:text-rose-300 flex items-center gap-1">
                                Ir al módulo <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </span>
                        </div>
                        <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-rose-300 transition">CRM & Fidelización</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Registro de interacciones, acumulación de puntos de fidelidad y canjes de recompensas.</p>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
