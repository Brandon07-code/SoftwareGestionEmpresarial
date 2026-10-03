<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionPago extends Model
{
    use HasFactory;

    protected $table = 'transacciones_pago';

    protected $fillable = [
        'empresa_id',
        'venta_id',
        'cita_id',
        'pasarela',
        'referencia_interna',
        'referencia_pasarela',
        'monto',
        'moneda',
        'estado',
        'metodo_pago_detalle',
        'respuesta_payload',
        'url_checkout',
        'pagado_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'respuesta_payload' => 'array',
        'pagado_at' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function isAprobado(): bool
    {
        return $this->estado === 'APPROVED';
    }

    public function isPendiente(): bool
    {
        return $this->estado === 'PENDING';
    }
}
