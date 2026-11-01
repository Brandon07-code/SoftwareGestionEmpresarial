<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Enumerable
    {
        return Sale::with(['client', 'user', 'details.product'])->orderByDesc('sale_date')->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return [
            'Factura',
            'Fecha',
            'Cliente',
            'Documento Cliente',
            'Vendedor',
            'Subtotal ($)',
            'IVA ($)',
            'Total ($)',
            'Estado',
            'Observaciones',
        ];
    }

    /**
     * @param  Sale|mixed  $sale
     */
    public function map(mixed $sale): array
    {
        return [
            $sale->invoice_number,
            $sale->sale_date ? $sale->sale_date->format('Y-m-d') : '',
            $sale->client?->name ?? 'Cliente General',
            $sale->client?->document_number ?? 'N/A',
            $sale->user?->name ?? 'Sistema',
            number_format((float) $sale->subtotal, 2, '.', ''),
            number_format((float) $sale->tax, 2, '.', ''),
            number_format((float) $sale->total, 2, '.', ''),
            strtoupper($sale->status),
            $sale->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1D4ED8'],
                ],
            ],
        ];
    }
}
