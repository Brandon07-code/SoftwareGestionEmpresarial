<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'nombre',
            'descripcion',
            'precio',
            'stock',
            'categoria',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Café Especial 500g',
                'Café tostado y molido suave origen Huila',
                28000,
                30,
                'Bebidas',
            ],
            [
                'Mug Cerámica Turnix',
                'Taza institucional 11oz color blanco',
                15000,
                50,
                'Accesorios',
            ],
            [
                'Agenda Ejecutiva 2026',
                'Cuaderno argollado pasta dura con planeador',
                32000,
                20,
                'Papelería',
            ],
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '047857'],
                ],
            ],
        ];
    }
}
