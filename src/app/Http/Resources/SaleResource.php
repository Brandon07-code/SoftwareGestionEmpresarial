<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'factura' => $this->invoice_number,
            'fecha' => $this->sale_date?->format('Y-m-d'),
            'estado' => $this->status,
            'cliente' => [
                'id' => $this->client?->id,
                'nombre' => $this->client?->name,
                'documento' => $this->client?->document_number,
            ],
            'vendedor' => $this->user?->name,
            'detalle' => $this->whenLoaded('details', fn () => $this->details->map(fn ($d) => [
                'producto_id' => $d->product_id,
                'producto' => $d->product?->name,
                'cantidad' => $d->quantity,
                'precio_unitario' => (float) $d->unit_price,
                'subtotal' => (float) $d->subtotal,
            ])),
            'subtotal' => (float) $this->subtotal,
            'iva' => (float) $this->tax,
            'total' => (float) $this->total,
            'pdf' => route('sales.pdf', $this->id),
        ];
    }
}
