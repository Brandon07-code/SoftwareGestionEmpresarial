<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'empresa_id',
        'role_id',
        'name',
        'email',
        'password',
        'telefono',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'role_id');
    }

    public function tercero(): HasOne
    {
        return $this->hasOne(Tercero::class);
    }

    public function codigoOtps(): HasMany
    {
        return $this->hasMany(CodigoOtp::class);
    }

    public function isAdmin(): bool
    {
        return $this->rol && in_array($this->rol->slug, ['superadmin', 'administrador', 'gerente']);
    }
}
