<x-app-layout>
    <div class="py-8 bg-[#090D16] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div>
                <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition mb-2">
                    ← Volver a Compras
                </a>
                <h1 class="text-2xl font-black text-white tracking-tight">Nueva Orden de Compra</h1>
                <p class="text-sm text-slate-400">Genera una orden en estado <span class="text-amber-400 font-semibold">Pendiente</span>. El inventario solo incrementará al recibir la mercancía.</p>
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

            <form method="POST" action="{{ route('purchases.store') }}" id="purchase-form"
                class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="provider_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Proveedor</label>
                        <select id="provider_id" name="provider_id" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-sm" required>
                            <option value="">Seleccione un proveedor</option>
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
                                    {{ $provider->name }} — {{ $provider->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Fecha de la Orden</label>
                        <input id="purchase_date" name="purchase_date" type="date" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-sm"
                            value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required />
                    </div>

                    <div>
                        <label for="provider_invoice" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Factura Proveedor (Opcional)</label>
                        <input id="provider_invoice" name="provider_invoice" type="text" class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-sm"
                            placeholder="Ej. FAC-99120" value="{{ old('provider_invoice') }}" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Productos a Comprar</label>
                        <span class="text-xs text-slate-400">Digita el costo unitario pactado con el proveedor</span>
                    </div>

                    <div class="overflow-x-auto border border-slate-800 rounded-xl">
                        <table class="min-w-full text-sm divide-y divide-slate-800">
                            <thead class="bg-slate-950/60">
                                <tr class="text-left text-xs uppercase tracking-wider font-semibold text-slate-400">
                                    <th class="py-3 px-4">Producto</th>
                                    <th class="py-3 px-4 w-28 text-center">Cantidad</th>
                                    <th class="py-3 px-4 w-44 text-right">Costo Unitario ($)</th>
                                    <th class="py-3 px-4 w-36 text-right">Subtotal</th>
                                    <th class="py-3 px-4 w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body" class="divide-y divide-slate-800/60">
                                <!-- Filas dinámicas en JS -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="add-item" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Agregar Producto a la Orden
                    </button>
                </div>

                {{-- Summary Calculations --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-800 bg-slate-950/40 p-4 rounded-xl">
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Subtotal</span>
                        <p id="subtotal-display" class="text-lg font-bold text-white font-mono mt-0.5">$0.00</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">IVA (19% Ley Colombiana)</span>
                        <p id="tax-display" class="text-lg font-bold text-slate-300 font-mono mt-0.5">$0.00</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-indigo-400 uppercase tracking-wider">Total Orden de Compra</span>
                        <p id="total-display" class="text-xl font-black text-emerald-400 font-mono mt-0.5">$0.00</p>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Notas / Condiciones Comerciales (Opcional)</label>
                    <textarea id="notes" name="notes" rows="2" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-sm"
                        placeholder="Observaciones de entrega, transporte o forma de pago...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-800">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        Registrar Orden de Compra
                    </button>
                    <a href="{{ route('purchases.index') }}" class="text-sm text-slate-400 hover:text-slate-200 transition">
                        Cancelar
                    </a>
                </div>
            </form>

        </div>
    </div>

    <!-- Template para fila dinámica de producto -->
    <template id="item-row-template">
        <tr class="item-row hover:bg-slate-800/30 transition">
            <td class="py-2.5 px-4">
                <select name="items[__INDEX__][product_id]" class="product-select w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition" required>
                    <option value="">Seleccione producto...</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} (Stock Actual: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="py-2.5 px-4 text-center">
                <input type="number" name="items[__INDEX__][quantity]" value="1" min="1"
                    class="quantity-input w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-2 py-2 text-xs text-center font-mono focus:border-indigo-500" required>
            </td>
            <td class="py-2.5 px-4 text-right">
                <input type="number" name="items[__INDEX__][unit_cost]" value="" min="0.01" step="0.01"
                    placeholder="0.00"
                    class="cost-input w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs text-right font-mono focus:border-indigo-500" required>
                <span class="price-hint block text-[10px] text-slate-500 mt-1"></span>
            </td>
            <td class="py-2.5 px-4 text-right font-mono text-xs text-white">
                <span class="subtotal-display font-semibold">$0.00</span>
            </td>
            <td class="py-2.5 px-4 text-center">
                <button type="button" class="remove-row text-rose-400 hover:text-rose-300 text-sm font-bold transition">×</button>
            </td>
        </tr>
    </template>

    @push('scripts')
    <script>
        let index = 0;
        const itemsBody = document.getElementById('items-body');
        const template = document.getElementById('item-row-template').innerHTML;
        const addBtn = document.getElementById('add-item');

        const oldItems = @json(array_values(old('items', [])));

        function addRow(item = null) {
            const html = template.replace(/__INDEX__/g, index++);
            itemsBody.insertAdjacentHTML('beforeend', html);
            const row = itemsBody.lastElementChild;

            if (item) {
                row.querySelector('.product-select').value = item.product_id ?? '';
                row.querySelector('.quantity-input').value = item.quantity ?? 1;
                row.querySelector('.cost-input').value = item.unit_cost ?? '';
            }

            bindRow(row);
            recalculate();
        }

        function bindRow(row) {
            row.querySelector('.product-select').addEventListener('change', recalculate);
            row.querySelector('.quantity-input').addEventListener('input', recalculate);
            row.querySelector('.cost-input').addEventListener('input', recalculate);
            row.querySelector('.remove-row').addEventListener('click', () => {
                row.remove();
                recalculate();
            });
        }

        function formatCOP(value) {
            return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function recalculate() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const select = row.querySelector('.product-select');
                const opt = select.options[select.selectedIndex];
                const salePrice = parseFloat(opt?.dataset?.price || 0);
                const qty = parseInt(row.querySelector('.quantity-input').value || 0);
                const cost = parseFloat(row.querySelector('.cost-input').value || 0);
                const lineSubtotal = cost * qty;

                // Margen comercial: si el costo es >= precio de venta comercial, advertencia en rojo
                const hint = row.querySelector('.price-hint');
                if (salePrice > 0) {
                    hint.textContent = 'PVP Catálogo: ' + formatCOP(salePrice);
                    if (cost > 0 && cost >= salePrice) {
                        hint.classList.add('text-rose-400', 'font-bold');
                        hint.classList.remove('text-slate-500');
                        hint.textContent = '¡Alerta! Costo ≥ PVP Catálogo (' + formatCOP(salePrice) + ')';
                    } else {
                        hint.classList.remove('text-rose-400', 'font-bold');
                        hint.classList.add('text-slate-500');
                    }
                } else {
                    hint.textContent = '';
                }

                row.querySelector('.subtotal-display').textContent = formatCOP(lineSubtotal);
                subtotal += lineSubtotal;
            });

            const tax = subtotal * 0.19;
            const total = subtotal + tax;

            document.getElementById('subtotal-display').textContent = formatCOP(subtotal);
            document.getElementById('tax-display').textContent = formatCOP(tax);
            document.getElementById('total-display').textContent = formatCOP(total);
        }

        addBtn.addEventListener('click', () => addRow());

        if (oldItems.length) {
            oldItems.forEach(item => addRow(item));
        } else {
            addRow(); // Fila inicial por defecto
        }
    </script>
    @endpush
</x-app-layout>
