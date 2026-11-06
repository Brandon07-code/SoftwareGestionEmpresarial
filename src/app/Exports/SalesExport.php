<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exporta las ventas de un periodo (para el contador o para análisis en Excel).
 */
class SalesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(private ?string $from = null, private ?string $to = null) {}

    // Excel v4 exige el tipo de retorno en query() y map()
    public function query(): Builder
    {
        return Sale::query()
            ->with(['client', 'user'])
            ->when($this->from, fn ($q) => $q->whereDate('sale_date', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('sale_date', '<=', $this->to))
            ->orderBy('sale_date')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return ['Factura', 'Fecha', 'Cliente', 'Documento', 'Vendedor', 'Subtotal', 'IVA', 'Total', 'Estado'];
    }

    public function map(mixed $sale): array
    {
        return [
            $sale->invoice_number,
            $sale->sale_date?->format('Y-m-d') ?? '',
            $sale->client?->name,
            $sale->client?->document_number,
            $sale->user?->name,
            (float) $sale->subtotal,
            (float) $sale->tax,
            (float) $sale->total,
            $sale->status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return 'Ventas';
    }
}
