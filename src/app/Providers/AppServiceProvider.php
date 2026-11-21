<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Events\PurchaseReceived;
use App\Events\SaleRegistered;
use App\Listeners\CheckLowStock;
use App\Listeners\NotifyPurchaseReceived;
use App\Listeners\SecurityAuditListener;
use App\Listeners\SendLowStockNotification;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Control de permisos centralizado con soporte de rol activo
        Gate::before(function ($user, string $ability) {
            if (! $user instanceof User) {
                return null;
            }

            if ($user->activeRole()?->name === 'admin') {
                return true;
            }

            return $user->hasPermissionInActiveRole($ability) ? true : null;
        });

        // Bitácora de Auditoría y Seguridad
        Event::subscribe(SecurityAuditListener::class);

        // Notificaciones automáticas de inventario y compras (Clase 14)
        Event::listen(LowStockDetected::class, SendLowStockNotification::class);
        Event::listen(SaleRegistered::class, CheckLowStock::class);
        Event::listen(PurchaseReceived::class, NotifyPurchaseReceived::class);
    }
}
