<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Importación masiva de productos desde Excel/CSV.
 * - WithHeadingRow: la primera fila trae los nombres de columna (nombre, precio, ...).
 * - WithValidation: cada fila se valida con reglas de Laravel.
 * - SkipsOnFailure + SkipsFailures: las filas con errores se OMITEN y se reportan; las demás se guardan.
 * Regla de negocio: si el producto ya existe (mismo nombre) se actualizan precio, descripción y categoría,
 * pero NUNCA el stock (el stock solo cambia por movimientos del kardex).
 */
class ProductsImport implements SkipsOnFailure, ToCollection, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $created = 0;

    public int $updated = 0;

    public int $importedCount = 0;

    public function __construct(private ?InventoryService $inventory = null)
    {
        $this->inventory ??= app(InventoryService::class);
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $category = Category::where('name', trim((string) $row['categoria']))->first();
            $product = Product::where('name', trim((string) $row['nombre']))->first();

            if ($product) {
                $product->update([
                    'description' => $row['descripcion'] ?? $product->description,
                    'price' => $row['precio'],
                    'category_id' => $category?->id ?? $product->category_id,
                ]);
                $this->updated++;
                $this->importedCount++;

                continue;
            }

            $product = Product::create([
                'name' => trim((string) $row['nombre']),
                'description' => $row['descripcion'] ?? null,
                'price' => $row['precio'],
                'stock' => (int) ($row['stock'] ?? 0),
                'category_id' => $category?->id,
                'active' => true,
            ]);

            // El stock del archivo entra al kardex como «inventario inicial»
            $this->inventory->opening($product);
            $this->created++;
            $this->importedCount++;
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'categoria' => ['required', Rule::exists('categories', 'name')->whereNull('deleted_at')],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.exists' => 'La categoría «:input» no existe. Créela primero o corrija el nombre.',
        ];
    }
}
