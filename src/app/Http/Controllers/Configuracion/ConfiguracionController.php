<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ConfiguracionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar-usuarios|gestionar-roles|gestionar-permisos'),
        ];
    }

    public function __invoke()
    {
        return view('configuracion.index', [
            'usersCount' => User::count(),
            'activeUsersCount' => User::where('activo', true)->count(),
            'inactiveUsersCount' => User::where('activo', false)->count(),
            'rolesCount' => Role::count(),
            'permissionsCount' => Permission::count(),
        ]);
    }
}
