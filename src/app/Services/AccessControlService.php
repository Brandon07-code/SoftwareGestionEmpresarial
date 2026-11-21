<?php

namespace App\Services;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlService
{
    public function __construct(
        protected PermissionRegistrar $registrar
    ) {}

    public function saveUser(User $user, array $data, User $actor): User
    {
        $roles = array_values(array_unique($data['roles'] ?? []));
        $directPermissions = array_values(array_unique($data['permissions'] ?? []));
        $activo = (bool) ($data['activo'] ?? true);

        if ($user->exists && $user->is($actor) && ! $activo) {
            throw ValidationException::withMessages([
                'activo' => 'No puede desactivar su propio usuario.',
            ]);
        }

        if ($user->exists && $user->is($actor) && $user->hasRole('admin') && ! in_array('admin', $roles, true)) {
            throw ValidationException::withMessages([
                'roles' => 'No puede quitarse el rol «admin» a sí mismo.',
            ]);
        }

        return DB::transaction(function () use ($user, $data, $roles, $directPermissions, $activo, $actor): User {
            if ($user->exists) {
                $user = User::lockForUpdate()->findOrFail($user->id);
                $this->ensureAnAdminRemains($user, $roles, $activo);
            }

            $before = $user->exists ? [
                'roles' => $user->getRoleNames()->sort()->values()->all(),
                'permisos_directos' => $user->getDirectPermissions()->pluck('name')->sort()->values()->all(),
            ] : [
                'roles' => [],
                'permisos_directos' => [],
            ];

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? null,
                'empresa_id' => $data['empresa_id'] ?? null,
                'activo' => $activo,
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            if (! $user->exists) {
                $payload['email_verified_at'] = now();
            }

            $user->fill($payload);
            $user->save();

            $user->syncRoles($roles);
            $user->syncPermissions($directPermissions);

            $this->registrar->forgetCachedPermissions();

            $after = [
                'roles' => $user->fresh()->getRoleNames()->sort()->values()->all(),
                'permisos_directos' => $user->fresh()->getDirectPermissions()->pluck('name')->sort()->values()->all(),
            ];

            if ($before !== $after) {
                activity('seguridad')
                    ->performedOn($user)
                    ->causedBy($actor)
                    ->event('updated')
                    ->withChanges([
                        'old' => $before,
                        'attributes' => $after,
                    ])
                    ->log("Roles y permisos del usuario «{$user->name}» actualizados");
            }

            return $user;
        });
    }

    public function toggleActive(User $user, User $actor): User
    {
        if ($user->is($actor)) {
            throw ValidationException::withMessages([
                'activo' => 'No puede desactivar su propio usuario.',
            ]);
        }

        return DB::transaction(function () use ($user): User {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);
            $targetActivo = ! $lockedUser->activo;

            if (! $targetActivo) {
                $this->ensureAnAdminRemains($lockedUser, $lockedUser->getRoleNames()->all(), false);
            }

            $lockedUser->update(['activo' => $targetActivo]);

            return $lockedUser->fresh();
        });
    }

    public function syncRolePermissions(Role $role, array $permissions, User $actor): void
    {
        $before = $role->permissions()->pluck('name')->sort()->values()->all();

        if ($role->name === 'admin') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions(array_values(array_unique($permissions)));
        }

        $this->registrar->forgetCachedPermissions();

        $after = $role->fresh()->permissions()->pluck('name')->sort()->values()->all();

        if ($before !== $after) {
            activity('seguridad')
                ->performedOn($role)
                ->causedBy($actor)
                ->event('updated')
                ->withChanges([
                    'old' => ['permisos' => $before],
                    'attributes' => ['permisos' => $after],
                ])
                ->log("Permisos del rol «{$role->name}» actualizados");
        }
    }

    public function createRole(string $name, User $actor): Role
    {
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        $this->registrar->forgetCachedPermissions();

        activity('seguridad')
            ->performedOn($role)
            ->causedBy($actor)
            ->event('created')
            ->withChanges([
                'attributes' => ['name' => $role->name],
            ])
            ->log("Rol «{$role->name}» creado");

        return $role;
    }

    public function deleteRole(Role $role, User $actor): void
    {
        if ($role->name === 'admin') {
            throw ValidationException::withMessages([
                'role' => 'El rol protegido «admin» no puede ser eliminado.',
            ]);
        }

        $assignedUsers = $role->users()->count();
        if ($assignedUsers > 0) {
            throw ValidationException::withMessages([
                'role' => "No se puede eliminar el rol «{$role->name}» porque tiene {$assignedUsers} usuario(s) asignado(s).",
            ]);
        }

        $roleName = $role->name;
        $role->delete();

        $this->registrar->forgetCachedPermissions();

        activity('seguridad')
            ->causedBy($actor)
            ->event('deleted')
            ->withProperties(['role' => $roleName])
            ->log("Rol «{$roleName}» eliminado");
    }

    public function createPermission(string $name, User $actor): Permission
    {
        $permission = Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        $this->registrar->forgetCachedPermissions();

        activity('seguridad')
            ->performedOn($permission)
            ->causedBy($actor)
            ->event('created')
            ->withChanges([
                'attributes' => ['name' => $permission->name],
            ])
            ->log("Permiso «{$permission->name}» creado y asignado a admin");

        return $permission;
    }

    public function deletePermission(Permission $permission, User $actor): void
    {
        if (in_array($permission->name, RolePermissionSeeder::CORE_PERMISSIONS, true)) {
            throw ValidationException::withMessages([
                'permission' => "El permiso del sistema «{$permission->name}» no puede ser eliminado.",
            ]);
        }

        $nonAdminRoles = $permission->roles()->where('name', '!=', 'admin')->pluck('name')->all();
        $directUsersCount = $permission->users()->count();

        if (! empty($nonAdminRoles) || $directUsersCount > 0) {
            $reasons = [];
            if (! empty($nonAdminRoles)) {
                $reasons[] = 'roles: '.implode(', ', $nonAdminRoles);
            }
            if ($directUsersCount > 0) {
                $reasons[] = "{$directUsersCount} usuario(s) con permiso directo";
            }

            throw ValidationException::withMessages([
                'permission' => "No se puede eliminar el permiso «{$permission->name}» porque está en uso (".implode(' y ', $reasons).').',
            ]);
        }

        $permissionName = $permission->name;
        $permission->roles()->detach();
        $permission->delete();

        $this->registrar->forgetCachedPermissions();

        activity('seguridad')
            ->causedBy($actor)
            ->event('deleted')
            ->withProperties(['permission' => $permissionName])
            ->log("Permiso «{$permissionName}» eliminado");
    }

    protected function ensureAnAdminRemains(User $user, array $targetRoles, bool $targetActivo): void
    {
        $wasActiveAdmin = $user->hasRole('admin') && $user->activo;
        $willRemainActiveAdmin = in_array('admin', $targetRoles, true) && $targetActivo;

        if ($wasActiveAdmin && ! $willRemainActiveAdmin) {
            $otherActiveAdmins = User::role('admin')
                ->where('activo', true)
                ->whereKeyNot($user->id)
                ->lockForUpdate()
                ->count();

            if ($otherActiveAdmins === 0) {
                throw ValidationException::withMessages([
                    'roles' => 'Debe permanecer al menos un usuario «admin» activo en el sistema.',
                ]);
            }
        }
    }
}
