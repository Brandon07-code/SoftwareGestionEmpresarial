<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exporta el catálogo de productos a Excel.
 * FromQuery: lee de la BD por partes (sirve con miles de registros sin agotar la memoria).
 */
class ProductsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private ?string $search = null) {}

    // Excel v4 exige el tipo de retorno en query() y map()
    public function query(): Builder
    {
        return Product::query()
            ->with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name');
    }

    // Primera fila del archivo
    public function headings(): array
    {
        return ['ID', 'Nombre', 'Descripción', 'Categoría', 'Precio', 'Stock', 'Valor inventario', 'Estado'];
    }

    // Cómo se convierte cada producto en una fila
    public function map(mixed $product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description,
            $product->category?->name,
            (float) $product->price,
            $product->stock,
            (float) $product->price * $product->stock,
            $product->active ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
