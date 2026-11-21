<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleSelectionController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasMultipleRoles()) {
            return redirect()->route('dashboard');
        }

        $roles = $user->roles()->with('permissions')->orderBy('name')->get();

        return view('auth.select-role', [
            'roles' => $roles,
            'currentRole' => $user->activeRole()?->name,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in($user->getRoleNames()->all())],
        ], [
            'role.required' => 'Debe seleccionar un rol activo para continuar.',
            'role.in' => 'El rol seleccionado no pertenece a su usuario.',
        ]);

        session(['active_role' => $validated['role']]);

        activity('seguridad')
            ->causedBy($user)
            ->withProperties([
                'rol_activo' => $validated['role'],
                'ip' => $request->ip(),
            ])
            ->log('Selección de rol activo de sesión');

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('success', "Sesión iniciada con el rol activo «{$validated['role']}».");
    }

    public function switch(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in($user->getRoleNames()->all())],
        ], [
            'role.required' => 'Debe indicar el rol al que desea cambiar.',
            'role.in' => 'No tiene asignado el rol seleccionado.',
        ]);

        $previousRole = session('active_role');
        session(['active_role' => $validated['role']]);

        activity('seguridad')
            ->causedBy($user)
            ->withProperties([
                'rol_anterior' => $previousRole,
                'rol_activo' => $validated['role'],
                'ip' => $request->ip(),
            ])
            ->log('Cambio de rol activo de sesión');

        return redirect()->route('dashboard')
            ->with('success', "Rol activo cambiado a «{$validated['role']}».");
    }
}
