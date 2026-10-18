<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $modules = collect([
            [
                'title' => 'Productos',
                'description' => $user->can('crear-productos') ? 'Administra el inventario.' : 'Consulta el inventario.',
                'route' => 'products.index',
                'permission' => 'ver-productos',
            ],
            [
                'title' => 'Categorías',
                'description' => $user->can('crear-categorias') ? 'Organiza los productos.' : 'Consulta las categorías.',
                'route' => 'categories.index',
                'permission' => 'ver-categorias',
            ],
            [
                'title' => 'Clientes',
                'description' => $user->can('crear-clientes') ? 'Administra los clientes.' : 'Consulta los clientes.',
                'route' => 'clients.index',
                'permission' => 'ver-clientes',
            ],
            [
                'title' => 'Ventas',
                'description' => $user->can('crear-ventas') ? 'Registra y consulta ventas.' : 'Consulta las ventas.',
                'route' => 'sales.index',
                'permission' => 'ver-ventas',
            ],
            [
                'title' => 'Proveedores',
                'description' => $user->can('crear-proveedores') ? 'Administra los proveedores.' : 'Consulta los proveedores.',
                'route' => 'providers.index',
                'permission' => 'ver-proveedores',
            ],
            [
                'title' => 'Compras',
                'description' => $user->can('crear-compras') ? 'Registra órdenes y recibe mercancía.' : 'Consulta las compras.',
                'route' => 'purchases.index',
                'permission' => 'ver-compras',
            ],
        ])->filter(fn ($m) => $user->can($m['permission']))->values();

        $stats = collect();

        if ($user->can('ver-productos')) {
            $stats->push(['label' => 'Productos activos', 'value' => Product::where('active', true)->count()]);
            $stats->push(['label' => 'Stock bajo (≤ 5)', 'value' => Product::where('active', true)->where('stock', '<=', 5)->count()]);
            $stats->push([
                'label' => 'Valor del inventario',
                'value' => '$'.number_format((float) Product::where('active', true)->sum(DB::raw('price * stock')), 2),
            ]);
        }

        if ($user->can('ver-clientes')) {
            $stats->push(['label' => 'Clientes activos', 'value' => Client::where('active', true)->count()]);
        }

        $salesMonthTotal = 0;
        if ($user->can('ver-ventas')) {
            $salesThisMonth = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year);
            $salesMonthTotal = (float) $salesThisMonth->sum('total');
            $stats->push([
                'label' => 'Ventas del mes',
                'value' => $salesThisMonth->count().' ($'.number_format($salesMonthTotal, 2).')',
            ]);
        }

        // --- KPIs de compras (Clase 10) ---
        $purchasesMonthTotal = 0;
        if ($user->can('ver-compras')) {
            $purchasesThisMonth = Purchase::whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year);
            $purchasesMonthTotal = (float) $purchasesThisMonth->sum('total');
            $stats->push([
                'label' => 'Compras del mes',
                'value' => $purchasesThisMonth->count().' ($'.number_format($purchasesMonthTotal, 2).')',
            ]);
            $stats->push([
                'label' => 'Órdenes por recibir',
                'value' => Purchase::whereIn('status', ['pendiente', 'parcial'])->count(),
            ]);
        }

        if ($user->can('ver-proveedores')) {
            $stats->push(['label' => 'Proveedores activos', 'value' => Provider::where('active', true)->count()]);
        }

        // Indicador financiero simple: lo que entró por ventas menos lo que salió por compras en el mes
        if ($user->can('ver-reportes')) {
            $stats->push([
                'label' => 'Ventas − Compras del mes',
                'value' => '$'.number_format($salesMonthTotal - $purchasesMonthTotal, 2),
            ]);
        }

        // Top 5 productos más vendidos del mes
        $topProducts = collect();
        if ($user->can('ver-reportes') || $user->can('ver-ventas')) {
            $topProducts = SaleDetail::query()
                ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->whereHas('sale', function ($q) {
                    $q->whereMonth('sale_date', now()->month)
                        ->whereYear('sale_date', now()->year);
                })
                ->with('product:id,name')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        }

        // Sugerencia de reabastecimiento: productos activos con stock bajo (Clase 10)
        $lowStock = collect();
        if ($user->can('crear-compras')) {
            $lowStock = Product::where('active', true)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->limit(10)
                ->get(['id', 'name', 'stock']);
        }

        return view('dashboard', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'modules' => $modules,
            'stats' => $stats,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
        ]);
    }
}
