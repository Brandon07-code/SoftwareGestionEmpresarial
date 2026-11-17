<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * «Se registró una venta». El evento solo AVISA lo que pasó; quién reacciona
 * (alertas, correos, contabilidad...) lo deciden los listeners.
 *
 * ShouldDispatchAfterCommit: el evento sale solo si la transacción de la venta
 * se confirmó. Si la venta falla y hace rollback, nadie recibe una alerta falsa.
 */
class SaleRegistered implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(public Sale $sale) {}
}
