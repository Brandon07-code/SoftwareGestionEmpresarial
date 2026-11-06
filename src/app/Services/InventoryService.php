<?php

namespace App\Services;

use App\Models\Product;

class InventoryService
{
    public function __construct(
        protected KardexService $kardexService = new KardexService
    ) {}

    /**
     * Registra el inventario inicial de un producto en el Kárdex y fija las existencias iniciales.
     */
    public function opening(Product $product, ?int $quantity = null, ?int $userId = null, string $docRef = 'IMPORT-EXCEL'): void
    {
        $qty = $quantity ?? (int) $product->stock;
        if ($qty > 0) {
            // Asegurar que la base de cálculo parta de 0 para reflejar el asiento de apertura
            $product->update(['stock' => 0]);

            $this->kardexService->record(
                $product,
                'AJUSTE_INICIAL',
                $qty,
                (float) $product->price,
                $docRef,
                $userId ?? auth()->id() ?? 1,
                'Inventario inicial por importación masiva de catálogo'
            );
        }
    }
}
