<?php

namespace App\Services;

use App\Models\Kardex;
use App\Models\Product;

class KardexService
{
    /**
     * Registra un movimiento en el Kárdex y actualiza atómicamente el stock del producto.
     *
     * @param  string  $type  ENTRADA_COMPRA | SALIDA_VENTA | ANULACION_VENTA | AJUSTE_INICIAL | AJUSTE_STOCK
     * @param  int  $quantity  Cantidad positiva
     * @param  float  $unitPrice  Costo o precio unitario
     * @param  string|null  $documentReference  Código de orden o factura (ej: OC-00001, FV-00001)
     * @param  int|null  $userId  Usuario responsable
     * @param  string|null  $description  Detalle del movimiento
     */
    public function record(
        Product $product,
        string $type,
        int $quantity,
        float $unitPrice,
        ?string $documentReference = null,
        ?int $userId = null,
        ?string $description = null
    ): Kardex {
        $stockBefore = (int) $product->stock;

        $isEntry = in_array($type, ['ENTRADA_COMPRA', 'ANULACION_VENTA', 'AJUSTE_INICIAL', 'AJUSTE_POSITIVO']);
        $stockAfter = $isEntry ? ($stockBefore + $quantity) : ($stockBefore - $quantity);

        // Actualizar stock del producto
        $product->update(['stock' => $stockAfter]);

        return Kardex::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'movement_type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'unit_price' => round($unitPrice, 2),
            'document_reference' => $documentReference,
            'description' => $description,
        ]);
    }
}
