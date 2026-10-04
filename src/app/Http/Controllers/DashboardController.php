<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Cada módulo solo se muestra si el usuario tiene el permiso indicado
        $modules = collect([
            [
                'title' => 'Productos',
                'description' => $user->can('crear-productos')
                    ? 'Consulta y administra el inventario de productos.'
                    : 'Consulta el inventario de productos.',
                'route' => 'products.index',
                'permission' => 'ver-productos',
            ],
            [
                'title' => 'Categorías',
                'description' => $user->can('crear-categorias')
                    ? 'Organiza los productos por categoría.'
                    : 'Consulta las categorías de productos.',
                'route' => 'categories.index',
                'permission' => 'ver-categorias',
            ],
        ])->filter(fn (array $module) => $user->can($module['permission']))->values();

        $stats = collect();

        if ($user->can('ver-productos')) {
            $stats->push(['label' => 'Productos activos', 'value' => Product::where('active', true)->count()]);
            $stats->push(['label' => 'Stock bajo (≤ 5)', 'value' => Product::where('active', true)->where('stock', '<=', 5)->count()]);
            $stats->push(['label' => 'Valor del inventario', 'value' => '$ '.number_format((float) Product::where('active', true)->sum(DB::raw('price * stock')), 2)]);
        }

        if ($user->can('ver-categorias')) {
            $stats->push(['label' => 'Categorías activas', 'value' => Category::where('active', true)->count()]);
        }

        return view('dashboard', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'modules' => $modules,
            'stats' => $stats,
        ]);
    }
}
