<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Muestra el listado de citas usando Eager Loading (with) para evitar el problema N+1
     */
    public function index()
    {
        // Uso de with() para optimizar las consultas a la base de datos (evita problema N+1)
        $citas = Cita::with(['cliente', 'servicio'])
            ->orderBy('fecha_hora', 'desc')
            ->paginate(10);

        // Métricas rápidas para el dashboard
        $totalCitas = Cita::count();
        $citasHoy = Cita::whereDate('fecha_hora', today())->count();
        $citasPendientes = Cita::whereIn('estado', ['pendiente', 'confirmada'])->count();
        $ingresosTotales = Cita::where('estado', 'completada')->sum('total');

        return view('citas.index', compact(
            'citas',
            'totalCitas',
            'citasHoy',
            'citasPendientes',
            'ingresosTotales'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva cita
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();
        $estilistas = ['Brayan Henao', 'Valentina Ríos', 'Sebastián Morales', 'Camila Ortiz', 'Mateo Gómez'];

        return view('citas.create', compact('clientes', 'servicios', 'estilistas'));
    }

    /**
     * Almacena una nueva cita en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'estilista' => 'required|string|max:100',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:pendiente,confirmada,en_atencion,completada,cancelada',
            'metodo_pago' => 'required|in:efectivo,transferencia,qr_fachada,pendiente',
            'notas' => 'nullable|string|max:500',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'servicio_id.required' => 'Debe seleccionar un servicio.',
            'estilista.required' => 'Debe ingresar o seleccionar el profesional/estilista.',
            'fecha_hora.required' => 'La fecha y hora son obligatorias.',
        ]);

        $servicio = Servicio::findOrFail($validated['servicio_id']);
        $validated['total'] = $servicio->precio;

        $cita = Cita::create($validated);

        // Sumar puntos de fidelización al cliente
        $cliente = Cliente::find($validated['cliente_id']);
        if ($cliente) {
            $cliente->increment('puntos_fidelizacion', 10);
        }

        return redirect()->route('citas.index')
            ->with('success', '¡Cita agendada exitosamente con código #' . $cita->id . '!');
    }

    /**
     * Muestra el detalle de una cita específica con su QR de pago fachada
     */
    public function show(Cita $cita)
    {
        $cita->load(['cliente', 'servicio']);
        return view('citas.show', compact('cita'));
    }

    /**
     * Muestra el formulario para editar una cita existente
     */
    public function edit(Cita $cita)
    {
        $cita->load(['cliente', 'servicio']);
        $clientes = Cliente::orderBy('nombre')->get();
        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();
        $estilistas = ['Brayan Henao', 'Valentina Ríos', 'Sebastián Morales', 'Camila Ortiz', 'Mateo Gómez'];

        return view('citas.edit', compact('cita', 'clientes', 'servicios', 'estilistas'));
    }

    /**
     * Actualiza la cita en la base de datos
     */
    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'estilista' => 'required|string|max:100',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:pendiente,confirmada,en_atencion,completada,cancelada',
            'metodo_pago' => 'required|in:efectivo,transferencia,qr_fachada,pendiente',
            'notas' => 'nullable|string|max:500',
        ]);

        $servicio = Servicio::findOrFail($validated['servicio_id']);
        $validated['total'] = $servicio->precio;

        $cita->update($validated);

        return redirect()->route('citas.index')
            ->with('success', '¡Cita #' . $cita->id . ' actualizada correctamente!');
    }

    /**
     * Elimina una cita de la base de datos
     */
    public function destroy(Cita $cita)
    {
        $id = $cita->id;
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita #' . $id . ' eliminada del sistema.');
    }
}
