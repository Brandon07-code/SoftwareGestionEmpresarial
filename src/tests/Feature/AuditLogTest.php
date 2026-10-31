<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $cajero;

    protected User $auditor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
        $this->auditor = User::where('email', 'auditor@test.com')->first();
    }

    public function test_product_update_records_before_and_after_attributes(): void
    {
        $category = Category::first();
        $product = Product::create([
            'name' => 'Loción Test Audit',
            'description' => 'Descripción inicial',
            'price' => 50000.00,
            'stock' => 10,
            'category_id' => $category->id,
            'active' => true,
        ]);

        // Actualizar precio y nombre como admin
        $this->actingAs($this->admin);
        $product->update([
            'name' => 'Loción Test Audit Modificada',
            'price' => 75000.00,
        ]);

        $activity = Activity::where('subject_type', Product::class)
            ->where('subject_id', $product->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('productos', $activity->log_name);

        $changes = $activity->attribute_changes ?: $activity->properties;

        // Validar trazabilidad Antes -> Después
        $this->assertEquals('Loción Test Audit', $changes['old']['name']);
        $this->assertEquals('Loción Test Audit Modificada', $changes['attributes']['name']);
        $this->assertEquals(50000.00, (float) $changes['old']['price']);
        $this->assertEquals(75000.00, (float) $changes['attributes']['price']);
    }

    public function test_purchase_and_sale_are_logged_in_audit_log(): void
    {
        $provider = Provider::where('active', true)->first();
        $product = Product::where('active', true)->first();
        $client = Client::where('active', true)->first();

        // 1. Compra
        $purchaseService = app(PurchaseService::class);
        $purchase = $purchaseService->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_cost' => 20000.00,
                ],
            ],
        ], $this->admin->id);

        $purchaseLog = Activity::where('subject_type', Purchase::class)
            ->where('subject_id', $purchase->id)
            ->first();
        $this->assertNotNull($purchaseLog);
        $this->assertEquals('compras', $purchaseLog->log_name);

        // 2. Venta
        $saleService = app(SaleService::class);
        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ], $this->cajero->id);

        $saleLog = Activity::where('subject_type', Sale::class)
            ->where('subject_id', $sale->id)
            ->first();
        $this->assertNotNull($saleLog);
        $this->assertEquals('ventas', $saleLog->log_name);
    }

    public function test_provider_and_client_are_logged_in_audit_log(): void
    {
        $provider = Provider::where('active', true)->first();
        $provider->update(['phone' => '3009998877']);

        $providerLog = Activity::where('subject_type', Provider::class)
            ->where('subject_id', $provider->id)
            ->where('event', 'updated')
            ->first();
        $this->assertNotNull($providerLog);
        $this->assertEquals('proveedores', $providerLog->log_name);
        $changesProv = $providerLog->attribute_changes ?: $providerLog->properties;
        $this->assertEquals('3009998877', $changesProv['attributes']['phone']);

        $client = Client::where('active', true)->first();
        $client->update(['address' => 'Carrera 5 # 10-20']);

        $clientLog = Activity::where('subject_type', Client::class)
            ->where('subject_id', $client->id)
            ->where('event', 'updated')
            ->first();
        $this->assertNotNull($clientLog);
        $this->assertEquals('clientes', $clientLog->log_name);
        $changesCli = $clientLog->attribute_changes ?: $clientLog->properties;
        $this->assertEquals('Carrera 5 # 10-20', $changesCli['attributes']['address']);
    }

    public function test_login_and_logout_events_are_recorded_in_security_audit_log(): void
    {
        // Disparar evento de Login
        event(new Login('web', $this->admin, false));

        $loginLog = Activity::where('log_name', 'seguridad')
            ->where('description', 'Inicio de sesión exitoso')
            ->where('causer_id', $this->admin->id)
            ->first();
        $this->assertNotNull($loginLog);

        // Disparar evento de Logout
        event(new Logout('web', $this->admin));

        $logoutLog = Activity::where('log_name', 'seguridad')
            ->where('description', 'Cierre de sesión de usuario')
            ->where('causer_id', $this->admin->id)
            ->first();
        $this->assertNotNull($logoutLog);
    }

    public function test_audit_index_view_is_accessible_to_auditor_and_admin_but_forbidden_to_cajero(): void
    {
        // Admin tiene acceso
        $this->actingAs($this->admin)->get(route('audit.index'))->assertOk();

        // Auditor tiene acceso
        $this->actingAs($this->auditor)->get(route('audit.index'))->assertOk();

        // Cajero NO tiene permiso para ver auditoría (403 Forbidden)
        $this->actingAs($this->cajero)->get(route('audit.index'))->assertForbidden();
    }
}
