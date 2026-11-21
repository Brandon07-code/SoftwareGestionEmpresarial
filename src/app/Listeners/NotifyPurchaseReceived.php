<?php

namespace App\Listeners;

use App\Events\PurchaseReceived;
use App\Notifications\PurchaseReceivedNotification;

/**
 * Cuando llega mercancía, se avisa a quien registró la orden de compra (con el PDF adjunto).
 */
class NotifyPurchaseReceived
{
    public function handle(PurchaseReceived $event): void
    {
        $user = $event->purchase->user;

        if ($user && $user->activo) {
            $user->notify(new PurchaseReceivedNotification($event->purchase, $event->complete));
        }
    }
}
