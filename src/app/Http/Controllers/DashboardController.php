<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Módulos mostrados según permisos del usuario
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
        ])->filter(fn ($m) => $user->can($m['permission']))->values();

        // Indicadores Clave de Desempeño (KPIs)
        $stats = collect();

        if ($user->can('ver-productos')) {
            $stats->push([
                'label' => 'Productos activos',
                'value' => Product::where('active', true)->count(),
                'icon' => '📦',
            ]);
            $stats->push([
                'label' => 'Stock bajo (≤ 5)',
                'value' => Product::where('active', true)->where('stock', '<=', 5)->count(),
                'icon' => '⚠️',
            ]);
            $stats->push([
                'label' => 'Valor del inventario',
                'value' => '$' . number_format((float) Product::where('active', true)->sum(DB::raw('price * stock')), 2),
                'icon' => '💰',
            ]);
        }

        if ($user->can('ver-clientes')) {
            $stats->push([
                'label' => 'Clientes activos',
                'value' => Client::where('active', true)->count(),
                'icon' => '👥',
            ]);
        }

        if ($user->can('ver-ventas')) {
            $salesThisMonth = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year);
            $stats->push([
                'label' => 'Ventas del mes',
                'value' => $salesThisMonth->count() . ' ($' . number_format((float) $salesThisMonth->sum('total'), 2) . ')',
                'icon' => '📈',
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
                ->with('product:id,name,price')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        }

        return view('dashboard', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'modules' => $modules,
            'stats' => $stats,
            'topProducts' => $topProducts,
        ]);
    }
}
