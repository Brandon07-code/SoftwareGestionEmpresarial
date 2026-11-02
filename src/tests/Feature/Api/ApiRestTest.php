<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRestTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->token = $this->admin->createToken('test_token')->plainTextToken;
    }

    public function test_login_returns_token_and_user_data(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
            'device_name' => 'test_suite',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'roles',
                    ],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_me_and_logout(): void
    {
        // 1. Consultar perfil con Bearer Token
        $responseMe = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/v1/auth/me');

        $responseMe->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'admin@test.com');

        // 2. Cerrar sesión y revocar token
        $responseLogout = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/v1/auth/logout');

        $responseLogout->assertOk()
            ->assertJsonPath('success', true);

        // Limpiar el guard en memoria del contenedor de pruebas para simular una nueva petición HTTP desacoplada
        $this->app['auth']->forgetGuards();

        // 3. El token revocado ya no puede acceder
        $responseAfterLogout = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/v1/auth/me');

        $responseAfterLogout->assertStatus(401);
    }

    public function test_products_api_returns_paginated_catalog(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/v1/products?per_page=5');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'total_items',
                    'items',
                ],
            ]);
    }

    public function test_product_detail_and_not_found_handling(): void
    {
        $product = Product::first();

        // Producto existente
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson("/api/v1/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $product->id);

        // Producto no existente (404)
        $responseNotFound = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/v1/products/99999');

        $responseNotFound->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_kardex_api_returns_inventory_movements(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/v1/kardex?per_page=10');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'total_items',
                    'items',
                ],
            ]);
    }

    public function test_sales_today_api_returns_kpis(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/v1/sales/today');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'date',
                    'count',
                    'paid_count',
                    'total_collected',
                    'sales',
                ],
            ]);
    }

    public function test_transactional_sale_post_endpoint_creates_sale_and_updates_kardex(): void
    {
        $client = Client::where('active', true)->first();
        $product = Product::where('active', true)->where('stock', '>=', 5)->first();

        $initialStock = $product->stock;
        $quantityToSell = 2;

        $payload = [
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'notes' => 'Venta generada desde prueba de integración API REST',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $quantityToSell,
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/v1/sales', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'invoice_number',
                    'subtotal',
                    'tax',
                    'total',
                    'status',
                    'details',
                ],
            ]);

        // Verificar descuento en stock del producto
        $product->refresh();
        $this->assertEquals($initialStock - $quantityToSell, $product->stock);

        // Verificar asiento de SALIDA_VENTA en el Kárdex
        $saleId = $response->json('data.id');
        $sale = Sale::find($saleId);

        $movement = Kardex::where('product_id', $product->id)
            ->where('movement_type', 'SALIDA_VENTA')
            ->where('document_reference', $sale->invoice_number)
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals($quantityToSell, $movement->quantity);
    }

    public function test_transactional_sale_fails_when_stock_insufficient(): void
    {
        $client = Client::where('active', true)->first();
        $product = Product::where('active', true)->first();

        $payload = [
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $product->stock + 100, // Stock excesivo
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/v1/sales', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_client_creation_api_endpoint(): void
    {
        $payload = [
            'document_type' => 'CC',
            'document_number' => '9988776655',
            'name' => 'Cliente API REST Automatizado',
            'email' => 'cliente.api@test.com',
            'phone' => '3001234567',
            'address' => 'Avenida del Río # 45-12',
            'active' => true,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/v1/clients', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.document_number', '9988776655');

        $this->assertDatabaseHas('clients', [
            'document_number' => '9988776655',
            'name' => 'Cliente API REST Automatizado',
        ]);
    }
}
