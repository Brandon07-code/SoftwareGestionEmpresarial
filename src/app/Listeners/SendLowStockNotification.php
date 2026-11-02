<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

class SendLowStockNotification
{
    public function handle(LowStockDetected $event): void
    {
        // Enviar notificación a usuarios con rol 'admin' y 'almacenista'
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'almacenista']);
        })->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new LowStockNotification($event->product, $event->currentStock));
        }
    }
}
