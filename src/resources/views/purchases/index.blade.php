<x-app-layout>
    @php
        $statusColors = [
            'pendiente' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            'parcial'   => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
            'recibida'  => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            'anulada'   => 'bg-slate-700/50 text-slate-400 border border-slate-600/50',
        ];
    @endphp

    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wider uppercase bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Abastecimiento & Bodega</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300">Procure-to-Pay</span>
                    </div>
                    <h1 class="text-2xl font-black text-white tracking-tight mt-1">Órdenes de Compra</h1>
                    <p class="text-sm text-slate-400">Recepción de mercancía de proveedores con incremento controlado de stock físico.</p>
                </div>

                <div class="flex items-center gap-3">
                    @can('crear-compras')
                        <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nueva Orden de Compra
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Summary KPI Pills --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 shadow-xl backdrop-blur-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400">Compras del Mes</p>
                        <p class="text-xl font-bold text-white mt-0.5">${{ number_format((float) $totalMonth, 2) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold">
                        COP
                    </div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 shadow-xl backdrop-blur-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400">Órdenes por Recibir</p>
                        <p class="text-xl font-bold text-amber-400 mt-0.5">{{ $pendingCount }} pendientes</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 shadow-xl backdrop-blur-sm">
                <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por OC, factura o proveedor..." 
                            class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                    </div>
                    <div class="sm:col-span-3">
                        <select name="status" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            <option value="">Todos los estados</option>
                            @foreach (['pendiente' => 'Pendiente', 'parcial' => 'Parcial', 'recibida' => 'Recibida', 'anulada' => 'Anulada'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <input type="date" name="from" value="{{ request('from') }}" title="Desde"
                            class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold border border-slate-700 transition">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-slate-800/80">
                        <thead class="bg-slate-950/60">
                            <tr class="text-left text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3.5 px-4">Orden</th>
                                <th class="py-3.5 px-4">Fecha</th>
                                <th class="py-3.5 px-4">Proveedor</th>
                                <th class="py-3.5 px-4">Registró</th>
                                <th class="py-3.5 px-4 text-right">Total</th>
                                <th class="py-3.5 px-4 text-center">Estado</th>
                                <th class="py-3.5 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-200">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-indigo-400">
                                        {{ $purchase->purchase_number }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap text-slate-300">
                                        {{ $purchase->purchase_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-white">
                                        {{ $purchase->provider?->name }}
                                        @if ($purchase->provider_invoice)
                                            <span class="block text-xs font-mono text-slate-500">Fact: {{ $purchase->provider_invoice }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400 text-xs">
                                        {{ $purchase->user?->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-semibold text-white whitespace-nowrap">
                                        ${{ number_format((float) $purchase->total, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$purchase->status] ?? '' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap font-semibold text-xs">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-400 hover:text-indigo-300 transition">
                                            Ver
                                        </a>
                                        <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank" class="text-cyan-400 hover:text-cyan-300 transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            PDF
                                        </a>

                                        @can('eliminar-compras')
                                            @if ($purchase->canBeCancelled())
                                                <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="inline"
                                                    onsubmit="return confirm('¿Anular esta orden de compra? (Solo permitido en estado pendiente)')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-rose-400 hover:text-rose-300 transition">
                                                        Anular
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">No hay órdenes de compra registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($purchases->hasPages())
                    <div class="p-4 border-t border-slate-800">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
