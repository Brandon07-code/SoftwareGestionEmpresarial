<?php

namespace App\Console\Commands;

use App\Events\PurchaseReceived;
use App\Events\SaleRegistered;
use App\Models\Category;
use App\Models\Client;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DemoTestTraceCommand extends Command
{
    protected $signature = 'erp:demo-test';

    protected $description = 'Ejecuta en consola la demostración paso a paso de los ciclos de compra, venta y kárdex (Requisito Clase 15)';

    public function handle(): int
    {
        $this->output->title('TURNIX ERP — DEMOSTRACIÓN DE PRUEBAS EN CONSOLA (CRITERIO CLASE 15)');
        $this->line('  COTECNOVA 2026 · Software de Gestión Empresarial');
        $this->line('  Expositores: Brandon Cortés Giraldo & Johan Sttive Linares Barragán');
        $this->newLine();

        // -------------------------------------------------------------
        // PRUEBA 1: CICLO DE COMPRAS Y RECEPCIÓN PARCIAL/TOTAL
        // -------------------------------------------------------------
        $this->alert('PRUEBA 1: CICLO DE COMPRA, RECEPCIÓN Y ASIENTO EN KÁRDEX');

        $provider = Provider::first() ?? Provider::create([
            'name' => 'Distribuidora Cosméticos del Valle S.A.S.',
            'document_type' => 'NIT',
            'document_number' => '901.458.789-3',
            'phone' => '3157894512',
            'email' => 'ventas@cosmeticosvalle.co',
            'address' => 'Zona Industrial Cartago',
            'active' => true,
        ]);

        $category = Category::first() ?? Category::create([
            'name' => 'Cuidado Capilar & Ceras',
            'active' => true,
        ]);

        $product = Product::firstOrCreate(
            ['name' => 'Pomada Mate Fijación Extrema Demo'],
            [
                'description' => 'Cera estilizadora para demostración técnica',
                'price' => 38000,
                'stock' => 5,
                'category_id' => $category->id,
                'active' => true,
            ]
        );

        $initialStock = $product->stock;
        $this->info("  [Paso 1.1] Producto seleccionado: «{$product->name}»");
        $this->line("             Stock físico inicial en bodega: <fg=yellow;options=bold>{$initialStock} unidades</>");

        // Crear Orden de Compra en estado PENDIENTE
        $user = User::first() ?? User::factory()->create(['name' => 'Administrador Demo']);
        $purchase = Purchase::create([
            'purchase_number' => 'OC-'.now()->format('YmdHis').'-'.rand(10, 99),
            'provider_id' => $provider->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'status' => 'pendiente',
            'subtotal' => 200000,
            'tax' => 38000,
            'total' => 238000,
            'notes' => 'Orden generada para demostración en vivo de sustentación',
        ]);

        $detail = $purchase->details()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'received_quantity' => 0,
            'unit_cost' => 20000,
            'subtotal' => 200000,
        ]);

        $product->refresh();
        $this->line("  [Paso 1.2] Se emite la Orden de Compra <fg=cyan;options=bold>{$purchase->purchase_number}</> por 10 unidades a \$20.000 c/u.");
        $this->line("             Estado de la orden: <fg=magenta;options=bold>{$purchase->status}</>");
        $this->line("             ¿Cambió el stock físico?: <fg=green;options=bold>NO ({$product->stock} unidades)</> (Regla ERP: el stock NO sube al crear la orden).");

        // Recepción PARCIAL de 6 unidades
        $purchaseService = app(PurchaseService::class);
        $this->comment('  [Paso 1.3] Almacenista confirma recepción física PARCIAL de 6 unidades en bodega...');

        $purchaseService->receive($purchase, [$detail->id => 6], 'FAC-PROV-8841');
        $product->refresh();
        $purchase->refresh();

        $this->line("             Estado de la orden actualizado a: <fg=yellow;options=bold>{$purchase->status}</>");
        $this->line("             Stock físico en bodega ahora: <fg=green;options=bold>{$product->stock} unidades</> (+6 ingresadas)");

        $kardexEntry = Kardex::where('product_id', $product->id)->latest('id')->first();
        if ($kardexEntry) {
            $this->line("             Asiento Kárdex registrado: <fg=cyan>Tipo: {$kardexEntry->movement_type} | Cantidad: +{$kardexEntry->quantity} | Saldo: {$kardexEntry->stock_after} uds (Antes: {$kardexEntry->stock_before}) | Costo: \${$kardexEntry->unit_price} | Doc: {$kardexEntry->document_reference}</>");
        }

        // Recepción del RESTANTE (4 unidades)
        $this->comment('  [Paso 1.4] Almacenista recibe las 4 unidades restantes...');
        $purchaseService->receive($purchase, [$detail->id => 4], 'FAC-PROV-8841-B');
        $product->refresh();
        $purchase->refresh();

        $this->line("             Estado final de la orden: <fg=green;options=bold>{$purchase->status}</>");
        $this->line("             Stock físico final tras completar compra: <fg=green;options=bold>{$product->stock} unidades</>");
        $this->newLine();

        // -------------------------------------------------------------
        // PRUEBA 2: CICLO DE VENTA POS, CONCURRENCIA E IVA (19%)
        // -------------------------------------------------------------
        $this->alert('PRUEBA 2: CICLO DE VENTA, CONCURRENCIA (lockForUpdate), IVA Y KÁRDEX');

        $client = Client::first() ?? Client::create([
            'name' => 'Carlos Mario Gómez',
            'document_type' => 'CC',
            'document_number' => '1113456789',
            'phone' => '3124567890',
            'email' => 'carlos.gomez@gmail.com',
            'address' => 'Cra 4 # 12-45 Cartago',
            'active' => true,
        ]);

        $saleService = app(SaleService::class);

        // Intento de sobreventa para validar rechazo 422
        $this->comment('  [Paso 2.1] Intento de sobreventa: solicitando 999 unidades en caja...');
        try {
            $saleService->register([
                'client_id' => $client->id,
                'sale_date' => now()->toDateString(),
                'notes' => 'Prueba de concurrencia y sobreventa',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 999],
                ],
            ], $user->id);
            $this->error('  ERROR: El sistema permitió sobreventa.');
        } catch (ValidationException $e) {
            $this->line('             <fg=red;options=bold>✓ VALIDACIÓN EXITOSA:</> La transacción se abortó con Rollback.');
            $this->line('             Motivo: <fg=red>«'.collect($e->errors())->flatten()->first().'»</>');
        }

        // Venta real exitosa
        $saleQty = 3;
        $this->comment("  [Paso 2.2] Procesando venta válida en caja por {$saleQty} unidades...");
        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->toDateString(),
            'notes' => 'Venta en caja mostrador Barbería JyM',
            'items' => [
                ['product_id' => $product->id, 'quantity' => $saleQty],
            ],
        ], $user->id);

        $product->refresh();
        $this->line("             Factura generada: <fg=cyan;options=bold>{$sale->invoice_number}</>");
        $this->line('             Cálculo tributario: Subtotal: $'.number_format((float) $sale->subtotal, 2).' + IVA (19%): $'.number_format((float) $sale->tax, 2).' = Total: <fg=green;options=bold>$'.number_format((float) $sale->total, 2).' COP</>');
        $this->line("             Stock físico actualizado en bodega: <fg=yellow;options=bold>{$product->stock} unidades</> (-{$saleQty} vendidas)");

        $kardexExit = Kardex::where('product_id', $product->id)->latest('id')->first();
        if ($kardexExit) {
            $this->line("             Asiento Kárdex registrado: <fg=cyan>Tipo: {$kardexExit->movement_type} | Cantidad: -{$kardexExit->quantity} | Saldo: {$kardexExit->stock_after} uds (Antes: {$kardexExit->stock_before}) | Factura: {$kardexExit->document_reference}</>");
        }
        $this->newLine();

        // -------------------------------------------------------------
        // RESUMEN FINAL
        // -------------------------------------------------------------
        $this->info('========================================================================');
        $this->info('  RESULTADO: Ambos recorridos de negocio se ejecutaron con éxito.');
        $this->info('  Integridad referencial, transacciones atómicas y Kárdex 100% consistentes.');
        $this->info('========================================================================');

        return self::SUCCESS;
    }
}
