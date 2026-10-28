<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'product_id',
        'user_id',
        'movement_type',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_price',
        'document_reference',
        'description',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isEntry(): bool
    {
        return in_array($this->movement_type, ['ENTRADA_COMPRA', 'ANULACION_VENTA', 'AJUSTE_INICIAL', 'AJUSTE_POSITIVO']);
    }

    public function movementTypeLabel(): string
    {
        return match ($this->movement_type) {
            'ENTRADA_COMPRA' => 'Entrada por Compra',
            'SALIDA_VENTA' => 'Salida por Venta',
            'ANULACION_VENTA' => 'Devolución Anulación',
            'AJUSTE_INICIAL' => 'Inventario Inicial',
            'AJUSTE_STOCK' => 'Ajuste Manual',
            default => $this->movement_type,
        };
    }
}
