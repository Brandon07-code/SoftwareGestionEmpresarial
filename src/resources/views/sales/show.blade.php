<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white tracking-tight leading-tight flex items-center gap-2">
                <span>Factura de Venta:</span>
                <span class="text-indigo-400 font-mono">{{ $sale->invoice_number }}</span>
            </h2>
            <a href="{{ route('sales.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-bold transition shadow-sm">
                ← Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 p-6 sm:p-8 space-y-6 backdrop-blur-sm">

                <!-- Encabezado de la Factura -->
                <div class="p-6 bg-slate-950/60 rounded-xl border border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Datos del Cliente</span>
                        <p class="font-black text-white text-base mt-1">{{ $sale->client?->name ?? 'Consumidor Final' }}</p>
                        <p class="text-xs text-slate-300 font-mono mt-0.5">
                            {{ $sale->client?->document_type }}: {{ $sale->client?->document_number }}
                        </p>
                        @if($sale->client?->email)
                            <p class="text-xs text-slate-400 mt-0.5">✉️ {{ $sale->client->email }}</p>
                        @endif
                        @if($sale->client?->phone)
                            <p class="text-xs text-slate-400 mt-0.5">📞 {{ $sale->client->phone }}</p>
                        @endif
                    </div>

                    <div class="md:text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Información de Emisión</span>
                        <p class="font-mono font-bold text-white text-sm mt-1">📅 {{ $sale->sale_date->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-300 mt-0.5">Vendedor responsable: <strong class="text-white">{{ $sale->user?->name ?? 'Admin' }}</strong></p>
                        <div class="mt-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $sale->status === 'pagada' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : ($sale->status === 'anulada' ? 'bg-rose-500/15 text-rose-300 border border-rose-500/30' : 'bg-cyan-500/15 text-cyan-300 border border-cyan-500/30') }}">
                                Estado: {{ $sale->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/40">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-950 text-slate-400 uppercase font-bold text-[11px] border-b border-slate-800">
                            <tr class="text-left">
                                <th class="py-3 px-4">Producto Facturado</th>
                                <th class="py-3 px-4 text-center w-24">Cantidad</th>
                                <th class="py-3 px-4 text-right w-36">Precio Unitario</th>
                                <th class="py-3 px-4 text-right w-40">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 text-slate-300">
                            @foreach ($sale->details as $detail)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="py-3 px-4 font-bold text-white">
                                        {{ $detail->product?->name ?? 'Producto no disponible' }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-indigo-300">
                                        {{ $detail->quantity }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-300">
                                        ${{ number_format((float) $detail->unit_price, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-white">
                                        ${{ number_format((float) $detail->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totales e Impuesto -->
                <div class="flex justify-end pt-2">
                    <div class="w-full sm:w-80 bg-slate-950/60 p-5 rounded-xl border border-slate-800 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-300">
                            <span>Subtotal (Base Gravable):</span>
                            <span class="font-mono font-bold text-white">${{ number_format((float) $sale->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>IVA (19% DIAN):</span>
                            <span class="font-mono font-bold text-cyan-400">${{ number_format((float) $sale->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t border-slate-800 text-white font-black">
                            <span>Total Facturado:</span>
                            <span class="font-mono text-emerald-400 text-base">${{ number_format((float) $sale->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($sale->notes)
                    <div class="p-4 bg-slate-950/40 rounded-xl border border-slate-800/80 text-xs text-slate-300">
                        <strong class="text-white block mb-0.5">Observaciones:</strong>
                        {{ $sale->notes }}
                    </div>
                @endif

                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <a href="{{ route('sales.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold transition">
                        ← Volver al Registro de Ventas
                    </a>

                    @can('eliminar-ventas')
                        @if($sale->status !== 'anulada')
                            <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('¿Anular esta factura? El stock será devuelto automáticamente al catálogo.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-800 text-xs font-bold transition shadow-sm">
                                    Anular Factura & Revertir Stock
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
