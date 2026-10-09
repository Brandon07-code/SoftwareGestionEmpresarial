@extends('layouts.app')

@section('title', 'Pasarelas de Pago & Recaudos - ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado del Módulo -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Pasarelas de Pago & Conciliación</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">WOMPI & Nequi</span>
            </div>
            <p class="text-sm text-slate-400 mt-0.5 font-medium">
                Monitoreo centralizado de transacciones electrónicas, estados de pago (PENDING / APPROVED / DECLINED) y webhooks.
            </p>
        </div>
    </div>

    <!-- Tarjetas de Métricas Financieras de Pasarela (KPIs sin bordes) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Recaudado -->
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Total Recaudado Pasarelas</span>
            <div class="text-2xl font-black text-emerald-400 mt-2">${{ number_format($totalRecaudado, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">{{ $transaccionesAprobadas }} transacciones aprobadas</span>
        </div>

        <!-- Recaudo WOMPI -->
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Volumen WOMPI</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-950/80 text-indigo-300 border border-indigo-800/60">Bancolombia</span>
            </div>
            <div class="text-2xl font-black text-indigo-400 mt-2">${{ number_format($totalWompi, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Tarjetas & Transferencias PSE</span>
        </div>

        <!-- Recaudo NEQUI -->
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Volumen NEQUI</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-pink-950/80 text-pink-300 border border-pink-800/60">QR Dinámico</span>
            </div>
            <div class="text-2xl font-black text-pink-400 mt-2">${{ number_format($totalNequi, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Cobros móviles directos</span>
        </div>

        <!-- Transacciones Pendientes -->
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Transacciones Pendientes</span>
            <div class="text-2xl font-black text-cyan-400 mt-2">{{ $transaccionesPendientes }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">En espera de webhook o confirmación</span>
        </div>
    </div>

    <!-- Listado de Transacciones de Pago (Dark Enterprise) -->
    <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">
        <div class="p-5 border-b border-slate-800/80 bg-slate-950/60 flex items-center justify-between">
            <h2 class="text-base font-black text-white">Registro de Transacciones de Pasarela</h2>
            <span class="text-xs text-slate-400 font-medium">Trazabilidad en tiempo real</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-800/80">
                    <tr>
                        <th class="px-6 py-4">Referencia / Pasarela</th>
                        <th class="px-6 py-4">Concepto / Turno</th>
                        <th class="px-6 py-4">Cliente (Tercero)</th>
                        <th class="px-6 py-4">Monto</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                    @forelse($transacciones as $trx)
                    <tr class="hover:bg-slate-800/50 transition">
                        <!-- Referencia y Pasarela -->
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-indigo-400 text-xs">
                                {{ $trx->referencia_interna }}
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                @if($trx->pasarela === 'WOMPI')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                        💳 WOMPI
                                    </span>
                                @elseif($trx->pasarela === 'NEQUI')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-pink-500/15 text-pink-300 border border-pink-500/30">
                                        📱 NEQUI
                                    </span>
                                @elseif($trx->pasarela === 'EFECTIVO')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        💵 EFECTIVO
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-800 text-slate-300 border border-slate-700/80">
                                        🏦 {{ $trx->pasarela }}
                                    </span>
                                @endif
                                @if($trx->referencia_pasarela)
                                    <span class="text-[10px] text-slate-400 font-mono">Auth: {{ $trx->referencia_pasarela }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Concepto / Turno -->
                        <td class="px-6 py-4">
                            @if($trx->cita)
                                <div class="font-bold text-slate-200">Turno #{{ $trx->cita->id }}</div>
                                <span class="text-slate-400 text-[11px] block">{{ $trx->cita->servicio->nombre ?? 'Servicio' }}</span>
                            @elseif($trx->venta)
                                <div class="font-bold text-slate-200">Factura #{{ $trx->venta->numero_factura }}</div>
                                <span class="text-slate-400 text-[11px] block">Venta Directa</span>
                            @else
                                <span class="text-slate-400">Cobro General</span>
                            @endif
                        </td>

                        <!-- Cliente -->
                        <td class="px-6 py-4">
                            @php
                                $cliente = $trx->cita->cliente ?? $trx->venta->cliente ?? null;
                            @endphp
                            @if($cliente)
                                <div class="font-bold text-white">{{ $cliente->nombre_completo }}</div>
                                <span class="text-slate-400 text-[11px] block font-mono">CC: {{ $cliente->numero_documento }}</span>
                            @else
                                <span class="text-slate-400 font-medium">Consumidor Final</span>
                            @endif
                        </td>

                        <!-- Monto -->
                        <td class="px-6 py-4">
                            <div class="font-black text-white text-sm font-mono">
                                ${{ number_format($trx->monto, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">{{ $trx->moneda }}</span>
                        </td>

                        <!-- Estado -->
                        <td class="px-6 py-4">
                            @if($trx->estado === 'APPROVED')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                    ● APROBADA
                                </span>
                            @elseif($trx->estado === 'PENDING')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                                    ⏳ PENDIENTE
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                    ✕ RECHAZADA
                                </span>
                            @endif
                        </td>

                        <!-- Fecha -->
                        <td class="px-6 py-4 text-slate-400 font-mono text-xs">
                            {{ $trx->created_at ? $trx->created_at->format('d/m/Y h:i A') : 'N/A' }}
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('pagos.checkout', $trx->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700/80 rounded-xl text-xs font-bold transition shadow-sm">
                                <span>Ver Checkout →</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                            No hay transacciones registradas en pasarela.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transacciones->hasPages())
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            {{ $transacciones->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
