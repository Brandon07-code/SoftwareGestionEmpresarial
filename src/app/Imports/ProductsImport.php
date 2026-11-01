<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\KardexService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements SkipsEmptyRows, SkipsOnFailure, ToCollection, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $importedCount = 0;

    /**
     * @param  Collection<array-key, mixed>  $collection
     */
    public function collection(Collection $collection): void
    {
        $kardexService = app(KardexService::class);
        $userId = auth()->id();

        DB::transaction(function () use ($collection, $kardexService, $userId) {
            foreach ($collection as $row) {
                $categoryName = trim((string) ($row['categoria'] ?? 'General'));
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'description' => 'Categoría generada automáticamente desde importación masiva',
                        'active' => true,
                    ]
                );

                $initialStock = (int) round((float) ($row['stock'] ?? 0));
                $price = round((float) ($row['precio'] ?? 0), 2);

                $product = Product::create([
                    'name' => trim((string) $row['nombre']),
                    'description' => isset($row['descripcion']) && trim((string) $row['descripcion']) !== ''
                        ? trim((string) $row['descripcion'])
                        : null,
                    'price' => $price,
                    'stock' => 0,
                    'category_id' => $category->id,
                    'active' => true,
                ]);

                if ($initialStock > 0) {
                    $kardexService->record(
                        $product,
                        'AJUSTE_INICIAL',
                        $initialStock,
                        $price,
                        'IMPORT-EXCEL',
                        $userId,
                        'Carga inicial mediante importación masiva de Excel'
                    );
                }

                $this->importedCount++;
            }
        });
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:products,name'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'categoria' => ['required', 'string', 'max:100'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto registrado con este nombre.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock inicial es obligatorio.',
            'stock.numeric' => 'El stock debe ser un valor numérico.',
            'stock.min' => 'El stock no puede ser negativo.',
            'categoria.required' => 'La categoría es obligatoria.',
        ];
    }
}
