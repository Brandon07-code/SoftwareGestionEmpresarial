<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmInteraccion extends Model
{
    use HasFactory;

    protected $table = 'crm_interacciones';

    protected $fillable = [
        'empresa_id',
        'tercero_id',
        'user_id',
        'canal',
        'tipo',
        'nota',
        'fecha_contacto',
        'proximo_seguimiento',
    ];

    protected $casts = [
        'fecha_contacto' => 'datetime',
        'proximo_seguimiento' => 'date',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tercero(): BelongsTo
    {
        return $this->belongsTo(Tercero::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
