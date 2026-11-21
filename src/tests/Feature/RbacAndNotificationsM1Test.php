<?php

namespace Tests\Feature;

use App\Events\LowStockDetected;
use App\Events\PurchaseReceived;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Tercero;
use App\Models\TransaccionPago;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\PurchaseReceivedNotification;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RbacAndNotificationsM1Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $almacenista;

    protected User $cajero;

    protected User $auditor;

    protected User $vendedor;

    protected User $sinrol;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
        $this->auditor = User::where('email', 'auditor@test.com')->first();
        $this->vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->sinrol = User::where('email', 'sinrol@test.com')->first();
    }

    public function test_satellite_controllers_enforce_rbac_permissions(): void
    {
        $pago = TransaccionPago::first();
        $tercero = Tercero::clientes()->first();
        $this->assertNotNull($pago);
        $this->assertNotNull($tercero);

        // Almacenista y sinrol no tienen permisos en módulos satélite
        foreach ([$this->almacenista, $this->sinrol] as $blocked) {
            $this->actingAs($blocked)->get(route('citas.index'))->assertForbidden();
            $this->actingAs($blocked)->get(route('citas.create'))->assertForbidden();
            $this->actingAs($blocked)->get(route('terceros.index'))->assertForbidden();
            $this->actingAs($blocked)->get(route('terceros.create'))->assertForbidden();
            $this->actingAs($blocked)->get(route('pagos.index'))->assertForbidden();
            $this->actingAs($blocked)->get(route('pagos.checkout', $pago->id))->assertForbidden();
            $this->actingAs($blocked)->get(route('crm.index'))->assertForbidden();
        }

        // Auditor: solo lectura
        $this->actingAs($this->auditor)->get(route('citas.index'))->assertOk();
        $this->actingAs($this->auditor)->get(route('terceros.index'))->assertOk();
        $this->actingAs($this->auditor)->get(route('pagos.index'))->assertOk();
        $this->actingAs($this->auditor)->get(route('crm.index'))->assertOk();
        $this->actingAs($this->auditor)->get(route('citas.create'))->assertForbidden();
        $this->actingAs($this->auditor)->get(route('terceros.create'))->assertForbidden();
        $this->actingAs($this->auditor)->get(route('pagos.checkout', $pago->id))->assertForbidden();
        $this->actingAs($this->auditor)->post(route('crm.interacciones.store'), [
            'tercero_id' => $tercero->id,
            'canal' => 'WHATSAPP',
            'tipo' => 'SEGUIMIENTO',
            'nota' => 'Intento sin permiso',
        ])->assertForbidden();

        // Cajero: gestiona pagos, consulta citas/terceros/crm, no crea citas ni interacciones
        $this->actingAs($this->cajero)->get(route('pagos.checkout', $pago->id))->assertOk();
        $this->actingAs($this->cajero)->get(route('citas.create'))->assertForbidden();

        // Vendedor: crea citas/terceros/crm, consulta pagos pero no gestiona checkout
        $this->actingAs($this->vendedor)->get(route('citas.create'))->assertOk();
        $this->actingAs($this->vendedor)->get(route('terceros.create'))->assertOk();
        $this->actingAs($this->vendedor)->get(route('pagos.index'))->assertOk();
        $this->actingAs($this->vendedor)->get(route('pagos.checkout', $pago->id))->assertForbidden();
        $this->actingAs($this->vendedor)->post(route('crm.interacciones.store'), [
            'tercero_id' => $tercero->id,
            'canal' => 'WHATSAPP',
            'tipo' => 'SEGUIMIENTO',
            'nota' => 'Seguimiento comercial permitido',
        ])->assertRedirect();
    }

    public function test_product_import_excel_requires_importar_productos_not_crear_productos(): void
    {
        $creatorOnly = User::factory()->create(['activo' => true]);
        $creatorOnly->givePermissionTo('crear-productos');

        $csv = "nombre,descripcion,precio,stock,categoria\nNuevoProd,Desc,10000,2,General";
        $file = UploadedFile::fake()->createWithContent('import.csv', $csv);

        $this->actingAs($creatorOnly)
            ->post(route('products.import.excel'), ['file' => $file])
            ->assertForbidden();

        $this->actingAs($this->almacenista)
            ->post(route('products.import.excel'), ['file' => $file])
            ->assertRedirect(route('products.index'));
    }

    private function apiWithToken(string $token): static
    {
        $this->app['auth']->forgetGuards();

        return $this->withToken($token);
    }

    public function test_api_v1_extended_endpoints_enforce_rbac_and_active_role(): void
    {
        $category = Category::first();
        $product = Product::first();
        $client = Client::first();

        $tokenAlmacenista = $this->almacenista->createToken('alm', ['rol:almacenista'])->plainTextToken;
        $tokenVendedor = $this->vendedor->createToken('ven', ['rol:vendedor'])->plainTextToken;
        $tokenAuditor = $this->auditor->createToken('aud', ['rol:auditor'])->plainTextToken;
        $tokenSinRol = $this->sinrol->createToken('sin')->plainTextToken;

        // POST /api/v1/products -> solo quien tenga crear-productos
        $this->apiWithToken($tokenVendedor)->postJson('/api/v1/products', [
            'name' => 'Bloqueado Vendedor',
            'price' => 10000,
            'stock' => 2,
            'category_id' => $category->id,
        ])->assertForbidden();

        $this->apiWithToken($tokenAlmacenista)->postJson('/api/v1/products', [
            'name' => 'Creado Almacenista API',
            'price' => 25000,
            'stock' => 5,
            'category_id' => $category->id,
        ])->assertStatus(201);

        // GET /api/v1/kardex -> requiere ver-inventario
        $this->apiWithToken($tokenAlmacenista)->getJson('/api/v1/kardex')->assertOk();
        $this->apiWithToken($tokenAuditor)->getJson("/api/v1/products/{$product->id}/kardex")->assertOk();
        $this->apiWithToken($tokenVendedor)->getJson('/api/v1/kardex')->assertForbidden();
        $this->apiWithToken($tokenSinRol)->getJson('/api/v1/kardex')->assertForbidden();

        // GET /api/v1/sales & /api/v1/sales/today -> requiere ver-ventas
        $this->apiWithToken($tokenVendedor)->getJson('/api/v1/sales')->assertOk();
        $this->apiWithToken($tokenAuditor)->getJson('/api/v1/sales/today')->assertOk();
        $this->apiWithToken($tokenAlmacenista)->getJson('/api/v1/sales')->assertForbidden();
        $this->apiWithToken($tokenAlmacenista)->getJson('/api/v1/sales/today')->assertForbidden();

        // Clients API -> ver-clientes / crear-clientes
        $this->apiWithToken($tokenAlmacenista)->getJson('/api/v1/clients')->assertForbidden();
        $this->apiWithToken($tokenAuditor)->getJson("/api/v1/clients/{$client->id}")->assertOk();
        $this->apiWithToken($tokenAuditor)->postJson('/api/v1/clients', [
            'document_number' => '7766554433',
            'name' => 'No permitido auditor',
        ])->assertForbidden();
        $this->apiWithToken($tokenVendedor)->postJson('/api/v1/clients', [
            'document_number' => '7766554434',
            'name' => 'Permitido vendedor',
        ])->assertStatus(201);

        // Multi-role vía /api/v1/auth/login (sin rol:) no debe filtrar permisos por unión de roles
        $legacyLogin = $this->postJson('/api/v1/auth/login', [
            'email' => 'supervisor@test.com',
            'password' => 'password',
        ])->assertOk();
        $legacyToken = $legacyLogin->json('data.token');

        $this->apiWithToken($legacyToken)->getJson('/api/v1/sales')->assertForbidden();
        $this->apiWithToken($legacyToken)->getJson('/api/v1/kardex')->assertForbidden();

        // Multi-role vía /api/v1/login con rol:vendedor respeta alcance del rol activo
        $canonicalLogin = $this->postJson('/api/v1/login', [
            'email' => 'supervisor@test.com',
            'password' => 'password',
            'device_name' => 'pos',
            'role' => 'vendedor',
        ])->assertStatus(201);
        $vendedorScopedToken = $canonicalLogin->json('token');

        $this->apiWithToken($vendedorScopedToken)->getJson('/api/v1/sales')->assertOk();
        $this->apiWithToken($vendedorScopedToken)->getJson('/api/v1/kardex')->assertForbidden();
    }

    public function test_notifications_exclude_inactive_users_across_all_channels(): void
    {
        Notification::fake();

        // Desactivar almacenista, dejar admin activo
        $this->almacenista->update(['activo' => false]);

        $product = Product::where('active', true)->first();
        $product->update(['stock' => 6]);

        // 1. CheckLowStock (evento SaleRegistered) no notifica a almacenista inactivo
        app(SaleService::class)->register([
            'client_id' => Client::first()->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ], $this->cajero->id);

        Notification::assertSentTo($this->admin, LowStockNotification::class);
        Notification::assertNotSentTo($this->almacenista, LowStockNotification::class);

        // 2. LowStockDetected (SendLowStockNotification) no notifica a almacenista inactivo
        Notification::fake();
        event(new LowStockDetected($product, 3));
        Notification::assertSentTo($this->admin, LowStockNotification::class);
        Notification::assertNotSentTo($this->almacenista, LowStockNotification::class);

        // 3. SendDailySummary (erp:resumen-diario) no notifica a auditor inactivo
        Notification::fake();
        $this->auditor->update(['activo' => false]);
        $this->artisan('erp:resumen-diario')->assertSuccessful();
        Notification::assertSentTo($this->admin, DailySummaryNotification::class);
        Notification::assertNotSentTo($this->auditor, DailySummaryNotification::class);
    }

    public function test_navbar_bell_renders_notification_title_instead_of_alerta_inventario(): void
    {
        $purchase = Purchase::create([
            'purchase_number' => 'OC-BELL-001',
            'provider_id' => Provider::first()->id,
            'user_id' => $this->admin->id,
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'recibida',
            'subtotal' => 10000,
            'tax' => 1900,
            'total' => 11900,
        ]);

        // Usar notifyNow en canal database o Fake Mail? No, Mail::fake() si database+mail, pero en phpunit.xml MAIL_MAILER=array!
        Mail::fake();
        $this->admin->notify(new PurchaseReceivedNotification($purchase, true));

        $dashboard = $this->actingAs($this->admin)->get(route('dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Compra recibida');
        $dashboard->assertSee('Ver detalle →');
    }

    public function test_purchase_received_listener_skips_inactive_creator(): void
    {
        Notification::fake();

        $this->almacenista->update(['activo' => false]);

        $purchase = Purchase::create([
            'purchase_number' => 'OC-INACT-001',
            'provider_id' => Provider::first()->id,
            'user_id' => $this->almacenista->id,
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'recibida',
            'subtotal' => 20000,
            'tax' => 3800,
            'total' => 23800,
        ]);

        event(new PurchaseReceived($purchase, true));

        Notification::assertNotSentTo($this->almacenista, PurchaseReceivedNotification::class);
    }
}
