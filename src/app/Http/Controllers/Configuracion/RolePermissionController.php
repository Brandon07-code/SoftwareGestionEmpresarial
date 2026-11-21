<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuracion\StorePermissionRequest;
use App\Http\Requests\Configuracion\StoreRoleRequest;
use App\Http\Requests\Configuracion\SyncMatrixRequest;
use App\Services\AccessControlService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar-roles', only: ['index', 'updateMatrix', 'storeRole', 'destroyRole']),
            new Middleware('permission:gestionar-permisos', only: ['storePermission', 'destroyPermission']),
        ];
    }

    public function index(): View
    {
        $roles = Role::with('permissions')->withCount('users')->orderByRaw("CASE WHEN name = 'admin' THEN 0 ELSE 1 END, name ASC")->get();
        $permissionGroups = $this->groupedPermissions();

        return view('configuracion.matrix', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'totalPermissions' => Permission::count(),
            'protectedPermissions' => RolePermissionSeeder::CORE_PERMISSIONS,
        ]);
    }

    public function updateMatrix(SyncMatrixRequest $request, AccessControlService $service): RedirectResponse
    {
        $matrix = $request->validated()['matrix'] ?? [];
        $roles = Role::all();

        foreach ($roles as $role) {
            if ($role->name === 'admin') {
                $service->syncRolePermissions($role, Permission::pluck('name')->all(), $request->user());

                continue;
            }

            $selected = $matrix[$role->id] ?? [];
            $service->syncRolePermissions($role, $selected, $request->user());
        }

        return redirect()->route('configuracion.matrix')
            ->with('success', 'Matriz de Roles × Permisos actualizada correctamente.');
    }

    public function storeRole(StoreRoleRequest $request, AccessControlService $service): RedirectResponse
    {
        $role = $service->createRole($request->validated()['name'], $request->user());

        return redirect()->route('configuracion.matrix')
            ->with('success', "Rol «{$role->name}» creado correctamente.");
    }

    public function destroyRole(Request $request, Role $role, AccessControlService $service): RedirectResponse
    {
        try {
            $name = $role->name;
            $service->deleteRole($role, $request->user());

            return redirect()->route('configuracion.matrix')
                ->with('success', "Rol «{$name}» eliminado correctamente.");
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->flatten()->first());
        }
    }

    public function storePermission(StorePermissionRequest $request, AccessControlService $service): RedirectResponse
    {
        $permission = $service->createPermission($request->validated()['name'], $request->user());

        return redirect()->route('configuracion.matrix')
            ->with('success', "Permiso «{$permission->name}» creado y asignado al rol admin.");
    }

    public function destroyPermission(Request $request, Permission $permission, AccessControlService $service): RedirectResponse
    {
        try {
            $name = $permission->name;
            $service->deletePermission($permission, $request->user());

            return redirect()->route('configuracion.matrix')
                ->with('success', "Permiso «{$name}» eliminado correctamente.");
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->flatten()->first());
        }
    }

    private function groupedPermissions(): Collection
    {
        return Permission::with(['roles:id,name', 'users:id'])
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission): string {
                $parts = explode('-', $permission->name);

                return match (end($parts)) {
                    'productos' => 'Productos y Excel',
                    'categorias' => 'Categorías',
                    'clientes' => 'Clientes',
                    'ventas' => 'Ventas',
                    'proveedores' => 'Proveedores',
                    'compras' => 'Compras',
                    'inventario' => 'Inventario y Kárdex',
                    'reportes', 'auditoria' => 'Reportes y Auditoría',
                    'usuarios', 'roles', 'permisos' => 'Configuración y Seguridad',
                    'citas', 'terceros', 'pagos', 'crm' => 'Servicios JyM y CRM',
                    default => 'Otros Permisos',
                };
            });
    }
}
