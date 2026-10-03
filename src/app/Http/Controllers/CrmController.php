<?php

namespace App\Http\Controllers;

use App\Models\Tercero;
use App\Models\PuntosMovimiento;
use App\Models\CrmInteraccion;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    /**
     * Tablero principal de CRM y Fidelización
     */
    public function index()
    {
        $clientesFrecuentes = Tercero::clientes()
            ->withCount('citasComoCliente')
            ->orderBy('puntos_fidelidad', 'desc')
            ->take(10)
            ->get();

        $movimientosPuntos = PuntosMovimiento::with('tercero')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $interaccionesRecientes = CrmInteraccion::with(['tercero', 'usuario'])
            ->orderBy('fecha_contacto', 'desc')
            ->take(8)
            ->get();

        $totalPuntosEmitidos = PuntosMovimiento::where('tipo', 'ACUMULACION')->sum('puntos');
        $totalPuntosRedimidos = PuntosMovimiento::where('tipo', 'REDENCION')->sum('puntos');
        $totalClientesFidelizados = Tercero::clientes()->where('puntos_fidelidad', '>', 0)->count();

        return view('crm.index', compact(
            'clientesFrecuentes',
            'movimientosPuntos',
            'interaccionesRecientes',
            'totalPuntosEmitidos',
            'totalPuntosRedimidos',
            'totalClientesFidelizados'
        ));
    }

    /**
     * Registrar una nueva interacción con cliente
     */
    public function storeInteraccion(Request $request)
    {
        $validated = $request->validate([
            'tercero_id' => 'required|exists:terceros,id',
            'canal' => 'required|in:WHATSAPP,LLAMADA,PRESENCIAL,EMAIL',
            'tipo' => 'required|in:PREFERENCIA,SEGUIMIENTO,RECLAMO,FELICITACION',
            'nota' => 'required|string|max:1000',
            'proximo_seguimiento' => 'nullable|date',
        ]);

        $empresa = Empresa::first();
        $user = User::first();

        CrmInteraccion::create([
            'empresa_id' => $empresa->id,
            'tercero_id' => $validated['tercero_id'],
            'user_id' => $user->id,
            'canal' => $validated['canal'],
            'tipo' => $validated['tipo'],
            'nota' => $validated['nota'],
            'fecha_contacto' => now(),
            'proximo_seguimiento' => $validated['proximo_seguimiento'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', '¡Interacción CRM registrada en el historial del cliente!');
    }
}
