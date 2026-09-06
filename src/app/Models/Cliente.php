<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'puntos_fidelizacion',
        'notas',
    ];

    /**
     * Relación: Un cliente tiene muchas citas
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
