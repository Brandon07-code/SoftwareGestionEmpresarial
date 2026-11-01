<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Enumerable
    {
        return Product::with('category')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre del Producto',
            'Descripción',
            'Categoría',
            'Precio ($)',
            'Stock Actual',
            'Estado',
            'Fecha de Registro',
        ];
    }

    /**
     * @param  Product|mixed  $product
     */
    public function map(mixed $product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description ?? 'Sin descripción',
            $product->category?->name ?? 'Sin categoría',
            number_format((float) $product->price, 2, '.', ''),
            $product->stock,
            $product->active ? 'Activo' : 'Inactivo',
            $product->created_at ? $product->created_at->format('Y-m-d H:i') : '',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'],
                ],
            ],
        ];
    }
}
