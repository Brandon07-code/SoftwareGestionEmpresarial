<?php

namespace App\Http\Controllers;

use App\Models\Tercero;
use App\Models\Empresa;
use Illuminate\Http\Request;

class TerceroController extends Controller
{
    /**
     * Muestra el directorio unificado de Terceros (Party Model)
     */
    public function index(Request $request)
    {
        $categoria = $request->query('filtro', 'todos'); // todos, clientes, proveedores, empleados

        $query = Tercero::with('empresa')->orderBy('nombre_completo');

        if ($categoria === 'clientes') {
            $query->where('es_cliente', true);
        } elseif ($categoria === 'proveedores') {
            $query->where('es_proveedor', true);
        } elseif ($categoria === 'empleados') {
            $query->where('es_empleado', true);
        }

        $terceros = $query->paginate(12);

        $conteoTotal = Tercero::count();
        $conteoClientes = Tercero::where('es_cliente', true)->count();
        $conteoProveedores = Tercero::where('es_proveedor', true)->count();
        $conteoEmpleados = Tercero::where('es_empleado', true)->count();

        return view('terceros.index', compact(
            'terceros',
            'categoria',
            'conteoTotal',
            'conteoClientes',
            'conteoProveedores',
            'conteoEmpleados'
        ));
    }

    /**
     * Formulario de creación de Tercero
     */
    public function create()
    {
        $empresas = Empresa::all();
        return view('terceros.create', compact('empresas'));
    }

    /**
     * Almacena un nuevo Tercero clasificado en el ERP
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|in:CC,NIT,CE,TI,Pasaporte',
            'numero_documento' => 'required|string|max:50',
            'nombre_completo' => 'required|string|max:200',
            'telefono' => 'required|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'required|string|max:100',
            'es_cliente' => 'nullable|boolean',
            'es_proveedor' => 'nullable|boolean',
            'es_empleado' => 'nullable|boolean',
            'cargo' => 'nullable|string|max:100',
            'porcentaje_comision' => 'nullable|numeric|min:0|max:100',
            'limite_credito' => 'nullable|numeric|min:0',
        ]);

        $empresa = Empresa::first();

        Tercero::create([
            'empresa_id' => $empresa->id,
            'tipo_documento' => $validated['tipo_documento'],
            'numero_documento' => $validated['numero_documento'],
            'nombre_completo' => $validated['nombre_completo'],
            'telefono' => $validated['telefono'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'email' => $validated['email'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'ciudad' => $validated['ciudad'],
            'es_cliente' => $request->has('es_cliente'),
            'es_proveedor' => $request->has('es_proveedor'),
            'es_empleado' => $request->has('es_empleado'),
            'cargo' => $validated['cargo'] ?? null,
            'porcentaje_comision' => $validated['porcentaje_comision'] ?? 0.00,
            'limite_credito' => $validated['limite_credito'] ?? 0.00,
            'activo' => true,
        ]);

        return redirect()->route('terceros.index')
            ->with('success', '¡Tercero registrado exitosamente bajo el Party Model empresarial!');
    }

    /**
     * Muestra el perfil 360 del Tercero con su historial CRM y puntos
     */
    public function show(Tercero $tercero)
    {
        $tercero->load(['puntosMovimientos', 'crmInteracciones', 'citasComoCliente.servicio', 'citasComoEspecialista.servicio']);
        return view('terceros.show', compact('tercero'));
    }
}
