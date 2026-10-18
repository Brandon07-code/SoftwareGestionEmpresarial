<x-app-layout>
    @php
        $statusColors = [
            'pendiente' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            'parcial'   => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
            'recibida'  => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            'anulada'   => 'bg-slate-700/50 text-slate-400 border border-slate-600/50',
        ];
        $canReceive = $purchase->canBeReceived() && auth()->user()->can('recibir-compras');
    @endphp

    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Top Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-2">
                        ← Volver al listado de compras
                    </a>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black text-white tracking-tight">Orden {{ $purchase->purchase_number }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$purchase->status] ?? '' }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-300 border border-cyan-500/30 text-xs font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Descargar PDF
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-xl backdrop-blur-sm">
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block mb-2">Datos del Proveedor</span>
                    <p class="text-base font-bold text-white">{{ $purchase->provider?->name }}</p>
                    <p class="text-xs font-mono text-slate-300 mt-0.5">{{ $purchase->provider?->document_type }}: {{ $purchase->provider?->document_number }}</p>
                    <p class="text-xs text-slate-400 mt-1">Contacto: {{ $purchase->provider?->contact_name ?? '—' }} · Tel: {{ $purchase->provider?->phone ?? '—' }}</p>
                </div>

                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-xl backdrop-blur-sm">
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block mb-2">Datos de la Orden</span>
                    <p class="text-sm text-slate-300">Fecha de Registro: <strong class="text-white">{{ $purchase->purchase_date->format('d/m/Y') }}</strong></p>
                    <p class="text-sm text-slate-300">Registrado por: <strong class="text-white">{{ $purchase->user?->name }}</strong></p>
                    <p class="text-sm text-slate-300">Factura de Referencia: <strong class="text-white font-mono">{{ $purchase->provider_invoice ?? '—' }}</strong></p>
                    @if ($purchase->received_at)
                        <p class="text-xs text-emerald-400 mt-1 font-semibold">✓ Recepción completa asentada el {{ $purchase->received_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>

            {{-- Reception Form & Details Table --}}
            <form method="POST" action="{{ route('purchases.receive', $purchase) }}" 
                class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-bold text-white">Detalle de Mercancía</h3>
                        @if ($canReceive)
                            <span class="text-xs text-indigo-400 font-medium">Digita en «Llega ahora» las unidades que entran a bodega</span>
                        @endif
                    </div>

                    <div class="overflow-x-auto border border-slate-800 rounded-xl">
                        <table class="min-w-full text-sm divide-y divide-slate-800">
                            <thead class="bg-slate-950/60">
                                <tr class="text-left text-xs uppercase tracking-wider font-semibold text-slate-400">
                                    <th class="py-3 px-4">Producto</th>
                                    <th class="py-3 px-4 text-right">Pedida</th>
                                    <th class="py-3 px-4 text-right">Recibida</th>
                                    <th class="py-3 px-4 text-right">Pendiente</th>
                                    <th class="py-3 px-4 text-right">Costo Unit.</th>
                                    <th class="py-3 px-4 text-right">Subtotal</th>
                                    @if ($canReceive)
                                        <th class="py-3 px-4 w-32 text-center bg-indigo-950/40 text-indigo-300">Llega ahora</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-200">
                                @foreach ($purchase->details as $detail)
                                    <tr class="hover:bg-slate-800/30 transition">
                                        <td class="py-3 px-4 font-semibold text-white">
                                            {{ $detail->product?->name }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono">{{ $detail->quantity }}</td>
                                        <td class="py-3 px-4 text-right font-mono text-emerald-400 font-bold">{{ $detail->received_quantity }}</td>
                                        <td class="py-3 px-4 text-right font-mono text-amber-400 font-bold">{{ $detail->pendingQuantity() }}</td>
                                        <td class="py-3 px-4 text-right font-mono">${{ number_format((float) $detail->unit_cost, 2) }}</td>
                                        <td class="py-3 px-4 text-right font-mono text-white">${{ number_format((float) $detail->subtotal, 2) }}</td>
                                        @if ($canReceive)
                                            <td class="py-3 px-4 text-center bg-indigo-950/20">
                                                <input type="number" name="items[{{ $detail->id }}]" min="0"
                                                    max="{{ $detail->pendingQuantity() }}"
                                                    value="{{ old('items.'.$detail->id, $detail->pendingQuantity()) }}"
                                                    class="w-24 bg-slate-950/90 border border-indigo-500/50 text-white rounded-lg px-2.5 py-1.5 text-xs text-center font-mono focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 disabled:opacity-30 disabled:border-slate-800"
                                                    @disabled($detail->pendingQuantity() === 0)>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($canReceive)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-xl bg-indigo-950/30 border border-indigo-800/40">
                        <div class="w-full sm:w-80">
                            <label for="provider_invoice" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-1">Factura del Proveedor (Opcional)</label>
                            <input id="provider_invoice" name="provider_invoice" type="text"
                                class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3 py-2 text-xs"
                                placeholder="Ej. FAC-10492"
                                value="{{ old('provider_invoice', $purchase->provider_invoice) }}">
                        </div>

                        <button type="submit" onclick="return confirm('¿Registrar la recepción? El stock de los productos aumentará en bodega.')"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition transform hover:-translate-y-0.5">
                            ✓ Registrar Recepción e Incrementar Stock
                        </button>
                    </div>
                @endif

                {{-- Totals --}}
                <div class="flex justify-end pt-4 border-t border-slate-800">
                    <div class="w-64 space-y-1.5 font-mono text-sm">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal:</span>
                            <span>${{ number_format((float) $purchase->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>IVA (19%):</span>
                            <span>${{ number_format((float) $purchase->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-emerald-400 border-t border-slate-800 pt-1">
                            <span>Total Factura:</span>
                            <span>${{ number_format((float) $purchase->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if ($purchase->notes)
                    <div class="text-xs text-slate-400 border-t border-slate-800 pt-3">
                        <strong class="text-slate-300">Observaciones:</strong> {{ $purchase->notes }}
                    </div>
                @endif
            </form>

        </div>
    </div>
</x-app-layout>
