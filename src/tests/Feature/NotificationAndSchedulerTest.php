<?php

namespace Tests\Feature;

use App\Events\LowStockDetected;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationAndSchedulerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $almacenista;

    protected User $cajero;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
    }

    public function test_low_stock_event_is_dispatched_and_listener_notifies_admin_and_almacenista(): void
    {
        Notification::fake();

        $product = Product::where('active', true)->first();

        // Disparar evento de stock crítico
        event(new LowStockDetected($product, 3));

        // Verificar que la notificación fue enviada a los roles correspondientes
        Notification::assertSentTo(
            [$this->admin, $this->almacenista],
            LowStockNotification::class,
            function ($notification, $channels) use ($product) {
                $this->assertContains('database', $channels);
                $this->assertContains('mail', $channels);

                return $notification->product->id === $product->id && $notification->currentStock === 3;
            }
        );

        // El cajero no debe recibir alertas operativas de inventario
        Notification::assertNotSentTo($this->cajero, LowStockNotification::class);
    }

    public function test_sale_automatically_triggers_low_stock_notification(): void
    {
        $client = Client::where('active', true)->first();
        $product = Product::where('active', true)->first();

        // Forzar stock en 7
        $product->update(['stock' => 7]);

        $saleService = app(SaleService::class);

        // Vender 3 unidades -> stock final será 4 (<= 5, crítico)
        $saleService->register([
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
        $this->assertEquals(4, $product->stock);

        // Comprobar que en la base de datos se registró la notificación para el administrador
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
            'type' => LowStockNotification::class,
        ]);

        $notification = $this->admin->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals($product->id, $notification->data['product_id']);
        $this->assertEquals(4, $notification->data['stock']);
    }

    public function test_notifications_can_be_marked_as_read(): void
    {
        $product = Product::where('active', true)->first();

        // Enviar notificación real a la base de datos
        $this->admin->notify(new LowStockNotification($product, 2));

        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        // Marcar todas como leídas vía endpoint web
        $response = $this->actingAs($this->admin)
            ->post(route('notifications.markAllAsRead'));

        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());
    }

    public function test_check_low_stock_artisan_command_scans_and_notifies(): void
    {
        $product = Product::where('active', true)->first();
        $product->update(['stock' => 2]); // Crítico

        // Ejecutar comando Artisan
        $this->artisan('turnix:check-low-stock', ['--threshold' => 5])
            ->expectsOutputToContain('Iniciando escaneo de inventario crítico')
            ->assertExitCode(0);

        // Se debió registrar la notificación en la base de datos
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
            'type' => LowStockNotification::class,
        ]);
    }

    public function test_scheduler_has_registered_low_stock_command(): void
    {
        $schedule = app(Schedule::class);
        $events = collect($schedule->events());

        // Buscar el comando registrado en la consola
        $lowStockEvent = $events->first(function ($event) {
            return str_contains($event->command ?? '', 'turnix:check-low-stock') ||
                   str_contains($event->description ?? '', 'bajo inventario');
        });

        $this->assertNotNull($lowStockEvent, 'El comando turnix:check-low-stock debe estar registrado en el Scheduler.');
        $this->assertEquals('0 8 * * *', $lowStockEvent->expression, 'El Scheduler debe ejecutarse diariamente a las 08:00 AM.');
    }
}
