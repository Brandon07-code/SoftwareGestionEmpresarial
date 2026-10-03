<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'sesion_caja_id',
        'numero_factura',
        'tercero_cliente_id',
        'tercero_vendedor_id',
        'subtotal',
        'descuento_puntos',
        'impuesto_iva',
        'total',
        'puntos_ganados',
        'puntos_canjeados',
        'estado',
        'notas',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento_puntos' => 'decimal:2',
        'impuesto_iva' => 'decimal:2',
        'total' => 'decimal:2',
        'puntos_ganados' => 'integer',
        'puntos_canjeados' => 'integer',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function sesionCaja(): BelongsTo
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Tercero::class, 'tercero_cliente_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(Tercero::class, 'tercero_vendedor_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function transaccionPago(): HasOne
    {
        return $this->hasOne(TransaccionPago::class);
    }

    public function cita(): HasOne
    {
        return $this->hasOne(Cita::class);
    }
}
