<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Services\KardexService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with('category:id,name')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', $search)
                        ->orWhere('description', 'like', $search);
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->has('active'), fn ($q) => $q->where('active', $request->boolean('active')))
            ->orderBy('name');

        if ($request->boolean('all')) {
            $products = $query->get();

            return $this->sendResponse([
                'total' => $products->count(),
                'items' => $products,
            ], 'Listado completo del catálogo de productos.');
        }

        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $paginated = $query->paginate($perPage);

        return $this->sendResponse([
            'current_page' => $paginated->currentPage(),
            'total_items' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'last_page' => $paginated->lastPage(),
            'items' => $paginated->items(),
        ], 'Catálogo de productos paginado.');
    }

    public function show(int|string $id): JsonResponse
    {
        $product = Product::with('category:id,name')->find($id);

        if (! $product) {
            return $this->sendError("El producto con ID {$id} no fue encontrado.", [], 404);
        }

        return $this->sendResponse($product, 'Detalle del producto recuperado.');
    }

    public function store(Request $request, KardexService $kardexService): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'active' => ['nullable', 'boolean'],
        ]);

        $initialStock = (int) $validated['stock'];
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => 0,
            'category_id' => $validated['category_id'],
            'active' => $validated['active'] ?? true,
        ]);

        if ($initialStock > 0) {
            $kardexService->record(
                $product,
                'AJUSTE_INICIAL',
                $initialStock,
                (float) $validated['price'],
                'API-REST',
                $request->user()->id,
                'Carga inicial registrada mediante API REST'
            );
        }

        $product->load('category:id,name');

        return $this->sendResponse($product, 'Producto creado exitosamente.', 201);
    }
}
