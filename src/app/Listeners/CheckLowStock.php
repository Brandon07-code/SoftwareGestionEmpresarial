<?php

namespace App\Listeners;

use App\Events\SaleRegistered;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

/**
 * Regla automática: cuando una venta deja un producto en stock bajo, avisar a quienes compran.
 * Laravel conecta este listener con el evento automáticamente por el tipo del parámetro de handle().
 */
class CheckLowStock
{
    public function handle(SaleRegistered $event): void
    {
        $threshold = config('erp.low_stock_threshold', 5);
        $event->sale->loadMissing('details.product');

        foreach ($event->sale->details as $detail) {
            $product = $detail->product?->fresh();
            if (! $product) {
                continue;
            }

            $before = $product->stock + $detail->quantity;

            // Solo se alerta cuando el producto CRUZA el umbral (evita repetir la alerta en cada venta)
            if ($before > $threshold && $product->stock <= $threshold) {
                Notification::send($this->recipients(), new LowStockNotification($product));
            }
        }
    }

    // Usuarios activos que pueden crear compras (por rol o por permiso directo)
    private function recipients()
    {
        $query = User::permission('crear-compras');

        if (Schema::hasColumn('users', 'activo')) {
            $query->where('activo', true);
        }

        return $query->get();
    }
}
