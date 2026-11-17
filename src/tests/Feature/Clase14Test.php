<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\PurchaseReceivedNotification;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class Clase14Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $almacenista;

    private User $vendedor;

    private Category $category;

    private Client $client;

    private Provider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->category = Category::create([
            'name' => 'General',
            'description' => 'Categoría general',
            'active' => true,
        ]);

        $this->client = Client::create([
            'name' => 'Cliente Prueba',
            'document_type' => 'CC',
            'document_number' => '123456789',
            'email' => 'cliente@test.com',
            'phone' => '3001234567',
            'active' => true,
        ]);

        $this->provider = Provider::create([
            'name' => 'Proveedor Prueba S.A.S.',
            'document_type' => 'NIT',
            'document_number' => '900123456-1',
            'email' => 'proveedor@test.com',
            'phone' => '6021234567',
            'active' => true,
        ]);

        $this->admin = User::factory()->create(['name' => 'Admin Test', 'email' => 'admin@test.com']);
        $this->admin->assignRole('admin');

        $this->almacenista = User::factory()->create(['name' => 'Almacenista Test', 'email' => 'almacen@test.com']);
        $this->almacenista->assignRole('almacenista');

        $this->vendedor = User::factory()->create(['name' => 'Vendedor Test', 'email' => 'vendedor@test.com']);
        $this->vendedor->assignRole('vendedor');
    }

    public function test_sale_registered_event_dispatched_and_triggers_low_stock_notification(): void
    {
        Notification::fake();

        $product = Product::create([
            'name' => 'Diadema HyperX',
            'description' => 'Auricular gamer',
            'price' => 120000,
            'stock' => 6,
            'category_id' => $this->category->id,
            'active' => true,
        ]);

        $saleService = app(SaleService::class);

        // Venta de 1 unidad: stock pasa de 6 a 5 (cruza el umbral de 5)
        $saleService->register([
            'client_id' => $this->client->id,
            'sale_date' => now()->format('Y-m-d'),
            'notes' => 'Venta prueba clase 14',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ], $this->admin->id);

        $product->refresh();
        $this->assertEquals(5, $product->stock);

        // Se envía a usuarios con 'crear-compras' (admin y almacenista)
        Notification::assertSentTo(
            [$this->admin, $this->almacenista],
            LowStockNotification::class,
            function ($notification) use ($product) {
                return $notification->product->id === $product->id;
            }
        );

        // No se debe enviar al vendedor (no tiene crear-compras)
        Notification::assertNotSentTo($this->vendedor, LowStockNotification::class);
    }

    public function test_selling_already_low_stock_product_does_not_repeat_alert(): void
    {
        Notification::fake();

        $product = Product::create([
            'name' => 'Mouse Gamer',
            'description' => 'Mouse óptico',
            'price' => 50000,
            'stock' => 5, // Ya estaba en el umbral
            'category_id' => $this->category->id,
            'active' => true,
        ]);

        $saleService = app(SaleService::class);

        // Venta de 1 unidad: stock pasa de 5 a 4 (NO cruza el umbral, ya estaba bajo)
        $saleService->register([
            'client_id' => $this->client->id,
            'sale_date' => now()->format('Y-m-d'),
            'notes' => 'Segunda venta',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ], $this->admin->id);

        $product->refresh();
        $this->assertEquals(4, $product->stock);

        // No se debe repetir la alerta
        Notification::assertNothingSent();
    }

    public function test_purchase_received_dispatches_event_and_notifies_user_with_pdf(): void
    {
        Notification::fake();

        $product = Product::create([
            'name' => 'Cable HDMI 2.1',
            'description' => 'Cable ultra rápido',
            'price' => 45000,
            'stock' => 10,
            'category_id' => $this->category->id,
            'active' => true,
        ]);

        $purchase = Purchase::create([
            'purchase_number' => 'OC-2026-0001',
            'provider_id' => $this->provider->id,
            'user_id' => $this->almacenista->id,
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'pendiente',
            'subtotal' => 30000,
            'tax' => 5700,
            'total' => 35700,
        ]);

        $detail = $purchase->details()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'received_quantity' => 0,
            'unit_cost' => 6000,
            'subtotal' => 30000,
        ]);

        $purchaseService = app(PurchaseService::class);

        // Recepción de la compra
        $purchaseService->receive($purchase, [
            $detail->id => 5,
        ], 'FAC-PROV-999');

        $purchase->refresh();
        $this->assertEquals('recibida', $purchase->status);

        // Notifica al usuario creador de la orden (almacenista)
        Notification::assertSentTo(
            $this->almacenista,
            PurchaseReceivedNotification::class,
            function ($notification) use ($purchase) {
                return $notification->purchase->id === $purchase->id && $notification->complete === true;
            }
        );
    }

    public function test_daily_summary_command_sends_notification(): void
    {
        Notification::fake();

        Product::create([
            'name' => 'Teclado Mecánico',
            'description' => 'RGB Switch Red',
            'price' => 180000,
            'stock' => 2,
            'category_id' => $this->category->id,
            'active' => true,
        ]);

        $this->artisan('erp:resumen-diario')
            ->assertSuccessful();

        // Se envía a usuarios con 'ver-reportes' (Admin)
        Notification::assertSentTo(
            $this->admin,
            DailySummaryNotification::class,
            function ($notification) {
                return $notification->summary['sales_count'] === 0
                    && in_array('Teclado Mecánico', $notification->summary['low_stock']);
            }
        );
    }

    public function test_notifications_web_screens_and_actions(): void
    {
        $product = Product::create([
            'name' => 'Monitor 24',
            'price' => 500000,
            'stock' => 3,
            'category_id' => $this->category->id,
            'active' => true,
        ]);

        $this->admin->notify(new LowStockNotification($product));
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        $notification = $this->admin->unreadNotifications()->first();

        // 1. Ver vista de notificaciones
        $response = $this->actingAs($this->admin)->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('Notificaciones');
        $response->assertSee('Monitor 24');

        // 2. Marcar una como leída
        $patchResponse = $this->actingAs($this->admin)->patch(route('notifications.read', $notification->id));
        $patchResponse->assertRedirect();
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());

        // 3. Crear otra y marcar todas como leídas
        $this->admin->notify(new LowStockNotification($product));
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        $postResponse = $this->actingAs($this->admin)->post(route('notifications.read-all'));
        $postResponse->assertSessionHas('success');
        $this->assertEquals(0, $this->admin->fresh()->unreadNotifications()->count());
    }

    public function test_scheduler_has_registered_daily_summary_and_activitylog_clean(): void
    {
        $schedule = app(Schedule::class);
        $events = collect($schedule->events());

        $dailySummaryEvent = $events->first(function ($event) {
            return str_contains($event->command ?? '', 'erp:resumen-diario');
        });

        $this->assertNotNull($dailySummaryEvent, 'El comando erp:resumen-diario debe estar registrado en el Scheduler');
        $this->assertEquals('America/Bogota', $dailySummaryEvent->timezone);
    }
}
