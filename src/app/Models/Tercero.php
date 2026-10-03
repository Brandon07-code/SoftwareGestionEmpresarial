<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Tercero extends Model
{
    use HasFactory;

    protected $table = 'terceros';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'tipo_documento',
        'numero_documento',
        'nombre_completo',
        'primer_nombre',
        'primer_apellido',
        'telefono',
        'whatsapp',
        'email',
        'direccion',
        'ciudad',
        'es_cliente',
        'es_proveedor',
        'es_empleado',
        'limite_credito',
        'dias_credito',
        'puntos_fidelidad',
        'cargo',
        'porcentaje_comision',
        'activo',
    ];

    protected $casts = [
        'es_cliente' => 'boolean',
        'es_proveedor' => 'boolean',
        'es_empleado' => 'boolean',
        'activo' => 'boolean',
        'limite_credito' => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'puntos_fidelidad' => 'integer',
        'dias_credito' => 'integer',
    ];

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ventasComoCliente(): HasMany
    {
        return $this->hasMany(Venta::class, 'tercero_cliente_id');
    }

    public function citasComoCliente(): HasMany
    {
        return $this->hasMany(Cita::class, 'tercero_cliente_id');
    }

    public function citasComoEspecialista(): HasMany
    {
        return $this->hasMany(Cita::class, 'tercero_especialista_id');
    }

    public function puntosMovimientos(): HasMany
    {
        return $this->hasMany(PuntosMovimiento::class, 'tercero_id')->orderBy('created_at', 'desc');
    }

    public function crmInteracciones(): HasMany
    {
        return $this->hasMany(CrmInteraccion::class, 'tercero_id')->orderBy('fecha_contacto', 'desc');
    }

    // Scopes de Negocio
    public function scopeClientes(Builder $query): Builder
    {
        return $query->where('es_cliente', true);
    }

    public function scopeProveedores(Builder $query): Builder
    {
        return $query->where('es_proveedor', true);
    }

    public function scopeEmpleados(Builder $query): Builder
    {
        return $query->where('es_empleado', true);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    // Helpers
    public function esCliente(): bool
    {
        return (bool) $this->es_cliente;
    }

    public function esProveedor(): bool
    {
        return (bool) $this->es_proveedor;
    }

    public function esEmpleado(): bool
    {
        return (bool) $this->es_empleado;
    }
}
