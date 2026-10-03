<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'tercero_cliente_id',
        'tercero_especialista_id',
        'servicio_id',
        'venta_id',
        'fecha_hora',
        'total',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Tercero::class, 'tercero_cliente_id');
    }

    public function especialista(): BelongsTo
    {
        return $this->belongsTo(Tercero::class, 'tercero_especialista_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function transaccionPago(): HasOne
    {
        return $this->hasOne(TransaccionPago::class, 'cita_id');
    }

    // Scopes de Negocio Reutilizables
    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('fecha_hora', today());
    }

    public function scopeProgramadas(Builder $query): Builder
    {
        return $query->where('estado', 'PROGRAMADA');
    }

    public function scopeEnAtencion(Builder $query): Builder
    {
        return $query->where('estado', 'EN_ATENCION');
    }

    public function scopeCompletadas(Builder $query): Builder
    {
        return $query->where('estado', 'COMPLETADA');
    }
}
