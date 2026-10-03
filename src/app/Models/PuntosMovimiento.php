<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuntosMovimiento extends Model
{
    use HasFactory;

    public $timestamps = false; // Solo maneja created_at

    protected $table = 'puntos_movimientos';

    protected $fillable = [
        'empresa_id',
        'tercero_id',
        'venta_id',
        'tipo',
        'puntos',
        'saldo_anterior',
        'saldo_nuevo',
        'motivo',
        'created_at',
    ];

    protected $casts = [
        'puntos' => 'integer',
        'saldo_anterior' => 'integer',
        'saldo_nuevo' => 'integer',
        'created_at' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tercero(): BelongsTo
    {
        return $this->belongsTo(Tercero::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}
