<?php

namespace App\Services;

use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KardexService
{
    public const ENTRY_TYPES = [
        'ENTRADA_COMPRA',
        'ANULACION_VENTA',
        'AJUSTE_INICIAL',
        'AJUSTE_POSITIVO',
    ];

    public const EXIT_TYPES = [
        'SALIDA_VENTA',
        'AJUSTE_NEGATIVO',
        'AJUSTE_STOCK',
    ];

    /**
     * Registra un movimiento en el Kárdex y actualiza atómicamente el stock del producto.
     *
     * @param  string  $type  ENTRADA_COMPRA | SALIDA_VENTA | ANULACION_VENTA | AJUSTE_INICIAL | AJUSTE_POSITIVO | AJUSTE_NEGATIVO | AJUSTE_STOCK
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
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad del movimiento en Kárdex debe ser mayor a cero.',
            ]);
        }

        $allowedTypes = array_merge(self::ENTRY_TYPES, self::EXIT_TYPES);
        if (! in_array($type, $allowedTypes, true)) {
            throw ValidationException::withMessages([
                'movement_type' => "Tipo de movimiento Kárdex inválido: {$type}.",
            ]);
        }

        $stockBefore = (int) $product->stock;

        $isEntry = in_array($type, self::ENTRY_TYPES, true);
        $stockAfter = $isEntry ? ($stockBefore + $quantity) : ($stockBefore - $quantity);

        if ($stockAfter < 0) {
            throw ValidationException::withMessages([
                'quantity' => "El stock de «{$product->name}» no puede quedar negativo (disponible: {$stockBefore}, requerido: {$quantity}).",
            ]);
        }

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

    /**
     * Registra un ajuste físico de inventario (AJUSTE_POSITIVO o AJUSTE_NEGATIVO) con bloqueo pesimista.
     */
    public function adjust(
        int $productId,
        string $type,
        int $quantity,
        string $reason,
        int $userId,
        ?string $documentReference = null
    ): Kardex {
        if (! in_array($type, ['AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO'], true)) {
            throw ValidationException::withMessages([
                'movement_type' => 'El tipo de ajuste debe ser AJUSTE_POSITIVO o AJUSTE_NEGATIVO.',
            ]);
        }

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad del ajuste debe ser al menos 1 unidad.',
            ]);
        }

        $cleanReason = trim($reason);
        if (mb_strlen($cleanReason) < 10) {
            throw ValidationException::withMessages([
                'reason' => 'El motivo del ajuste debe tener al menos 10 caracteres.',
            ]);
        }

        return DB::transaction(function () use ($productId, $type, $quantity, $cleanReason, $userId, $documentReference): Kardex {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($type === 'AJUSTE_NEGATIVO' && $product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stock insuficiente para ajuste negativo en «{$product->name}». Disponible: {$product->stock}, solicitado: {$quantity}.",
                ]);
            }

            $reference = $documentReference ? trim($documentReference) : 'AJUSTE-'.now()->format('YmdHis');

            return $this->record(
                $product,
                $type,
                $quantity,
                (float) $product->price,
                $reference,
                $userId,
                $cleanReason
            );
        });
    }
}
