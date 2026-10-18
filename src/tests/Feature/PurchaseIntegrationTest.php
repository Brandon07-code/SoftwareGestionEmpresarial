<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $vendedor;
    protected User $almacenista;
    protected Provider $provider;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();

        $this->provider = Provider::where('active', true)->first();
        $this->product = Product::first();
    }

    public function test_can_register_purchase_and_stock_does_not_change_yet(): void
    {
        $initialStock = $this->product->stock;
        $quantity = 10;
        $cost = 50000.00;

        $response = $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $cost,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $this->assertNotNull($purchase);
        $response->assertRedirect(route('purchases.show', $purchase));

        // Regla clave: Pedir NO es recibir -> El stock físico NO debe haber cambiado
        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);

        // Estado inicial de la orden debe ser "pendiente"
        $this->assertEquals('pendiente', $purchase->status);
        $this->assertNull($purchase->received_at);

        // Cálculo de totales con IVA 19%
        $expectedSubtotal = round($quantity * $cost, 2);
        $expectedTax = round($expectedSubtotal * 0.19, 2);
        $expectedTotal = round($expectedSubtotal + $expectedTax, 2);

        $this->assertEquals($expectedSubtotal, (float) $purchase->subtotal);
        $this->assertEquals($expectedTax, (float) $purchase->tax);
        $this->assertEquals($expectedTotal, (float) $purchase->total);
    }

    public function test_can_receive_purchase_partially_and_stock_increments(): void
    {
        $initialStock = $this->product->stock;
        $orderQuantity = 10;
        $receiveFirstBatch = 4;

        // 1. Crear la orden de compra
        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $orderQuantity,
                    'unit_cost' => 45000.00,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $detail = $purchase->details->first();

        // 2. Recepción parcial de 4 unidades
        $response = $this->actingAs($this->admin)->patch(route('purchases.receive', $purchase), [
            'items' => [
                $detail->id => $receiveFirstBatch,
            ],
            'provider_invoice' => 'FAC-PARCIAL-01',
        ]);

        $response->assertRedirect(route('purchases.show', $purchase));

        // 3. Verificar estado parcial y aumento exacto de stock
        $purchase->refresh();
        $this->assertEquals('parcial', $purchase->status);
        $this->assertNull($purchase->received_at);

        $detail->refresh();
        $this->assertEquals($receiveFirstBatch, $detail->received_quantity);
        $this->assertEquals($orderQuantity - $receiveFirstBatch, $detail->pendingQuantity());

        $this->product->refresh();
        $this->assertEquals($initialStock + $receiveFirstBatch, $this->product->stock);
    }

    public function test_can_receive_remaining_purchase_and_order_completes(): void
    {
        $initialStock = $this->product->stock;
        $orderQuantity = 6;

        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $orderQuantity,
                    'unit_cost' => 30000.00,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $detail = $purchase->details->first();

        // Recepción total de una sola vez
        $response = $this->actingAs($this->admin)->patch(route('purchases.receive', $purchase), [
            'items' => [
                $detail->id => $orderQuantity,
            ],
            'provider_invoice' => 'FAC-TOTAL-99',
        ]);

        $response->assertRedirect(route('purchases.show', $purchase));

        // Verificar estado "recibida" y fecha de cierre
        $purchase->refresh();
        $this->assertEquals('recibida', $purchase->status);
        $this->assertNotNull($purchase->received_at);

        $this->product->refresh();
        $this->assertEquals($initialStock + $orderQuantity, $this->product->stock);
    }

    public function test_cannot_receive_more_than_pending_quantity(): void
    {
        $initialStock = $this->product->stock;
        $orderQuantity = 5;

        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $orderQuantity,
                    'unit_cost' => 20000.00,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $detail = $purchase->details->first();

        // Intentar recibir 99 unidades cuando se pidieron 5
        $response = $this->actingAs($this->admin)->patch(route('purchases.receive', $purchase), [
            'items' => [
                $detail->id => 99,
            ],
        ]);

        $response->assertSessionHasErrors(['items']);

        // El stock físico no debe haberse alterado
        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);

        $purchase->refresh();
        $this->assertEquals('pendiente', $purchase->status);
    }

    public function test_can_cancel_pending_purchase_but_cannot_cancel_received_purchase(): void
    {
        // 1. Crear orden pendiente y anularla
        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_cost' => 15000.00,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $this->assertTrue($purchase->canBeCancelled());

        $response = $this->actingAs($this->admin)->delete(route('purchases.destroy', $purchase));
        $response->assertRedirect(route('purchases.index'));

        $purchase->refresh();
        $this->assertEquals('anulada', $purchase->status);
        $this->assertNotNull($purchase->deleted_at);

        // 2. Crear segunda orden, recibir mercancía e intentar anularla
        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_cost' => 15000.00,
                ],
            ],
        ]);

        $purchase2 = Purchase::where('status', 'pendiente')->first();
        $detail2 = $purchase2->details->first();

        $this->actingAs($this->admin)->patch(route('purchases.receive', $purchase2), [
            'items' => [$detail2->id => 2],
        ]);

        $purchase2->refresh();
        $this->assertFalse($purchase2->canBeCancelled());

        // Intentar anular debe arrojar error
        $response2 = $this->actingAs($this->admin)->delete(route('purchases.destroy', $purchase2));
        $response2->assertSessionHas('error');
        $this->assertNull($purchase2->fresh()->deleted_at);
    }

    public function test_cannot_delete_provider_with_purchases(): void
    {
        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_cost' => 10000.00,
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin)->delete(route('providers.destroy', $this->provider));
        $response->assertSessionHas('error');
        $this->assertNull($this->provider->fresh()->deleted_at);
    }

    public function test_can_generate_purchase_and_sale_pdfs(): void
    {
        // 1. PDF de compra
        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                    'unit_cost' => 25000.00,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $responsePurchasePdf = $this->actingAs($this->admin)->get(route('purchases.pdf', $purchase));
        $responsePurchasePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePurchasePdf->headers->get('Content-Type'));

        // 2. PDF de venta
        $client = Client::first();
        $this->actingAs($this->admin)->post(route('sales.store'), [
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $sale = Sale::first();
        $responseSalePdf = $this->actingAs($this->admin)->get(route('sales.pdf', $sale));
        $responseSalePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responseSalePdf->headers->get('Content-Type'));
    }

    public function test_role_permissions_for_purchases_and_providers(): void
    {
        // Vendedor no tiene acceso a compras ni proveedores (403)
        $this->actingAs($this->vendedor)->get(route('purchases.index'))->assertStatus(403);
        $this->actingAs($this->vendedor)->get(route('providers.index'))->assertStatus(403);

        // Almacenista puede acceder a compras y proveedores
        $this->actingAs($this->almacenista)->get(route('purchases.index'))->assertStatus(200);
        $this->actingAs($this->almacenista)->get(route('providers.index'))->assertStatus(200);

        // Almacenista NO puede eliminar compras ni proveedores (403)
        $this->actingAs($this->admin)->post(route('purchases.store'), [
            'provider_id' => $this->provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_cost' => 10000.00,
                ],
            ],
        ]);

        $purchase = Purchase::first();
        $this->actingAs($this->almacenista)->delete(route('purchases.destroy', $purchase))->assertStatus(403);
        $this->actingAs($this->almacenista)->delete(route('providers.destroy', $this->provider))->assertStatus(403);
    }
}
