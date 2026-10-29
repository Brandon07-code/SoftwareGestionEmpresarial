<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KardexTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $almacenista;

    protected User $cajero;

    protected User $auditor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
        $this->auditor = User::where('email', 'auditor@test.com')->first();
    }

    public function test_kardex_records_initial_balance_on_seed(): void
    {
        $product = Product::where('name', 'Wahl Magic Clip Cordless')->first();
        $this->assertNotNull($product);

        $initialMovement = Kardex::where('product_id', $product->id)
            ->where('movement_type', 'AJUSTE_INICIAL')
            ->first();

        $this->assertNotNull($initialMovement);
        $this->assertEquals(8, $initialMovement->quantity);
        $this->assertEquals(0, $initialMovement->stock_before);
        $this->assertEquals(8, $initialMovement->stock_after);
    }

    public function test_kardex_records_entry_when_purchase_is_received(): void
    {
        $service = app(PurchaseService::class);
        $provider = Provider::where('active', true)->first();
        $product = Product::where('active', true)->first();

        $initialStock = $product->stock;

        // 1. Crear orden de compra (pendiente, no mueve Kardex aún)
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_cost' => 50000.00,
                ],
            ],
        ], $this->admin->id);

        $detail = $purchase->details->first();

        // 2. Recepcionar 6 unidades
        $service->receive($purchase, [$detail->id => 6], 'FAC-PROV-999');

        $product->refresh();
        $this->assertEquals($initialStock + 6, $product->stock);

        // Verificar que el movimiento en Kárdex existe y tiene los datos exactos
        $movement = Kardex::where('product_id', $product->id)
            ->where('movement_type', 'ENTRADA_COMPRA')
            ->where('document_reference', $purchase->purchase_number)
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(6, $movement->quantity);
        $this->assertEquals($initialStock, $movement->stock_before);
        $this->assertEquals($initialStock + 6, $movement->stock_after);
        $this->assertEquals(50000.00, (float) $movement->unit_price);
    }

    public function test_kardex_records_exit_when_sale_is_registered(): void
    {
        $saleService = app(SaleService::class);
        $client = Client::where('active', true)->first();
        $product = Product::where('active', true)->where('stock', '>=', 4)->first();

        $stockBefore = $product->stock;

        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ], $this->cajero->id);

        $product->refresh();
        $this->assertEquals($stockBefore - 3, $product->stock);

        // Verificar movimiento en Kárdex
        $movement = Kardex::where('product_id', $product->id)
            ->where('movement_type', 'SALIDA_VENTA')
            ->where('document_reference', $sale->invoice_number)
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(3, $movement->quantity);
        $this->assertEquals($stockBefore, $movement->stock_before);
        $this->assertEquals($stockBefore - 3, $movement->stock_after);
    }

    public function test_kardex_records_return_when_sale_is_canceled(): void
    {
        $saleService = app(SaleService::class);
        $client = Client::where('active', true)->first();
        $product = Product::where('active', true)->where('stock', '>=', 4)->first();

        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ], $this->cajero->id);

        $product->refresh();
        $stockAfterSale = $product->stock;

        // Anular venta desde el controlador
        $response = $this->actingAs($this->admin)->delete(route('sales.destroy', $sale));
        $response->assertRedirect(route('sales.index'));

        $product->refresh();
        $this->assertEquals($stockAfterSale + 2, $product->stock);

        // Verificar movimiento de devolución en Kárdex
        $returnMovement = Kardex::where('product_id', $product->id)
            ->where('movement_type', 'ANULACION_VENTA')
            ->where('document_reference', $sale->invoice_number)
            ->latest('id')
            ->first();

        $this->assertNotNull($returnMovement);
        $this->assertEquals(2, $returnMovement->quantity);
        $this->assertEquals($stockAfterSale, $returnMovement->stock_before);
        $this->assertEquals($stockAfterSale + 2, $returnMovement->stock_after);
    }

    public function test_kardex_view_can_be_rendered_and_filtered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kardex.index'));
        $response->assertOk();
        $response->assertSee('Kárdex de Inventario');

        $product = Product::first();
        $responseFilter = $this->actingAs($this->admin)->get(route('kardex.index', ['product_id' => $product->id]));
        $responseFilter->assertOk();
        $responseFilter->assertSee($product->name);
    }

    public function test_auditor_and_almacenista_can_view_kardex(): void
    {
        $this->actingAs($this->auditor)->get(route('kardex.index'))->assertOk();
        $this->actingAs($this->almacenista)->get(route('kardex.index'))->assertOk();
    }
}
