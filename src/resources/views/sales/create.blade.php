<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-white tracking-tight leading-tight">
            Registrar Nueva Venta
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sales.store') }}" id="sale-form"
                  class="bg-slate-900/80 shadow-2xl rounded-2xl border border-slate-800/80 p-6 sm:p-8 space-y-6 backdrop-blur-sm">
                @csrf

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                        <strong class="block font-bold mb-1">Por favor corrija los siguientes errores:</strong>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="client_id" value="Cliente *" />
                        <select id="client_id" name="client_id" class="mt-1 block w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" required>
                            <option value="" class="bg-slate-900 text-slate-400">Seleccione un cliente</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" class="bg-slate-900 text-white" @selected(old('client_id') == $client->id)>
                                    {{ $client->name }} — {{ $client->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="sale_date" value="Fecha de Venta *" />
                        <x-text-input id="sale_date" name="sale_date" type="date" class="mt-1 block w-full"
                            :value="old('sale_date', now()->format('Y-m-d'))" required />
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2">
                        <x-input-label value="Detalle de Productos Vendidos *" />
                        <span class="text-[11px] text-slate-400">El stock se descuenta atómicamente de la base de datos</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/60">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-950 text-slate-400 uppercase font-bold text-[11px] border-b border-slate-800">
                                <tr class="text-left">
                                    <th class="py-3 px-4">Producto</th>
                                    <th class="py-3 px-4 w-28">Cantidad</th>
                                    <th class="py-3 px-4 w-32 text-right">Precio Unitario</th>
                                    <th class="py-3 px-4 w-36 text-right">Subtotal</th>
                                    <th class="py-3 px-4 w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body" class="divide-y divide-slate-800/80 text-slate-300">
                                <!-- Filas dinámicas con JS -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="add-item" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-indigo-400 hover:text-indigo-300 border border-slate-700 text-xs font-bold transition shadow-sm">
                        <span>➕ Agregar producto</span>
                    </button>
                </div>

                <!-- Resumen de Liquidación con IVA 19% -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-slate-800 pt-5">
                    <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                        <x-input-label value="Subtotal (Sin IVA)" />
                        <p id="subtotal-display" class="text-xl font-bold text-white font-mono mt-1">$0.00</p>
                    </div>

                    <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                        <x-input-label value="IVA (19% Colombia)" />
                        <p id="tax-display" class="text-xl font-bold text-cyan-400 font-mono mt-1">$0.00</p>
                    </div>

                    <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                        <x-input-label value="Total Liquidado" />
                        <p id="total-display" class="text-2xl font-black text-emerald-400 font-mono mt-1">$0.00</p>
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" value="Notas / Observaciones de la Factura (Opcional)" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3.5 py-2.5 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" placeholder="Ej: Pago de contado en efectivo, entrega inmediata...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-800">
                    <x-primary-button>
                        Registrar Venta & Descontar Stock
                    </x-primary-button>
                    <a href="{{ route('sales.index') }}" class="text-xs text-slate-400 hover:text-slate-200 underline font-semibold transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Plantilla de una fila de producto para JS -->
    <template id="item-row-template">
        <tr class="item-row hover:bg-slate-900/50 transition">
            <td class="py-2.5 px-4">
                <select name="items[__INDEX__][product_id]" class="product-select w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:border-indigo-500" required>
                    <option value="" class="bg-slate-900 text-slate-400">Seleccione un producto</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}" class="bg-slate-900 text-white">
                            {{ $product->name }} (Stock: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="py-2.5 px-4">
                <input type="number" name="items[__INDEX__][quantity]" value="1" min="1"
                       class="quantity-input w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs font-mono font-bold focus:border-indigo-500" required>
            </td>
            <td class="py-2.5 px-4 text-right font-mono text-slate-300">
                <span class="price-display">$0.00</span>
            </td>
            <td class="py-2.5 px-4 text-right font-mono font-bold text-white">
                <span class="subtotal-display">$0.00</span>
            </td>
            <td class="py-2.5 px-4 text-center">
                <button type="button" class="remove-row p-1 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 transition font-bold" title="Eliminar fila">
                    ✕
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
    <script>
        let index = 0;
        const itemsBody = document.getElementById('items-body');
        const template = document.getElementById('item-row-template').innerHTML;
        const addBtn = document.getElementById('add-item');

        // Filas enviadas antes (si el servidor devolvió un error de validación se reconstruyen)
        const oldItems = @json(array_values(old('items', [])));

        function addRow(item = null) {
            const html = template.replace(/__INDEX__/g, index++);
            itemsBody.insertAdjacentHTML('beforeend', html);
            const row = itemsBody.lastElementChild;

            if (item) {
                row.querySelector('.product-select').value = item.product_id ?? '';
                row.querySelector('.quantity-input').value = item.quantity ?? 1;
            }

            bindRow(row);
            recalculate();
        }

        function bindRow(row) {
            row.querySelector('.product-select').addEventListener('change', recalculate);
            row.querySelector('.quantity-input').addEventListener('input', recalculate);
            row.querySelector('.remove-row').addEventListener('click', () => {
                row.remove();
                recalculate();
            });
        }

        function formatCOP(value) {
            return '$' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function recalculate() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const select = row.querySelector('.product-select');
                const qty = parseInt(row.querySelector('.quantity-input').value || 0);
                const opt = select.options[select.selectedIndex];
                const price = parseFloat(opt?.dataset?.price || 0);
                const lineSubtotal = price * qty;

                row.querySelector('.price-display').textContent = formatCOP(price);
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

        if (oldItems && oldItems.length > 0) {
            oldItems.forEach(item => addRow(item));
        } else {
            addRow(); // Fila inicial por defecto
        }
    </script>
    @endpush
</x-app-layout>
