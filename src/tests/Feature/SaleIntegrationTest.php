<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Client $client;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->client = Client::first();
        $this->product = Product::first();
    }

    public function test_can_register_sale_and_stock_is_decremented(): void
    {
        $initialStock = $this->product->stock;
        $quantityToSell = 2;

        $response = $this->actingAs($this->admin)->post(route('sales.store'), [
            'client_id' => $this->client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $quantityToSell,
                ],
            ],
        ]);

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $response->assertRedirect(route('sales.show', $sale));

        // Verificar descuento de stock en la BD
        $this->product->refresh();
        $this->assertEquals($initialStock - $quantityToSell, $this->product->stock);

        // Verificar cálculo de IVA 19%
        $expectedSubtotal = round($this->product->price * $quantityToSell, 2);
        $expectedTax = round($expectedSubtotal * 0.19, 2);
        $expectedTotal = round($expectedSubtotal + $expectedTax, 2);

        $this->assertEquals($expectedSubtotal, (float) $sale->subtotal);
        $this->assertEquals($expectedTax, (float) $sale->tax);
        $this->assertEquals($expectedTotal, (float) $sale->total);
    }

    public function test_cannot_sell_more_than_available_stock(): void
    {
        $excessiveQuantity = $this->product->stock + 50;

        $response = $this->actingAs($this->admin)->from(route('sales.create'))->post(route('sales.store'), [
            'client_id' => $this->client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $excessiveQuantity,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_canceling_sale_restores_stock(): void
    {
        $initialStock = $this->product->stock;
        $quantity = 3;

        // Registrar venta
        $this->actingAs($this->admin)->post(route('sales.store'), [
            'client_id' => $this->client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $quantity,
                ],
            ],
        ]);

        $sale = Sale::first();
        $this->product->refresh();
        $this->assertEquals($initialStock - $quantity, $this->product->stock);

        // Anular venta
        $response = $this->actingAs($this->admin)->delete(route('sales.destroy', $sale));
        $response->assertRedirect(route('sales.index'));

        // El stock debe volver a su valor original
        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);

        // La venta queda con status anulada y soft-deleted
        $sale->refresh();
        $this->assertEquals('anulada', $sale->status);
        $this->assertNotNull($sale->deleted_at);
    }

    public function test_cannot_delete_client_with_sales(): void
    {
        // Registrar venta para este cliente
        $this->actingAs($this->admin)->post(route('sales.store'), [
            'client_id' => $this->client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        // Intentar borrar cliente con ventas asociadas
        $response = $this->actingAs($this->admin)->delete(route('clients.destroy', $this->client));
        $response->assertSessionHas('error');

        // El cliente NO debe estar borrado
        $this->assertNull($this->client->fresh()->deleted_at);
    }
}
