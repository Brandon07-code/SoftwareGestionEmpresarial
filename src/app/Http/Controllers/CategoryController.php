<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    // Permiso de Spatie que exige cada acción
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-categorias', only: ['index']),
            new Middleware('permission:crear-categorias', only: ['create', 'store']),
            new Middleware('permission:editar-categorias', only: ['edit', 'update']),
            new Middleware('permission:eliminar-categorias', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && $request->user()->can('eliminar-categorias');

        $categories = Category::query()
            ->withCount('products')
            ->when($showTrashed, fn ($query) => $query->onlyTrashed())
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('active', $request->input('status') === 'active'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'showTrashed' => $showTrashed,
            'trashedCount' => Category::onlyTrashed()->count(),
        ]);
    }

    public function create()
    {
        return view('categories.create', [
            'category' => new Category(['active' => true]),
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Category $category)
    {
        // No se permite eliminar una categoría que todavía tiene productos
        if ($category->products()->exists()) {
            return back()->with('error', "No se puede eliminar «{$category->name}»: tiene productos asociados.");
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Categoría «{$category->name}» enviada a la papelera.");
    }

    public function restore(Category $category)
    {
        $category->restore();

        return redirect()->route('categories.index', ['trashed' => 1])
            ->with('success', "Categoría «{$category->name}» restaurada.");
    }
}
