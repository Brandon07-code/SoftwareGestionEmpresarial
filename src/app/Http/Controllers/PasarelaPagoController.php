<?php

namespace App\Http\Controllers;

use App\Models\TransaccionPago;
use App\Models\Cita;
use App\Models\Venta;
use App\Models\PuntosMovimiento;
use Illuminate\Http\Request;

class PasarelaPagoController extends Controller
{
    /**
     * Muestra el monitoreo de transacciones de pasarela (Wompi, Nequi, etc.)
     */
    public function index()
    {
        $transacciones = TransaccionPago::with(['cita.cliente', 'cita.servicio', 'venta.cliente'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $totalRecaudado = TransaccionPago::where('estado', 'APPROVED')->sum('monto');
        $transaccionesAprobadas = TransaccionPago::where('estado', 'APPROVED')->count();
        $transaccionesPendientes = TransaccionPago::where('estado', 'PENDING')->count();
        $totalWompi = TransaccionPago::where('pasarela', 'WOMPI')->where('estado', 'APPROVED')->sum('monto');
        $totalNequi = TransaccionPago::where('pasarela', 'NEQUI')->where('estado', 'APPROVED')->sum('monto');

        return view('pagos.index', compact(
            'transacciones',
            'totalRecaudado',
            'transaccionesAprobadas',
            'transaccionesPendientes',
            'totalWompi',
            'totalNequi'
        ));
    }

    /**
     * Pantalla interactiva de Checkout (Wompi Widget / QR Dinámico Nequi)
     */
    public function checkout($id)
    {
        $transaccion = TransaccionPago::with(['cita.cliente', 'cita.servicio'])->findOrFail($id);
        return view('pagos.checkout', compact('transaccion'));
    }

    /**
     * Simulación o Procesamiento de Webhook oficial de Wompi / Nequi
     */
    public function simularWebhook(Request $request, $id)
    {
        $transaccion = TransaccionPago::findOrFail($id);
        $accion = $request->input('accion', 'APPROVED'); // APPROVED o DECLINED

        if ($accion === 'APPROVED') {
            $transaccion->update([
                'estado' => 'APPROVED',
                'referencia_pasarela' => strtoupper($transaccion->pasarela) . '-AUTH-' . rand(100000, 999999),
                'pagado_at' => now(),
                'respuesta_payload' => [
                    'event' => 'transaction.updated',
                    'data' => [
                        'transaction' => [
                            'id' => $transaccion->referencia_interna,
                            'status' => 'APPROVED',
                            'amount_in_cents' => $transaccion->monto * 100,
                            'reference' => $transaccion->referencia_interna,
                            'currency' => 'COP',
                            'payment_method_type' => $transaccion->pasarela === 'NEQUI' ? 'NEQUI' : 'BANCOLOMBIA_TRANSFER',
                        ]
                    ]
                ]
            ]);

            // Si está vinculada a una cita, marcarla como completada y sumar puntos
            if ($transaccion->cita_id) {
                $cita = Cita::find($transaccion->cita_id);
                if ($cita) {
                    $cita->update(['estado' => 'COMPLETADA']);
                    
                    // Fidelización de puntos
                    $cliente = $cita->cliente;
                    if ($cliente) {
                        $puntos = intdiv($transaccion->monto, 10000);
                        if ($puntos > 0) {
                            $saldoAnt = $cliente->puntos_fidelidad;
                            $cliente->increment('puntos_fidelidad', $puntos);
                            PuntosMovimiento::create([
                                'empresa_id' => $transaccion->empresa_id,
                                'tercero_id' => $cliente->id,
                                'tipo' => 'ACUMULACION',
                                'puntos' => $puntos,
                                'saldo_anterior' => $saldoAnt,
                                'saldo_nuevo' => $saldoAnt + $puntos,
                                'motivo' => 'Pago confirmado por ' . $transaccion->pasarela . ' (Turno #' . $cita->id . ')',
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('pagos.index')
                ->with('success', '¡Transacción ' . $transaccion->referencia_interna . ' aprobada exitosamente por pasarela ' . $transaccion->pasarela . '!');
        } else {
            $transaccion->update([
                'estado' => 'DECLINED',
                'respuesta_payload' => [
                    'event' => 'transaction.updated',
                    'data' => [
                        'status' => 'DECLINED',
                        'error_message' => 'Fondos insuficientes o rechazo por el emisor'
                    ]
                ]
            ]);

            return redirect()->route('pagos.index')
                ->with('error', 'Transacción rechazada por la pasarela de pagos.');
        }
    }
}
