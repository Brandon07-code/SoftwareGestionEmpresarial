<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded-full border border-indigo-500/20">
                    Control Físico y Trazabilidad Transaccional
                </span>
                <h1 class="font-black text-2xl text-white tracking-tight mt-1">
                    Kárdex de Inventario {{ $selectedProduct ? '— ' . $selectedProduct->name : '' }}
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">
                    Historial cronológico inmutable de entradas por compras, salidas por ventas y devoluciones
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    ← Volver a Productos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-semibold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Resumen de Métricas del Kárdex -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 shadow-lg">
                    <span class="text-xs font-medium text-slate-400 block">Total Movimientos</span>
                    <p class="text-2xl font-black text-white mt-1 font-mono">{{ $totalMovements }}</p>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Registros auditados en el sistema</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/20 shadow-lg">
                    <span class="text-xs font-medium text-emerald-400 block">Total Entradas</span>
                    <p class="text-2xl font-black text-emerald-300 mt-1 font-mono">+{{ $totalIn }} uds</p>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Compras recepcionadas y aperturas</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-rose-500/20 shadow-lg">
                    <span class="text-xs font-medium text-rose-400 block">Total Salidas</span>
                    <p class="text-2xl font-black text-rose-300 mt-1 font-mono">-{{ $totalOut }} uds</p>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Ventas facturadas en mostrador</span>
                </div>
            </div>

            @can('ajustar-inventario')
                <div class="bg-slate-900/90 border border-indigo-500/30 rounded-2xl p-5 shadow-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-800 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-white">Registrar Ajuste Físico de Inventario</h2>
                            <p class="text-xs text-slate-300">Conteo físico, mermas, daños o sobrantes auditados (motivo mínimo 10 caracteres)</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                            Trazabilidad Obligatoria
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kardex.adjust') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                        @csrf
                        <div class="lg:col-span-2">
                            <label for="adj_product_id" class="block text-[11px] font-semibold text-slate-300 uppercase mb-1">Producto</label>
                            <select id="adj_product_id" name="product_id" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Seleccione un producto...</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" @selected(old('product_id', $selectedProduct?->id) == $p->id)>
                                        {{ $p->name }} (Stock: {{ $p->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="adj_movement_type" class="block text-[11px] font-semibold text-slate-300 uppercase mb-1">Tipo de Ajuste</label>
                            <select id="adj_movement_type" name="movement_type" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="AJUSTE_POSITIVO" @selected(old('movement_type') === 'AJUSTE_POSITIVO')>Ajuste Positivo (+)</option>
                                <option value="AJUSTE_NEGATIVO" @selected(old('movement_type') === 'AJUSTE_NEGATIVO')>Ajuste Negativo (-)</option>
                            </select>
                        </div>

                        <div>
                            <label for="adj_quantity" class="block text-[11px] font-semibold text-slate-300 uppercase mb-1">Cantidad</label>
                            <input id="adj_quantity" type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="lg:col-span-2">
                            <label for="adj_reason" class="block text-[11px] font-semibold text-slate-300 uppercase mb-1">Motivo / Justificación (mín. 10 car.)</label>
                            <input id="adj_reason" type="text" name="reason" minlength="10" maxlength="255" value="{{ old('reason') }}" placeholder="Ej: Conteo físico de bodega semanal" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="lg:col-span-4">
                            <label for="adj_doc" class="block text-[11px] font-semibold text-slate-300 uppercase mb-1">Documento Soporte (Opcional)</label>
                            <input id="adj_doc" type="text" name="document_reference" maxlength="50" value="{{ old('document_reference') }}" placeholder="Ej: ACTA-INV-012" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="lg:col-span-2">
                            <button type="submit" class="w-full py-2 px-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition">
                                Registrar Ajuste en Kárdex
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

            <!-- Panel Principal con Filtros y Tabla -->
            <div class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
                <!-- Toolbar y Filtros -->
                <div class="p-5 border-b border-slate-800/80 bg-slate-950/60">
                    <form method="GET" action="{{ route('kardex.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Producto</label>
                            <select name="product_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Todos los productos</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Tipo de Movimiento</label>
                            <select name="movement_type" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Todos los tipos</option>
                                <option value="ENTRADA_COMPRA" {{ request('movement_type') === 'ENTRADA_COMPRA' ? 'selected' : '' }}>Entrada por Compra</option>
                                <option value="SALIDA_VENTA" {{ request('movement_type') === 'SALIDA_VENTA' ? 'selected' : '' }}>Salida por Venta</option>
                                <option value="ANULACION_VENTA" {{ request('movement_type') === 'ANULACION_VENTA' ? 'selected' : '' }}>Devolución Anulación</option>
                                <option value="AJUSTE_INICIAL" {{ request('movement_type') === 'AJUSTE_INICIAL' ? 'selected' : '' }}>Inventario Inicial</option>
                                <option value="AJUSTE_POSITIVO" {{ request('movement_type') === 'AJUSTE_POSITIVO' ? 'selected' : '' }}>Ajuste Positivo (+)</option>
                                <option value="AJUSTE_NEGATIVO" {{ request('movement_type') === 'AJUSTE_NEGATIVO' ? 'selected' : '' }}>Ajuste Negativo (-)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Desde</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Hasta</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shadow-md">
                                Filtrar
                            </button>
                            @if(request()->hasAny(['product_id', 'movement_type', 'from', 'to', 'search']))
                                <a href="{{ route('kardex.index') }}" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition" title="Limpiar filtros">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Tabla de Movimientos del Kárdex -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-4">Fecha y Hora</th>
                                <th class="py-3 px-4">Producto</th>
                                <th class="py-3 px-4">Movimiento</th>
                                <th class="py-3 px-4">Doc. Soporte</th>
                                <th class="py-3 px-4 text-right">Cantidad</th>
                                <th class="py-3 px-4 text-right">P. Unitario</th>
                                <th class="py-3 px-4 text-center">Saldo (Ant → Nuevo)</th>
                                <th class="py-3 px-4">Responsable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300 font-medium">
                            @forelse ($movements as $m)
                                <tr class="hover:bg-slate-800/30 transition">
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-slate-400">
                                        {{ $m->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-white">
                                        <a href="{{ route('kardex.index', ['product_id' => $m->product_id]) }}" class="hover:text-indigo-400 transition">
                                            {{ $m->product?->name ?? 'Producto Eliminado' }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($m->movement_type === 'ENTRADA_COMPRA')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                ⬇ Entrada Compra
                                            </span>
                                        @elseif ($m->movement_type === 'SALIDA_VENTA')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                ⬆ Salida Venta
                                            </span>
                                        @elseif ($m->movement_type === 'ANULACION_VENTA')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                                ↺ Devolución
                                            </span>
                                        @elseif ($m->movement_type === 'AJUSTE_POSITIVO')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                                + Ajuste Positivo
                                            </span>
                                        @elseif ($m->movement_type === 'AJUSTE_NEGATIVO')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                                − Ajuste Negativo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/15 text-blue-400 border border-blue-500/30">
                                                ◉ {{ $m->movementTypeLabel() }}
                                            </span>
                                        @endif
                                        @if ($m->description)
                                            <div class="text-[11px] text-slate-300 mt-1 max-w-xs truncate" title="{{ $m->description }}">
                                                {{ $m->description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-indigo-300">
                                        {{ $m->document_reference ?? 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap {{ $m->isEntry() ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $m->isEntry() ? '+' : '-' }}{{ $m->quantity }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-slate-300">
                                        ${{ number_format($m->unit_price, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono">
                                        <span class="text-slate-400">{{ $m->stock_before }}</span>
                                        <span class="text-slate-600 mx-1.5">→</span>
                                        <span class="font-bold text-white bg-slate-800 px-2 py-0.5 rounded-md border border-slate-700">
                                            {{ $m->stock_after }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-400">
                                        {{ $m->user?->name ?? 'Sistema / Apertura' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-slate-400 text-sm">
                                        No hay movimientos registrados en el Kárdex para los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
                    {{ $movements->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
