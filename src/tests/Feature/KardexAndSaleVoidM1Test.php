<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\User;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KardexAndSaleVoidM1Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $almacenista;

    protected User $auditor;

    protected User $cajero;

    protected User $vendedor;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->auditor = User::where('email', 'auditor@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
        $this->vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->category = Category::first();
    }

    public function test_product_store_records_ajuste_inicial_only_when_initial_stock_is_positive(): void
    {
        // Con stock inicial > 0
        $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Pomada Matte Pro',
            'description' => 'Fijación alta',
            'price' => 32000,
            'stock' => 12,
            'category_id' => $this->category->id,
            'active' => '1',
        ])->assertRedirect(route('products.index'));

        $productWithStock = Product::where('name', 'Pomada Matte Pro')->first();
        $this->assertNotNull($productWithStock);
        $this->assertEquals(12, $productWithStock->stock);

        $this->assertDatabaseHas('kardex', [
            'product_id' => $productWithStock->id,
            'movement_type' => 'AJUSTE_INICIAL',
            'quantity' => 12,
            'stock_before' => 0,
            'stock_after' => 12,
        ]);

        // Con stock inicial = 0
        $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Peine Carbono Sin Stock',
            'description' => 'Sin existencias iniciales',
            'price' => 15000,
            'stock' => 0,
            'category_id' => $this->category->id,
            'active' => '1',
        ])->assertRedirect(route('products.index'));

        $productZero = Product::where('name', 'Peine Carbono Sin Stock')->first();
        $this->assertNotNull($productZero);
        $this->assertEquals(0, $productZero->stock);
        $this->assertEquals(0, Kardex::where('product_id', $productZero->id)->count());
    }

    public function test_product_update_cannot_mutate_stock_and_edit_view_renders_readonly_stock(): void
    {
        $product = Product::first();
        $originalStock = $product->stock;
        $kardexCountBefore = Kardex::where('product_id', $product->id)->count();

        $editResponse = $this->actingAs($this->admin)->get(route('products.edit', $product));
        $editResponse->assertOk();
        $editResponse->assertSee('Solo lectura');
        $editResponse->assertSee(route('kardex.index', ['product_id' => $product->id]), false);
        $editResponse->assertDontSee('name="stock"', false);

        // Envío malicioso con stock = 999
        $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => $product->name.' Editado',
            'description' => 'Actualizado sin tocar stock',
            'price' => 55000,
            'stock' => 999,
            'category_id' => $product->category_id,
            'active' => '1',
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertStringEndsWith('Editado', $product->name);
        $this->assertEquals(55000.0, (float) $product->price);
        $this->assertEquals($originalStock, $product->stock);
        $this->assertEquals($kardexCountBefore, Kardex::where('product_id', $product->id)->count());
    }

    public function test_kardex_physical_adjustment_flow_and_validation_invariants(): void
    {
        $product = Product::where('stock', '>=', 5)->first();
        $initialStock = $product->stock;

        // 1. Almacenista realiza ajuste positivo
        $this->actingAs($this->almacenista)->post(route('kardex.adjust'), [
            'product_id' => $product->id,
            'movement_type' => 'AJUSTE_POSITIVO',
            'quantity' => 4,
            'reason' => 'Hallazgo en conteo físico mensual de estantería',
            'document_reference' => 'ACTA-001',
        ])->assertRedirect(route('kardex.index', ['product_id' => $product->id]));

        $product->refresh();
        $this->assertEquals($initialStock + 4, $product->stock);
        $this->assertDatabaseHas('kardex', [
            'product_id' => $product->id,
            'movement_type' => 'AJUSTE_POSITIVO',
            'quantity' => 4,
            'stock_before' => $initialStock,
            'stock_after' => $initialStock + 4,
            'document_reference' => 'ACTA-001',
        ]);

        // 2. Admin realiza ajuste negativo válido
        $this->actingAs($this->admin)->post(route('kardex.adjust'), [
            'product_id' => $product->id,
            'movement_type' => 'AJUSTE_NEGATIVO',
            'quantity' => 2,
            'reason' => 'Merma por envase averiado en bodega principal',
        ])->assertRedirect(route('kardex.index', ['product_id' => $product->id]));

        $product->refresh();
        $this->assertEquals($initialStock + 2, $product->stock);

        // 3. Motivo corto (<10 chars) debe fallar sin alterar stock
        $this->actingAs($this->almacenista)->post(route('kardex.adjust'), [
            'product_id' => $product->id,
            'movement_type' => 'AJUSTE_POSITIVO',
            'quantity' => 3,
            'reason' => 'Corto',
        ])->assertSessionHasErrors('reason');
        $this->assertEquals($initialStock + 2, $product->fresh()->stock);

        // 4. Ajuste negativo mayor al stock debe fallar sin dejar stock negativo
        $this->actingAs($this->almacenista)->post(route('kardex.adjust'), [
            'product_id' => $product->id,
            'movement_type' => 'AJUSTE_NEGATIVO',
            'quantity' => $product->stock + 50,
            'reason' => 'Intento de salida por encima del saldo disponible',
        ])->assertSessionHasErrors('quantity');
        $this->assertEquals($initialStock + 2, $product->fresh()->stock);

        // 5. Auditor, cajero y vendedor no pueden ajustar; cajero y vendedor tampoco ven Kardex
        foreach ([$this->auditor, $this->cajero, $this->vendedor] as $unauthorized) {
            $this->actingAs($unauthorized)->post(route('kardex.adjust'), [
                'product_id' => $product->id,
                'movement_type' => 'AJUSTE_POSITIVO',
                'quantity' => 1,
                'reason' => 'Intento sin permiso suficiente en el sistema',
            ])->assertForbidden();
        }

        $this->actingAs($this->cajero)->get(route('kardex.index'))->assertForbidden();
        $this->actingAs($this->vendedor)->get(route('kardex.index'))->assertForbidden();
        $this->actingAs($this->auditor)->get(route('kardex.index'))->assertOk();
    }

    public function test_sale_void_is_idempotent_and_cannot_double_restore_stock(): void
    {
        $saleService = app(SaleService::class);
        $client = Client::first();
        $product = Product::where('active', true)->where('stock', '>=', 5)->first();
        $initialStock = $product->stock;

        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ], $this->cajero->id);

        $this->assertEquals($initialStock - 3, $product->fresh()->stock);

        // Primera anulación vía controlador
        $this->actingAs($this->admin)
            ->delete(route('sales.destroy', $sale))
            ->assertRedirect(route('sales.index'));

        $this->assertEquals($initialStock, $product->fresh()->stock);
        $sale->refresh();
        $this->assertEquals('anulada', $sale->status);
        $this->assertFalse($sale->canBeCancelled());

        // Segunda llamada directa al servicio debe rechazar con ValidationException
        $this->expectException(ValidationException::class);
        try {
            $saleService->cancel($sale, $this->admin->id);
        } finally {
            $this->assertEquals($initialStock, $product->fresh()->stock);
            $this->assertEquals(
                1,
                Kardex::where('product_id', $product->id)
                    ->where('movement_type', 'ANULACION_VENTA')
                    ->where('document_reference', $sale->invoice_number)
                    ->count()
            );
        }
    }
}
