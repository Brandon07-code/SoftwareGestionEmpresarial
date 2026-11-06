<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Define qué datos de un producto salen por la API y con qué nombres.
 * Evita exponer columnas internas de infraestructura (deleted_at, etc.) y mantiene un contrato estable.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'descripcion' => $this->description,
            'precio' => (float) $this->price,
            'stock' => $this->stock,
            'disponible' => (bool) $this->active && $this->stock > 0,
            'categoria' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'nombre' => $this->category?->name,
            ]),
        ];
    }
}
