<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-black text-2xl text-white tracking-tight">
                    Registro de Ventas & Facturación
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Control transaccional con descuento automático de inventario e IVA del 19%</p>
            </div>
            <div class="flex items-center gap-2">
                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva Venta
                    </a>
                @endcan
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

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold text-sm flex items-center gap-3 shadow-md">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
                <!-- Toolbar y Métricas de Cabecera -->
                <div class="p-5 border-b border-slate-800/80 bg-slate-950/60 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 flex items-center gap-2 shadow-sm">
                            <span class="text-slate-400 uppercase font-bold text-[10px] tracking-wider">Total Facturado Mes:</span>
                            <strong class="text-emerald-400 font-black text-sm font-mono">${{ number_format((float) $totalMonth, 2) }}</strong>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por factura o cliente..." class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs placeholder-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 px-3 py-2 w-56">

                        <input type="date" name="from" value="{{ request('from') }}" class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs px-2.5 py-2 focus:border-indigo-500">
                        <span class="text-slate-500 text-xs">a</span>
                        <input type="date" name="to" value="{{ request('to') }}" class="bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs px-2.5 py-2 focus:border-indigo-500">

                        <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition">
                            Filtrar
                        </button>
                    </form>
                </div>

                <!-- Tabla de Ventas -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-[11px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-800/80">
                            <tr>
                                <th class="py-4 px-5">Factura</th>
                                <th class="py-4 px-5">Fecha</th>
                                <th class="py-4 px-5">Cliente</th>
                                <th class="py-4 px-5">Vendedor</th>
                                <th class="py-4 px-5">Total</th>
                                <th class="py-4 px-5">Estado</th>
                                <th class="py-4 px-5 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="py-4 px-5 font-mono font-black text-indigo-400 text-sm">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="py-4 px-5 font-mono text-slate-300">
                                        {{ $sale->sale_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-4 px-5 font-bold text-white text-sm">
                                        {{ $sale->client?->name ?? 'Cliente no asignado' }}
                                        <span class="text-[10px] text-slate-400 block font-mono font-normal">
                                            {{ $sale->client?->document_type }} {{ $sale->client?->document_number }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-slate-300">
                                        {{ $sale->user?->name ?? 'Sistema' }}
                                    </td>
                                    <td class="py-4 px-5 font-black text-white text-sm font-mono">
                                        ${{ number_format((float) $sale->total, 2) }}
                                    </td>
                                    <td class="py-4 px-5">
                                        @if($sale->status === 'pagada')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 uppercase">
                                                Pagada
                                            </span>
                                        @elseif($sale->status === 'anulada')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-300 border border-rose-500/30 uppercase">
                                                Anulada
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 uppercase">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('sales.show', $sale) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700/80 text-xs font-bold transition shadow-sm" title="Ver Factura">
                                                Ver Factura →
                                            </a>

                                            @can('eliminar-ventas')
                                                @if($sale->status !== 'anulada')
                                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="inline" onsubmit="return confirm('¿Anular esta venta? El stock de todos los productos será devuelto automáticamente al inventario.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-950/40 hover:bg-rose-900 text-rose-400 border border-rose-900/50 text-xs font-bold transition shadow-sm" title="Anular Venta">
                                                            Anular
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                                        No hay ventas registradas con los criterios seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="p-4 bg-slate-950/60 border-t border-slate-800/80">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
