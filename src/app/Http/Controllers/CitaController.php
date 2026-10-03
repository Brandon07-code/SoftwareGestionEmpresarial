<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Tercero;
use App\Models\Servicio;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\TransaccionPago;
use App\Models\PuntosMovimiento;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Muestra el listado de citas usando Eager Loading (with) para evitar el problema N+1
     */
    public function index()
    {
        // Uso de with() para optimizar las consultas a la base de datos (Eager Loading anti N+1)
        $citas = Cita::with(['cliente', 'especialista', 'servicio', 'transaccionPago'])
            ->orderBy('fecha_hora', 'desc')
            ->paginate(10);

        // Métricas rápidas para el dashboard
        $totalCitas = Cita::count();
        $citasHoy = Cita::whereDate('fecha_hora', today())->count();
        $citasPendientes = Cita::whereIn('estado', ['PROGRAMADA', 'EN_ATENCION'])->count();
        $ingresosTotales = Cita::where('estado', 'COMPLETADA')->sum('total');

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
        // El ERP consulta Terceros filtrados por roles de negocio
        $clientes = Tercero::clientes()->activos()->orderBy('nombre_completo')->get();
        $especialistas = Tercero::empleados()->activos()->orderBy('nombre_completo')->get();
        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('citas.create', compact('clientes', 'especialistas', 'servicios'));
    }

    /**
     * Almacena una nueva cita en la base de datos con transacción de pago y fidelización CRM
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tercero_cliente_id' => 'required|exists:terceros,id',
            'tercero_especialista_id' => 'required|exists:terceros,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:PROGRAMADA,EN_ATENCION,COMPLETADA,CANCELADA',
            'pasarela' => 'required|in:WOMPI,NEQUI,EFECTIVO,TRANSFERENCIA',
            'notas' => 'nullable|string|max:500',
        ], [
            'tercero_cliente_id.required' => 'Debe seleccionar un cliente (Tercero).',
            'tercero_especialista_id.required' => 'Debe asignar un especialista/empleado.',
            'servicio_id.required' => 'Debe seleccionar un servicio del catálogo.',
            'fecha_hora.required' => 'La fecha y hora son obligatorias.',
        ]);

        $empresa = Empresa::first() ?? Empresa::create([
            'nit' => '901.884.231-1',
            'razon_social' => 'Empresa Matriz ERP S.A.S.',
            'nombre_comercial' => 'Matriz ERP',
        ]);

        $sucursal = Sucursal::where('empresa_id', $empresa->id)->first() ?? Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sede Principal',
        ]);

        $servicio = Servicio::findOrFail($validated['servicio_id']);
        
        $cita = Cita::create([
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
            'tercero_cliente_id' => $validated['tercero_cliente_id'],
            'tercero_especialista_id' => $validated['tercero_especialista_id'],
            'servicio_id' => $validated['servicio_id'],
            'fecha_hora' => $validated['fecha_hora'],
            'total' => $servicio->precio_venta,
            'estado' => $validated['estado'],
            'notas' => $validated['notas'] ?? null,
        ]);

        // Crear la transacción de pago con pasarela
        $pasarela = $validated['pasarela'];
        $estadoPago = ($validated['estado'] === 'COMPLETADA' || $pasarela === 'EFECTIVO') ? 'APPROVED' : 'PENDING';
        
        TransaccionPago::create([
            'empresa_id' => $empresa->id,
            'cita_id' => $cita->id,
            'pasarela' => $pasarela,
            'referencia_interna' => strtoupper($pasarela) . '-TRX-' . strtoupper(bin2hex(random_bytes(4))),
            'monto' => $servicio->precio_venta,
            'moneda' => 'COP',
            'estado' => $estadoPago,
            'metodo_pago_detalle' => $pasarela === 'EFECTIVO' ? 'Efectivo en mostrador' : ($pasarela === 'NEQUI' ? 'Nequi Dinámico' : 'Wompi Checkout'),
            'pagado_at' => $estadoPago === 'APPROVED' ? now() : null,
        ]);

        // Motor de Fidelización CRM: Calcular y sumar puntos si fue completada
        if ($validated['estado'] === 'COMPLETADA') {
            $cliente = Tercero::find($validated['tercero_cliente_id']);
            if ($cliente) {
                $puntosGanados = intdiv($servicio->precio_venta, $empresa->puntos_por_monto ?? 10000);
                if ($puntosGanados > 0) {
                    $saldoAnterior = $cliente->puntos_fidelidad;
                    $cliente->increment('puntos_fidelidad', $puntosGanados);
                    
                    PuntosMovimiento::create([
                        'empresa_id' => $empresa->id,
                        'tercero_id' => $cliente->id,
                        'tipo' => 'ACUMULACION',
                        'puntos' => $puntosGanados,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $saldoAnterior + $puntosGanados,
                        'motivo' => 'Servicio ' . $servicio->nombre . ' (Turno #' . $cita->id . ')',
                    ]);
                }
            }
        }

        return redirect()->route('citas.index')
            ->with('success', '¡Turno/Cita #' . $cita->id . ' agendado exitosamente con pasarela ' . $pasarela . '!');
    }

    /**
     * Muestra el detalle de una cita específica con liquidación en caja y pasarela
     */
    public function show(Cita $cita)
    {
        $cita->load(['cliente', 'especialista', 'servicio', 'transaccionPago']);
        return view('citas.show', compact('cita'));
    }

    /**
     * Muestra el formulario para editar una cita existente
     */
    public function edit(Cita $cita)
    {
        $cita->load(['cliente', 'especialista', 'servicio', 'transaccionPago']);
        $clientes = Tercero::clientes()->activos()->orderBy('nombre_completo')->get();
        $especialistas = Tercero::empleados()->activos()->orderBy('nombre_completo')->get();
        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('citas.edit', compact('cita', 'clientes', 'especialistas', 'servicios'));
    }

    /**
     * Actualiza una cita existente
     */
    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'tercero_cliente_id' => 'required|exists:terceros,id',
            'tercero_especialista_id' => 'required|exists:terceros,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:PROGRAMADA,EN_ATENCION,COMPLETADA,CANCELADA',
            'pasarela' => 'required|in:WOMPI,NEQUI,EFECTIVO,TRANSFERENCIA',
            'notas' => 'nullable|string|max:500',
        ]);

        $servicio = Servicio::findOrFail($validated['servicio_id']);
        $estadoAnterior = $cita->estado;

        $cita->update([
            'tercero_cliente_id' => $validated['tercero_cliente_id'],
            'tercero_especialista_id' => $validated['tercero_especialista_id'],
            'servicio_id' => $validated['servicio_id'],
            'fecha_hora' => $validated['fecha_hora'],
            'total' => $servicio->precio_venta,
            'estado' => $validated['estado'],
            'notas' => $validated['notas'] ?? null,
        ]);

        // Actualizar transacción de pago
        if ($cita->transaccionPago) {
            $nuevoEstadoPago = ($validated['estado'] === 'COMPLETADA' || $validated['pasarela'] === 'EFECTIVO') ? 'APPROVED' : $cita->transaccionPago->estado;
            $cita->transaccionPago->update([
                'pasarela' => $validated['pasarela'],
                'monto' => $servicio->precio_venta,
                'estado' => $nuevoEstadoPago,
                'pagado_at' => $nuevoEstadoPago === 'APPROVED' ? now() : null,
            ]);
        }

        // Si cambió a COMPLETADA, liquidar puntos CRM
        if ($estadoAnterior !== 'COMPLETADA' && $validated['estado'] === 'COMPLETADA') {
            $cliente = Tercero::find($validated['tercero_cliente_id']);
            if ($cliente) {
                $puntosGanados = intdiv($servicio->precio_venta, 10000);
                if ($puntosGanados > 0) {
                    $saldoAnterior = $cliente->puntos_fidelidad;
                    $cliente->increment('puntos_fidelidad', $puntosGanados);
                    PuntosMovimiento::create([
                        'empresa_id' => $cita->empresa_id,
                        'tercero_id' => $cliente->id,
                        'tipo' => 'ACUMULACION',
                        'puntos' => $puntosGanados,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $saldoAnterior + $puntosGanados,
                        'motivo' => 'Liquidación Turno #' . $cita->id,
                    ]);
                }
            }
        }

        return redirect()->route('citas.index')
            ->with('success', '¡Turno/Cita #' . $cita->id . ' actualizado correctamente!');
    }

    /**
     * Elimina una cita de la base de datos
     */
    public function destroy(Cita $cita)
    {
        $id = $cita->id;
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita #' . $id . ' eliminada correctamente del sistema.');
    }
}
