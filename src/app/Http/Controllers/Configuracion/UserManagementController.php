<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuracion\StoreUserRequest;
use App\Http\Requests\Configuracion\UpdateUserRequest;
use App\Models\Empresa;
use App\Models\User;
use App\Services\AccessControlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar-usuarios'),
        ];
    }

    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['roles', 'permissions', 'empresa'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('telefono', 'like', $search);
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->whereHas('roles', fn ($q) => $q->where('name', $request->string('role')));
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                $query->where('activo', $request->input('estado') === 'activo');
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('configuracion.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('configuracion.users.create', [
            'user' => new User(['activo' => true]),
            'roles' => Role::withCount('permissions')->orderBy('name')->get(),
            'permissionGroups' => $this->groupedPermissions(),
            'empresas' => Empresa::where('activo', true)->orderBy('nombre_comercial')->get(),
        ]);
    }

    public function store(StoreUserRequest $request, AccessControlService $service): RedirectResponse
    {
        try {
            $user = $service->saveUser(new User, $request->validated(), $request->user());

            return redirect()->route('configuracion.users.index')
                ->with('success', "Usuario «{$user->name}» creado y configurado correctamente.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(User $user): View
    {
        $user->load(['roles', 'permissions']);

        return view('configuracion.users.edit', [
            'user' => $user,
            'roles' => Role::withCount('permissions')->orderBy('name')->get(),
            'permissionGroups' => $this->groupedPermissions(),
            'empresas' => Empresa::where('activo', true)->orderBy('nombre_comercial')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, AccessControlService $service): RedirectResponse
    {
        try {
            $service->saveUser($user, $request->validated(), $request->user());

            return redirect()->route('configuracion.users.index')
                ->with('success', "Usuario «{$user->name}» actualizado correctamente.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function toggle(Request $request, User $user, AccessControlService $service): RedirectResponse
    {
        try {
            $updated = $service->toggleActive($user, $request->user());
            $estadoLabel = $updated->activo ? 'activado' : 'desactivado';

            return redirect()->route('configuracion.users.index')
                ->with('success', "Usuario «{$updated->name}» {$estadoLabel} correctamente.");
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->flatten()->first());
        }
    }

    private function groupedPermissions(): Collection
    {
        return Permission::orderBy('name')->get()->groupBy(function (Permission $permission): string {
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
