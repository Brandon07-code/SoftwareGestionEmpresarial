<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KardexController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-productos'),
        ];
    }

    public function index(Request $request)
    {
        $movements = Kardex::query()
            ->with(['product', 'user'])
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->integer('product_id')))
            ->when($request->filled('movement_type'), fn ($q) => $q->where('movement_type', $request->input('movement_type')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('document_reference', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhereHas('product', fn ($q) => $q->where('name', 'like', $search));
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $products = Product::orderBy('name')->get(['id', 'name']);

        $totalIn = Kardex::whereIn('movement_type', ['ENTRADA_COMPRA', 'ANULACION_VENTA', 'AJUSTE_INICIAL', 'AJUSTE_POSITIVO'])->sum('quantity');
        $totalOut = Kardex::whereIn('movement_type', ['SALIDA_VENTA', 'AJUSTE_NEGATIVO'])->sum('quantity');

        return view('kardex.index', [
            'movements' => $movements,
            'products' => $products,
            'selectedProduct' => $request->filled('product_id') ? Product::find($request->integer('product_id')) : null,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'totalMovements' => Kardex::count(),
        ]);
    }

    public function productKardex(Product $product)
    {
        return redirect()->route('kardex.index', ['product_id' => $product->id]);
    }
}
