<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'cliente_id',
        'servicio_id',
        'estilista',
        'fecha_hora',
        'estado',
        'total',
        'metodo_pago',
        'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'total' => 'decimal:2',
    ];

    /**
     * Relación: La cita pertenece a un cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relación: La cita pertenece a un servicio
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    /**
     * Scope local: Filtrar citas programadas para hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora', today());
    }

    /**
     * Scope local: Filtrar citas en espera o confirmadas
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada']);
    }

    /**
     * Scope local: Filtrar citas completadas y facturadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
}
