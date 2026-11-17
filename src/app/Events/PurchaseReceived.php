<?php

namespace App\Events;

use App\Models\Purchase;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * «Llegó mercancía de una orden de compra» (recepción parcial o total).
 */
class PurchaseReceived implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(public Purchase $purchase, public bool $complete) {}
}
