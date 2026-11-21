<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class Clase13Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $vendedor;

    protected User $almacenista;

    protected User $supervisorMultiRol;

    protected Category $categoriaGeneral;

    protected Product $productoPrueba;

    protected Client $clientePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();

        // Crear o recuperar usuario supervisor con múltiples roles (vendedor y almacenista)
        $this->supervisorMultiRol = User::firstOrCreate(
            ['email' => 'supervisor@test.com'],
            [
                'empresa_id' => $this->vendedor->empresa_id,
                'name' => 'Supervisor Dual',
                'password' => bcrypt('password'),
                'telefono' => '3119998877',
                'activo' => true,
            ]
        );
        $this->supervisorMultiRol->syncRoles(['vendedor', 'almacenista']);

        $this->categoriaGeneral = Category::firstOrCreate(['name' => 'Accesorios & Cuidado Personal'], ['description' => 'Test', 'active' => true]);
        $this->productoPrueba = Product::where('active', true)->where('stock', '>', 5)->first();
        $this->clientePrueba = Client::first();
    }

    public function test_api_login_returns_201_token_and_active_role(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'vendedor@test.com',
            'password' => 'password',
            'device_name' => 'postman-runner',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'token_type',
                'rol_activo',
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('rol_activo', 'vendedor');
    }

    public function test_api_login_fails_with_422_when_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'vendedor@test.com',
            'password' => 'clave-errada',
            'device_name' => 'postman-runner',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_api_login_fails_with_422_when_user_has_multiple_roles_and_omits_role(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'supervisor@test.com',
            'password' => 'password',
            'device_name' => 'movil-supervisor',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role'])
            ->assertJsonPath('errors.role.0', function ($msg) {
                return str_contains($msg, 'Su usuario tiene varios roles. Indique con cuál desea trabajar');
            });
    }

    public function test_api_login_succeeds_when_multi_role_user_specifies_valid_role(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'supervisor@test.com',
            'password' => 'password',
            'device_name' => 'movil-supervisor',
            'role' => 'vendedor',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('rol_activo', 'vendedor');
    }

    public function test_api_products_without_token_returns_401_unauthorized(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(401);
    }

    public function test_api_me_returns_current_user_and_effective_permissions(): void
    {
        $token = $this->vendedor->createToken('test', ['rol:vendedor'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertOk()
            ->assertJsonPath('email', 'vendedor@test.com')
            ->assertJsonPath('rol_activo', 'vendedor')
            ->assertJsonStructure([
                'id',
                'nombre',
                'email',
                'rol_activo',
                'permisos',
            ]);
    }

    public function test_api_logout_revokes_token_and_blocks_further_requests(): void
    {
        $token = $this->vendedor->createToken('test', ['rol:vendedor'])->plainTextToken;

        $responseLogout = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $responseLogout->assertOk()
            ->assertJsonPath('message', 'Sesión cerrada. El token fue revocado.');

        // Limpiar el guard de autenticación en memoria
        $this->app['auth']->forgetGuards();

        $responseMe = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $responseMe->assertStatus(401);
    }

    public function test_api_products_index_and_show_return_product_resource(): void
    {
        $token = $this->vendedor->createToken('test', ['rol:vendedor'])->plainTextToken;

        $responseIndex = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/products');

        $responseIndex->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'descripcion',
                        'precio',
                        'stock',
                        'disponible',
                        'categoria' => [
                            'id',
                            'nombre',
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);

        $responseShow = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/products/{$this->productoPrueba->id}");

        $responseShow->assertOk()
            ->assertJsonPath('data.id', $this->productoPrueba->id)
            ->assertJsonPath('data.nombre', $this->productoPrueba->name);
    }

    public function test_api_sales_store_returns_403_when_almacenista_attempts_to_sell(): void
    {
        $token = $this->almacenista->createToken('bodega', ['rol:almacenista'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/sales', [
                'client_id' => $this->clientePrueba->id,
                'sale_date' => now()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $this->productoPrueba->id,
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertStatus(403);
    }

    public function test_api_sales_store_returns_422_when_stock_is_insufficient(): void
    {
        $token = $this->vendedor->createToken('vendedor', ['rol:vendedor'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/sales', [
                'client_id' => $this->clientePrueba->id,
                'sale_date' => now()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $this->productoPrueba->id,
                        'quantity' => 99999, // Supera stock disponible
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_api_sales_store_succeeds_with_201_and_decrements_stock_via_kardex(): void
    {
        $token = $this->vendedor->createToken('vendedor', ['rol:vendedor'])->plainTextToken;
        $initialStock = $this->productoPrueba->stock;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/sales', [
                'client_id' => $this->clientePrueba->id,
                'sale_date' => now()->format('Y-m-d'),
                'notes' => 'Venta registrada vía API REST v1',
                'items' => [
                    [
                        'product_id' => $this->productoPrueba->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'factura',
                    'fecha',
                    'estado',
                    'cliente' => ['id', 'nombre', 'documento'],
                    'vendedor',
                    'detalle' => [
                        '*' => ['producto_id', 'producto', 'cantidad', 'precio_unitario', 'subtotal'],
                    ],
                    'subtotal',
                    'iva',
                    'total',
                    'pdf',
                ],
            ]);

        $this->productoPrueba->refresh();
        $this->assertEquals($initialStock - 2, $this->productoPrueba->stock);

        // Kárdex de salida registrado
        $this->assertDatabaseHas('kardex', [
            'product_id' => $this->productoPrueba->id,
            'movement_type' => 'SALIDA_VENTA',
            'quantity' => 2,
        ]);
    }

    public function test_excel_export_products_and_sales_downloads_xlsx(): void
    {
        // 1. Exportar productos
        $responseProd = $this->actingAs($this->almacenista)->get(route('products.export'));
        $responseProd->assertOk();
        $this->assertStringContainsString('.xlsx', (string) $responseProd->headers->get('content-disposition'));

        // 2. Exportar ventas
        $responseSales = $this->actingAs($this->vendedor)->get(route('sales.export'));
        $responseSales->assertOk();
        $this->assertStringContainsString('.xlsx', (string) $responseSales->headers->get('content-disposition'));

        // 3. Descargar plantilla
        $responseTemplate = $this->actingAs($this->almacenista)->get(route('products.template'));
        $responseTemplate->assertOk();
        $this->assertStringContainsString('plantilla_productos.xlsx', (string) $responseTemplate->headers->get('content-disposition'));
    }

    public function test_excel_import_products_creates_updates_and_reports_failures(): void
    {
        // Preparar categorías conocidas
        $categoriaCapilar = Category::firstOrCreate(['name' => 'Cuidado Capilar & Ceras'], ['description' => 'Test', 'active' => true]);

        // Fila 1: Producto nuevo válido
        // Fila 2: Producto existente con actualización de precio (mismo stock)
        // Fila 3: Fila con error de precio negativo
        // Fila 4: Fila con categoría inexistente
        $existing = $this->productoPrueba;
        $stockBefore = $existing->stock;

        $csvContent = implode("\n", [
            'nombre,descripcion,precio,stock,categoria',
            'Locion Capilar Hidratante,Locion reparadora 250ml,38000,10,Cuidado Capilar & Ceras',
            "{$existing->name},Descripcion actualizada,52000,99,{$existing->category->name}",
            'Gel Fijador Sin Precio Valido,Gel sin alcohol,-15000,5,Cuidado Capilar & Ceras',
            'Shampoo Fantasma,Sin categoria real,22000,8,CategoriaInexistente123XYZ',
        ]);

        $file = UploadedFile::fake()->createWithContent('lote_productos.csv', $csvContent);

        $response = $this->actingAs($this->almacenista)->post(route('products.import.store'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('products.import'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('failures');

        $failures = session('failures');
        $this->assertCount(2, $failures);

        // Verificar creación de nuevo producto
        $this->assertDatabaseHas('products', [
            'name' => 'Locion Capilar Hidratante',
            'price' => 38000,
            'stock' => 10,
        ]);

        // Verificar que producto existente cambió de precio pero conservó su stock
        $existing->refresh();
        $this->assertEquals(52000, (float) $existing->price);
        $this->assertEquals($stockBefore, $existing->stock);

        // Kárdex para el nuevo producto (inventario inicial)
        $nuevo = Product::where('name', 'Locion Capilar Hidratante')->first();
        $this->assertDatabaseHas('kardex', [
            'product_id' => $nuevo->id,
            'movement_type' => 'AJUSTE_INICIAL',
            'quantity' => 10,
        ]);
    }
}
