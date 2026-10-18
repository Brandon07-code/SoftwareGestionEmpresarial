<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded-full border border-indigo-500/20">
                    Plataforma Modular Transaccional
                </span>
                <h2 class="font-black text-2xl text-white tracking-tight leading-tight mt-1">
                    Panel Ejecutivo — Bienvenido, {{ auth()->user()->name }}
                </h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-slate-400">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Empresa Activa: <strong class="text-white">Barbería & Perfumería JyM</strong> (Sede Cartago)</span>
                    <span class="text-slate-600">|</span>
                    <span>Roles: <strong class="text-indigo-300">{{ $roles->isEmpty() ? 'Sin rol asignado' : $roles->join(', ') }}</strong></span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="px-3 py-1 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs font-mono text-slate-300">
                    {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- 1. Tarjetas de Métricas Clave (KPIs) Dinámicas por Permiso --}}
            @if ($stats->isNotEmpty())
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Métricas Operativas & Financieras en Vivo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($stats as $stat)
                            <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 shadow-xl backdrop-blur-sm transition duration-200 hover:border-slate-700">
                                <span class="text-xs font-medium text-slate-400 block">{{ $stat['label'] }}</span>
                                <p class="text-xl font-black text-white mt-1 font-mono tracking-tight">{{ $stat['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 2. Sección Analítica: Top 5 Vendidos y Sugerencia de Reabastecimiento --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Top 5 Vendidos --}}
                @if (isset($topProducts) && $topProducts->isNotEmpty())
                    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl backdrop-blur-sm p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div>
                                <h3 class="font-bold text-white text-base flex items-center gap-2">
                                    <span>🔥</span> Top 5 Productos Más Vendidos Este Mes
                                </h3>
                                <p class="text-xs text-slate-400">Ranking acumulado según detalles de venta liquidados</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800/80">
                                    <tr>
                                        <th class="py-2.5 px-3">#</th>
                                        <th class="py-2.5 px-3">Producto</th>
                                        <th class="py-2.5 px-3 text-right">Unidades Vendidas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                    @foreach ($topProducts as $i => $item)
                                        <tr class="hover:bg-slate-800/30 transition">
                                            <td class="py-2.5 px-3 font-mono font-bold text-indigo-400">
                                                #{{ $i + 1 }}
                                            </td>
                                            <td class="py-2.5 px-3 font-semibold text-white">
                                                {{ $item->product?->name ?? 'Producto no disponible' }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-400">
                                                {{ $item->total_sold }} uds
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Reabastecimiento: Stock Crítico (≤ 5) --}}
                @if (isset($lowStock) && $lowStock->isNotEmpty())
                    <div class="bg-slate-900/80 border border-amber-500/30 rounded-2xl shadow-xl backdrop-blur-sm p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div>
                                <h3 class="font-bold text-amber-300 text-base flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Productos para Reabastecer (Stock ≤ 5)
                                </h3>
                                <p class="text-xs text-slate-400">Referencias en riesgo de desabastecimiento comercial</p>
                            </div>
                            @can('crear-compras')
                                <a href="{{ route('purchases.create') }}" class="px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                    + Crear Orden de Compra
                                </a>
                            @endcan
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800/80">
                                    <tr>
                                        <th class="py-2.5 px-3">Producto</th>
                                        <th class="py-2.5 px-3 text-right">Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                    @foreach ($lowStock as $prod)
                                        <tr class="hover:bg-slate-800/30 transition">
                                            <td class="py-2.5 px-3 font-semibold text-white">
                                                {{ $prod->name }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-black {{ $prod->stock === 0 ? 'text-rose-400' : 'text-amber-400' }}">
                                                {{ $prod->stock }} uds {{ $prod->stock === 0 ? '(AGOTADO)' : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            {{-- 3. Módulos Operativos del ERP --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Módulos Operativos de Turnix ERP
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @can('ver-ventas')
                        <a href="{{ route('sales.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-emerald-500/50 hover:shadow-xl transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold group-hover:bg-emerald-600 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-emerald-300">Ir al módulo →</span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-emerald-300 transition">Ventas & Facturación</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Registro de transacciones, IVA 19%, descuento automático de inventario y PDFs.</p>
                        </a>
                    @endcan

                    @can('ver-compras')
                        <a href="{{ route('purchases.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-indigo-500/50 hover:shadow-xl transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold group-hover:bg-indigo-600 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-indigo-300">Ir al módulo →</span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-indigo-300 transition">Compras & Abastecimiento</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Órdenes de compra, recepción total/parcial con aumento de stock y órdenes PDF.</p>
                        </a>
                    @endcan

                    @can('ver-proveedores')
                        <a href="{{ route('providers.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-cyan-500/50 hover:shadow-xl transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/15 text-cyan-400 flex items-center justify-center font-bold group-hover:bg-cyan-600 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-cyan-300">Ir al módulo →</span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-cyan-300 transition">Proveedores</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Directorio de laboratorios, distribuidores e importadores con control de borrado.</p>
                        </a>
                    @endcan

                    @can('ver-clientes')
                        <a href="{{ route('clients.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-blue-500/50 hover:shadow-xl transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center font-bold group-hover:bg-blue-600 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-blue-300">Ir al módulo →</span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-blue-300 transition">Clientes</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Gestión de cédulas, NITs, historial de compras y papelera de reciclaje.</p>
                        </a>
                    @endcan

                    @can('ver-productos')
                        <a href="{{ route('products.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-purple-500/50 hover:shadow-xl transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center font-bold group-hover:bg-purple-600 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-purple-300">Ir al módulo →</span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-purple-300 transition">Inventario & Catálogo</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Control de existencias físicas en bodega, precios comerciales y alertas de stock bajo.</p>
                        </a>
                    @endcan

                    @can('ver-categorias')
                        <a href="{{ route('categories.index') }}" class="group block p-5 rounded-2xl border border-slate-800/80 bg-slate-950/60 hover:bg-slate-800/80 hover:border-pink-500/50 hover:shadow-xl transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-pink-500/15 text-pink-400 flex items-center justify-center font-bold group-hover:bg-pink-600 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 group-hover:text-pink-300">Ir al módulo →</span>
                            </div>
                            <h4 class="font-bold text-white text-base mt-3.5 group-hover:text-pink-300 transition">Categorías</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Familias de productos: Perfumería, Barbería & Afeitado, Cuidado Capilar.</p>
                        </a>
                    @endcan
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
