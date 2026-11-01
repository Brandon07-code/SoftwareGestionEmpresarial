<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SaleApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Sale::query()
            ->with(['client:id,name,document_number', 'user:id,name'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('invoice_number', 'like', $search)
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', $search));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('sale_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('sale_date', '<=', $request->input('to')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('sale_date')
            ->orderByDesc('id');

        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $paginated = $query->paginate($perPage);

        return $this->sendResponse([
            'current_page' => $paginated->currentPage(),
            'total_items' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'last_page' => $paginated->lastPage(),
            'items' => $paginated->items(),
        ], 'Listado de ventas registradas.');
    }

    public function today(): JsonResponse
    {
        $today = now()->format('Y-m-d');

        $sales = Sale::whereDate('sale_date', $today)
            ->with(['client:id,name,document_number', 'user:id,name', 'details.product:id,name'])
            ->orderByDesc('id')
            ->get();

        $activeSales = $sales->where('status', 'pagada');
        $totalCollected = (float) $activeSales->sum('total');
        $subtotal = (float) $activeSales->sum('subtotal');
        $tax = (float) $activeSales->sum('tax');

        return $this->sendResponse([
            'date' => $today,
            'count' => $sales->count(),
            'paid_count' => $activeSales->count(),
            'canceled_count' => $sales->where('status', 'anulada')->count(),
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total_collected' => round($totalCollected, 2),
            'sales' => $sales,
        ], "Ventas registradas para la fecha {$today}.");
    }

    public function show(int|string $id): JsonResponse
    {
        $sale = Sale::with(['client', 'user:id,name', 'details.product:id,name,price'])->find($id);

        if (! $sale) {
            return $this->sendError("La venta con ID {$id} no fue encontrada.", [], 404);
        }

        return $this->sendResponse($sale, 'Detalle de la factura de venta.');
    }

    public function store(Request $request, SaleService $service): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'sale_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'client_id.required' => 'Debe asociar un cliente válido.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'items.required' => 'Debe incluir al menos un producto en la venta.',
            'items.min' => 'Debe incluir al menos un producto en la venta.',
            'items.*.product_id.exists' => 'Uno de los productos seleccionados no existe en el catálogo.',
            'items.*.quantity.min' => 'La cantidad mínima por producto es 1.',
        ]);

        $validated['sale_date'] = $validated['sale_date'] ?? now()->format('Y-m-d');

        try {
            $sale = $service->register($validated, $request->user()->id);
            $sale->load(['client:id,name,document_number', 'user:id,name', 'details.product:id,name']);

            return $this->sendResponse(
                $sale,
                "Venta «{$sale->invoice_number}» registrada exitosamente con descarga de inventario.",
                201
            );
        } catch (ValidationException $e) {
            return $this->sendError('Inconsistencia en el registro de la venta.', $e->errors(), 422);
        } catch (\Throwable $e) {
            return $this->sendError('Error interno al procesar la venta.', ['error' => $e->getMessage()], 500);
        }
    }
}
