<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'codigo_barras',
        'nombre',
        'descripcion',
        'precio_costo',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'maneja_inventario',
        'activo',
    ];

    protected $casts = [
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'maneja_inventario' => 'boolean',
        'activo' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class, 'producto_id');
    }

    public function getMargenGananciaAttribute(): float
    {
        if ($this->precio_costo <= 0) return 100.0;
        return round((($this->precio_venta - $this->precio_costo) / $this->precio_costo) * 100, 2);
    }
}
