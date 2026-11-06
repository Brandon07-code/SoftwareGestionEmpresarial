<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Listeners\SecurityAuditListener;
use App\Listeners\SendLowStockNotification;
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
            if ($user->hasRole('admin')) {
                return true;
            }

            if (method_exists($user, 'hasPermissionInActiveRole')) {
                return $user->hasPermissionInActiveRole($ability);
            }

            return null;
        });

        // Bitácora de Auditoría y Seguridad
        Event::subscribe(SecurityAuditListener::class);

        // Notificaciones automáticas de inventario (Clase 14)
        Event::listen(LowStockDetected::class, SendLowStockNotification::class);
    }
}
