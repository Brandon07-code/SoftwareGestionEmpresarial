<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class EmpresaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if ($request->user()?->activeRole()?->name !== 'admin') {
                    abort(403, 'Solo el rol activo «admin» puede gestionar empresas.');
                }

                return $next($request);
            },
        ];
    }

    public function index()
    {
        $empresas = Empresa::withCount(['sucursales', 'cajas'])->orderBy('id')->get();

        return view('empresas.index', [
            'empresas' => $empresas,
        ]);
    }

    public function create()
    {
        return view('empresas.create', [
            'empresa' => new Empresa(['activo' => true, 'moneda' => 'COP', 'tipo_negocio' => 'mixto']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nit' => ['required', 'string', 'max:50', 'unique:empresas,nit'],
            'razon_social' => ['required', 'string', 'max:255'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'tipo_negocio' => ['required', 'string', 'in:servicios,retail,mixto,mayorista'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'moneda' => ['required', 'string', 'max:10'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'nit.required' => 'El NIT o documento tributario es obligatorio.',
            'nit.unique' => 'Ya existe una empresa registrada con este NIT.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $empresa = Empresa::create($validated);

        return redirect()->route('empresas.index')
            ->with('success', "Empresa «{$empresa->nombre_comercial}» registrada exitosamente.");
    }

    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', [
            'empresa' => $empresa,
        ]);
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nit' => ['required', 'string', 'max:50', 'unique:empresas,nit,'.$empresa->id],
            'razon_social' => ['required', 'string', 'max:255'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'tipo_negocio' => ['required', 'string', 'in:servicios,retail,mixto,mayorista'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'moneda' => ['required', 'string', 'max:10'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'nit.required' => 'El NIT es obligatorio.',
            'nit.unique' => 'Ya existe otra empresa con este NIT.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $empresa->update($validated);

        return redirect()->route('empresas.index')
            ->with('success', "Perfil de la empresa «{$empresa->nombre_comercial}» actualizado correctamente.");
    }
}
