@extends('layouts.app')

@section('title', 'CRM & Fidelización - ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado del Módulo -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">CRM & Fidelización Empresarial</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">Kárdex de Puntos</span>
            </div>
            <p class="text-sm text-slate-400 mt-0.5 font-medium">
                Seguimiento de valor de vida del cliente (CLV), bitácora de interacciones 360° y contabilidad de puntos de lealtad.
            </p>
        </div>
    </div>

    <!-- Tarjetas de Métricas CRM (KPIs sin bordes) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Puntos Emitidos (Acumulación)</span>
            <div class="text-2xl font-black text-cyan-400 mt-2">⭐ {{ number_format($totalPuntosEmitidos) }} pts</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Generados por compras y servicios</span>
        </div>

        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Puntos Redimidos (Premios)</span>
            <div class="text-2xl font-black text-indigo-400 mt-2">🎁 {{ number_format($totalPuntosRedimidos) }} pts</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Beneficios entregados a clientes</span>
        </div>

        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Clientes con Fidelización Activa</span>
            <div class="text-2xl font-black text-emerald-400 mt-2">👥 {{ $totalClientesFidelizados }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Clientes con saldo &gt; 0 puntos</span>
        </div>
    </div>

    <!-- Contenido Principal en 2 Columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna Izquierda (2 cols): Ranking y Kárdex de Puntos -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Ranking de Clientes Más Frecuentes / Leales -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-white">Top Clientes Fidelizados (VIP)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Clientes con mayor acumulación de puntos y concurrencia de turnos.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase font-bold tracking-wider text-[11px] border-b border-slate-800/80">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Cliente (Tercero)</th>
                                <th class="px-4 py-3">Contacto</th>
                                <th class="px-4 py-3 text-center">Turnos</th>
                                <th class="px-4 py-3">Puntos Acumulados</th>
                                <th class="px-4 py-3 rounded-r-xl text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                            @forelse($clientesFrecuentes as $index => $cliente)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-800 text-slate-300 border border-slate-700/80 flex items-center justify-center font-bold text-[10px]">
                                            #{{ $index + 1 }}
                                        </span>
                                        <div>
                                            <div class="font-bold text-white">{{ $cliente->nombre_completo }}</div>
                                            <span class="text-slate-400 text-[10px] font-mono">CC: {{ $cliente->numero_documento }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    📞 {{ $cliente->telefono }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-indigo-400">
                                    {{ $cliente->citas_como_cliente_count }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                        ⭐ {{ $cliente->puntos_fidelidad }} pts
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('terceros.show', $cliente) }}" class="text-cyan-400 hover:text-cyan-300 font-bold transition">
                                        Perfil 360° →
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                    No hay clientes con puntos de fidelización aún.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kárdex Contable de Puntos (Ledger Inmutable) -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-white">Kárdex de Fidelización (Ledger de Puntos)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Auditoría contable: cada punto ganado o gastado tiene trazabilidad inmutable.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase font-bold tracking-wider text-[11px] border-b border-slate-800/80">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Fecha / Hora</th>
                                <th class="px-4 py-3">Tercero (Cliente)</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Puntos</th>
                                <th class="px-4 py-3">Saldo Anterior → Nuevo</th>
                                <th class="px-4 py-3 rounded-r-xl">Motivo / Concepto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                            @forelse($movimientosPuntos as $m)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3 font-medium text-slate-400 font-mono">
                                    {{ $m->created_at ? $m->created_at->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 font-bold text-white">
                                    {{ optional($m->tercero)->nombre_completo ?? 'Cliente Desconocido' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($m->tipo === 'ACUMULACION')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                            + ACUMULACIÓN
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                            - REDENCIÓN
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-black text-white">
                                    {{ $m->puntos }} pts
                                </td>
                                <td class="px-4 py-3 font-mono text-[11px] text-slate-400">
                                    {{ $m->saldo_anterior }} → {{ $m->saldo_nuevo }}
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    {{ $m->motivo }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                    No hay movimientos de puntos registrados en el kárdex.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($movimientosPuntos->hasPages())
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    {{ $movimientosPuntos->links() }}
                </div>
                @endif
            </div>

        </div>

        <!-- Columna Derecha (1 col): Registro y Bitácora CRM -->
        <div class="space-y-6">

            <!-- Formulario de Interacción Rápida -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <h3 class="text-sm font-black text-white mb-1">Registrar Contacto con Cliente</h3>
                <p class="text-xs text-slate-400 mb-4">Anota un punto de contacto (llamada, WhatsApp, preferencia de corte).</p>

                <form action="{{ route('crm.interacciones.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Cliente *</label>
                        <select name="tercero_id" required class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            <option value="" class="bg-slate-900 text-slate-300">-- Seleccionar cliente --</option>
                            @foreach($clientesFrecuentes as $cli)
                                <option value="{{ $cli->id }}" class="bg-slate-900 text-white">{{ $cli->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Canal *</label>
                            <select name="canal" required class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <option value="WHATSAPP" class="bg-slate-900 text-white">📱 WhatsApp</option>
                                <option value="LLAMADA" class="bg-slate-900 text-white">📞 Llamada</option>
                                <option value="PRESENCIAL" class="bg-slate-900 text-white">🏢 Presencial</option>
                                <option value="EMAIL" class="bg-slate-900 text-white">✉️ Email</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Tipo *</label>
                            <select name="tipo" required class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <option value="PREFERENCIA" class="bg-slate-900 text-white">💡 Preferencia</option>
                                <option value="SEGUIMIENTO" class="bg-slate-900 text-white">🎯 Seguimiento</option>
                                <option value="FELICITACION" class="bg-slate-900 text-white">⭐ Felicitación</option>
                                <option value="RECLAMO" class="bg-slate-900 text-white">⚠️ PQR</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Nota de la Interacción *</label>
                        <textarea name="nota" required rows="3" class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition" placeholder="Cliente menciona que prefiere agendar los sábados por la mañana..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        ✓ Guardar en Historial CRM
                    </button>
                </form>
            </div>

            <!-- Bitácora de Interacciones Recientes -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <h3 class="text-sm font-black text-white mb-3">Últimas Interacciones de Clientes</h3>
                <div class="space-y-3">
                    @forelse($interaccionesRecientes as $interaccion)
                    <div class="p-3.5 bg-slate-950/60 rounded-xl border border-slate-800/80 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white">{{ optional($interaccion->tercero)->nombre_completo ?? 'Cliente' }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $interaccion->fecha_contacto ? $interaccion->fecha_contacto->diffForHumans() : '' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-indigo-400 font-bold uppercase tracking-wider">
                            <span>{{ $interaccion->canal }}</span> · <span>{{ $interaccion->tipo }}</span>
                        </div>
                        <p class="text-slate-300 text-xs pt-1">{{ $interaccion->nota }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-3 text-center">No hay interacciones recientes registradas.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
