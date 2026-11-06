<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-ventas', only: ['show']),
            new Middleware('permission:crear-ventas', only: ['store']),
        ];
    }

    // GET /api/v1/sales/{id}
    public function show(Sale $sale): SaleResource
    {
        return new SaleResource($sale->load(['client', 'user', 'details.product']));
    }

    /**
     * POST /api/v1/sales
     * Reutiliza exactamente la misma validación (StoreSaleRequest) y la misma lógica
     * de negocio (SaleService) de la pantalla web: stock, IVA, kardex y número de factura.
     */
    public function store(StoreSaleRequest $request, SaleService $service): JsonResponse
    {
        $sale = $service->register($request->validated(), $request->user()->id);

        return (new SaleResource($sale->load(['client', 'user', 'details.product'])))
            ->response()
            ->setStatusCode(201);
    }
}
