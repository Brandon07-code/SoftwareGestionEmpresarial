<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesionCaja extends Model
{
    use HasFactory;

    protected $table = 'sesiones_caja';

    protected $fillable = [
        'caja_id',
        'user_id',
        'monto_apertura',
        'monto_cierre_efectivo',
        'total_ventas_efectivo',
        'total_ventas_digitales',
        'diferencia',
        'estado',
        'observaciones',
        'abierto_at',
        'cerrado_at',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'monto_cierre_efectivo' => 'decimal:2',
        'total_ventas_efectivo' => 'decimal:2',
        'total_ventas_digitales' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'abierto_at' => 'datetime',
        'cerrado_at' => 'datetime',
    ];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}
