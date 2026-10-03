<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nit',
        'razon_social',
        'nombre_comercial',
        'tipo_negocio',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'moneda',
        'puntos_por_monto',
        'valor_por_punto',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'puntos_por_monto' => 'integer',
        'valor_por_punto' => 'integer',
    ];

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }

    public function terceros(): HasMany
    {
        return $this->hasMany(Tercero::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class);
    }
}
