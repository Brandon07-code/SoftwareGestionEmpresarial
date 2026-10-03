<?php

namespace App\Models;

/**
 * Clase de compatibilidad / Alias para Tercero (Categoría Cliente).
 * En el modelo de ERP, los clientes son Terceros con es_cliente = true.
 */
class Cliente extends Tercero
{
    protected $table = 'terceros';

    protected static function booted()
    {
        static::addGlobalScope('solo_clientes', function ($builder) {
            $builder->where('es_cliente', true);
        });
    }
}
