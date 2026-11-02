<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\LowStockDetected;
use App\Models\Product;
use Illuminate\Console\Command;

class CheckLowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'turnix:check-low-stock {--threshold=5 : Umbral de unidades para considerar stock crítico}';

    /**
     * The console command description.
     */
    protected $description = 'Verifica existencias críticas de inventario (<= umbral) y emite notificaciones por correo y base de datos (Clase 14)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');
        $this->info("Iniciando escaneo de inventario crítico (umbral: <= {$threshold} unidades)...");

        $products = Product::where('active', true)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->get();

        if ($products->isEmpty()) {
            $this->info("✅ Inventario óptimo. No se detectaron productos con stock menor o igual a {$threshold}.");

            return self::SUCCESS;
        }

        $rows = [];
        foreach ($products as $product) {
            event(new LowStockDetected($product, (int) $product->stock));

            $rows[] = [
                $product->id,
                $product->name,
                $product->category?->name ?? 'Sin categoría',
                $product->stock,
                $product->price,
            ];
        }

        $this->table(['ID', 'Producto', 'Categoría', 'Stock', 'Precio'], $rows);
        $this->warn("⚠️ Se notificaron {$products->count()} productos con existencias críticas a los administradores y almacenistas.");

        return self::SUCCESS;
    }
}
