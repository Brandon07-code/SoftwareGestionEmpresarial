<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KardexApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Kardex::query()
            ->with(['product:id,name,stock', 'user:id,name'])
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->integer('product_id')))
            ->when($request->filled('movement_type'), fn ($q) => $q->where('movement_type', $request->string('movement_type')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->orderByDesc('id');

        $perPage = min(max($request->integer('per_page', 20), 1), 100);
        $paginated = $query->paginate($perPage);

        return $this->sendResponse([
            'current_page' => $paginated->currentPage(),
            'total_items' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'last_page' => $paginated->lastPage(),
            'items' => $paginated->items(),
        ], 'Movimientos de inventario y Kárdex transaccional.');
    }

    public function productKardex(int|string $productId): JsonResponse
    {
        $product = Product::with('category:id,name')->find($productId);

        if (! $product) {
            return $this->sendError("Producto con ID {$productId} no encontrado.", [], 404);
        }

        $movements = Kardex::where('product_id', $productId)
            ->with('user:id,name')
            ->orderByDesc('id')
            ->get();

        return $this->sendResponse([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category?->name,
                'current_stock' => $product->stock,
                'price' => (float) $product->price,
            ],
            'movements_count' => $movements->count(),
            'movements' => $movements,
        ], "Kárdex histórico del producto «{$product->name}».");
    }
}
