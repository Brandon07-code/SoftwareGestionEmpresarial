<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispensingService
{
    /**
     * Despacha una cantidad solicitada de un producto aplicando la regla sanitaria FEFO
     * (First Expired, First Out) dentro de una transacción atómica de base de datos.
     *
     * @param Product $product
     * @param int $quantity
     * @return array Resumen de lotes descontados
     * @throws ValidationException
     */
    public function dispense(Product $product, int $quantity): array
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad a dispensar debe ser mayor a cero.'
            ]);
        }

        return DB::transaction(function () use ($product, $quantity) {
            $today = now()->toDateString();

            // Bloquear los lotes vigentes ordenados por fecha de vencimiento ascendente (FEFO)
            $availableLots = $product->lots()
                ->where('expiration_date', '>=', $today)
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date', 'asc')
                ->lockForUpdate()
                ->get();

            $totalAvailable = (int) $availableLots->sum('quantity');

            // Validar que el stock total vigente no vencido cubra la cantidad requerida
            if ($totalAvailable < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stock no vencido insuficiente para '{$product->name}'. Solicitado: {$quantity}, disponible vigente: {$totalAvailable}."
                ]);
            }

            $dispensedDetails = [];
            $remaining = $quantity;

            // Descontar primero del lote que vence antes (FEFO)
            foreach ($availableLots as $lot) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($remaining, $lot->quantity);
                $lot->decrement('quantity', $take);
                $remaining -= $take;

                $dispensedDetails[] = [
                    'lot_id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'expiration_date' => is_string($lot->expiration_date) ? $lot->expiration_date : $lot->expiration_date->format('Y-m-d'),
                    'quantity_deducted' => $take,
                    'remaining_lot_stock' => $lot->fresh()->quantity,
                ];
            }

            // Descontar también el stock total acumulado del producto
            $product->decrement('stock', $quantity);

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'requested_quantity' => $quantity,
                'total_product_stock_now' => $product->fresh()->stock,
                'dispensed_lots' => $dispensedDetails,
            ];
        });
    }
}
