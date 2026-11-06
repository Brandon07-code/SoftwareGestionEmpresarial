<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable;

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

    public function codigoOtps(): HasMany
    {
        return $this->hasMany(CodigoOtp::class);
    }

    public function isAdmin(): bool
    {
        return $this->rol && in_array($this->rol->slug, ['superadmin', 'administrador', 'gerente']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'telefono', 'activo'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('seguridad')
            ->setDescriptionForEvent(fn (string $eventName) => "Usuario «{$this->name}» fue {$eventName}");
    }

    /**
     * Alias de compatibilidad para atributo active.
     */
    public function getActiveAttribute(): bool
    {
        return (bool) ($this->attributes['activo'] ?? true);
    }

    /**
     * Rol con el que el usuario está trabajando en esta sesión.
     * - Con un solo rol: ese rol.
     * - Con varios roles: el que eligió al iniciar sesión (session('active_role')).
     * - Desde la API (Clase 13): el rol con el que se emitió el token (ability «rol:nombre»).
     * - Sin roles o sin elección (consola, pruebas): null.
     */
    public function activeRole(): ?Role
    {
        $roles = $this->roles;

        if ($roles->count() === 1) {
            return $roles->first();
        }

        $token = $this->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $ability = collect($token->abilities)->first(fn ($a) => str_starts_with($a, 'rol:'));

            return $ability ? $roles->firstWhere('name', substr($ability, 4)) : null;
        }

        return $roles->firstWhere('name', session('active_role'));
    }

    /**
     * ¿El usuario debe elegir con qué rol trabajar?
     */
    public function hasMultipleRoles(): bool
    {
        return $this->roles->count() > 1;
    }

    /**
     * Verificación central de permisos del ERP (la usa Gate::before en AppServiceProvider):
     *   permiso efectivo = permisos DIRECTOS del usuario + permisos del ROL ACTIVO.
     */
    public function hasPermissionInActiveRole(string $permission): ?bool
    {
        try {
            if ($this->hasDirectPermission($permission)) {
                return true;
            }

            $role = $this->activeRole();

            return $role
                ? $role->hasPermissionTo($permission)
                : $this->hasPermissionTo($permission);
        } catch (PermissionDoesNotExist) {
            return null;
        }
    }
}
