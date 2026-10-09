@extends('layouts.app')

@section('title', 'Perfil 360° - ' . $tercero->nombre_completo . ' - ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de Perfil 360 -->
    <div class="bg-slate-900/90 rounded-2xl p-6 sm:p-8 shadow-2xl border border-slate-800/80 backdrop-blur-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-indigo-600/30">
                {{ strtoupper(substr($tercero->nombre_completo, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-white tracking-tight">{{ $tercero->nombre_completo }}</h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-slate-800 text-slate-300 border border-slate-700/80">
                        {{ $tercero->tipo_documento }}: {{ $tercero->numero_documento }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    @if($tercero->es_cliente)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                            ⭐ Cliente
                        </span>
                    @endif
                    @if($tercero->es_empleado)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                            ✂️ Empleado ({{ $tercero->cargo ?? 'Especialista' }})
                        </span>
                    @endif
                    @if($tercero->es_proveedor)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                            📦 Proveedor
                        </span>
                    @endif
                    <span class="text-slate-400 text-xs font-medium">📍 {{ $tercero->ciudad }} · 📞 {{ $tercero->telefono }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('citas.create', ['cliente_id' => $tercero->id]) }}" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                + Agendar Cita
            </a>
            <a href="{{ route('terceros.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700/80 text-xs font-bold rounded-xl transition shadow-sm">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Métricas 360 del Actor (KPIs sin bordes) -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Puntos Fidelidad CRM</span>
            <div class="text-2xl font-black text-cyan-400 mt-2">⭐ {{ $tercero->puntos_fidelidad }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Kárdex de premios activo</span>
        </div>

        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Turnos Consumidos</span>
            <div class="text-2xl font-black text-indigo-400 mt-2">{{ $tercero->citasComoCliente->count() }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Historial como cliente</span>
        </div>

        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Atenciones Realizadas</span>
            <div class="text-2xl font-black text-emerald-400 mt-2">{{ $tercero->citasComoEspecialista->count() }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Comisión: {{ $tercero->porcentaje_comision }}%</span>
        </div>

        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-xl backdrop-blur-sm transition">
            <span class="text-slate-400 text-xs uppercase font-bold tracking-wider block">Interacciones CRM</span>
            <div class="text-2xl font-black text-white mt-2">{{ $tercero->crmInteracciones->count() }}</div>
            <span class="text-xs text-slate-400 mt-1 block font-medium">Contactos y seguimiento</span>
        </div>
    </div>

    <!-- Contenido en 2 Columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna Izquierda (2 cols): Turnos e Historial de Puntos -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Historial de Turnos y Servicios -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-black text-white">Historial de Turnos y Atenciones</h2>
                    <span class="text-xs text-slate-400 font-medium">Últimos registros</span>
                </div>

                @if($tercero->citasComoCliente->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase font-bold tracking-wider text-[11px] border-b border-slate-800/80">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Fecha</th>
                                <th class="px-4 py-3">Servicio</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 rounded-r-xl text-right">Ver</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                            @foreach($tercero->citasComoCliente as $c)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3 font-semibold text-white font-mono">{{ $c->fecha_hora ? $c->fecha_hora->format('d/m/Y h:i A') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ $c->servicio->nombre ?? 'Servicio' }}</td>
                                <td class="px-4 py-3 font-bold text-white font-mono">${{ number_format($c->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $c->estado === 'COMPLETADA' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30' }}">
                                        {{ $c->estado }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('citas.show', $c) }}" class="text-cyan-400 hover:text-cyan-300 font-bold transition">Detalle →</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-xs text-slate-400 py-4 text-center">Este tercero aún no registra turnos como cliente.</p>
                @endif
            </div>

            <!-- Kárdex de Puntos (Ledger de Fidelización) -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-white">Kárdex de Fidelización (Ledger de Puntos)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Trazabilidad contable e inmutable de acumulación y redención de puntos.</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-500/15 text-indigo-300 rounded-full text-xs font-black border border-indigo-500/30">
                        Saldo: {{ $tercero->puntos_fidelidad }} pts
                    </span>
                </div>

                @if($tercero->puntosMovimientos->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase font-bold tracking-wider text-[11px] border-b border-slate-800/80">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Puntos</th>
                                <th class="px-4 py-3">Saldo Ant. → Nuevo</th>
                                <th class="px-4 py-3 rounded-r-xl">Concepto / Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40 text-slate-300">
                            @foreach($tercero->puntosMovimientos as $pm)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3 font-medium text-slate-400 font-mono">{{ $pm->created_at ? $pm->created_at->format('d/m/Y h:i A') : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($pm->tipo === 'ACUMULACION')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">+ ACUMULACIÓN</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">- REDENCIÓN</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-black text-white font-mono">{{ $pm->puntos }} pts</td>
                                <td class="px-4 py-3 font-mono text-[11px] text-slate-400">{{ $pm->saldo_anterior }} → {{ $pm->saldo_nuevo }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $pm->motivo }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-xs text-slate-400 py-4 text-center">No hay movimientos contables de puntos registrados para este tercero.</p>
                @endif
            </div>

        </div>

        <!-- Columna Derecha (1 col): Bitácora CRM y Nueva Interacción -->
        <div class="space-y-6">

            <!-- Registrar Interacción CRM -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <h3 class="text-sm font-black text-white mb-1">Registrar Contacto CRM</h3>
                <p class="text-xs text-slate-400 mb-4">Anota llamadas, mensajes de WhatsApp o notas de seguimiento.</p>

                <form action="{{ route('crm.interacciones.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="tercero_id" value="{{ $tercero->id }}">

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Canal</label>
                            <select name="canal" class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <option value="WHATSAPP" class="bg-slate-900 text-white">📱 WhatsApp</option>
                                <option value="LLAMADA" class="bg-slate-900 text-white">📞 Llamada</option>
                                <option value="PRESENCIAL" class="bg-slate-900 text-white">🏢 Presencial</option>
                                <option value="EMAIL" class="bg-slate-900 text-white">✉️ Email</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Tipo</label>
                            <select name="tipo" class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <option value="PREFERENCIA" class="bg-slate-900 text-white">💡 Preferencia</option>
                                <option value="SEGUIMIENTO" class="bg-slate-900 text-white">🎯 Seguimiento</option>
                                <option value="FELICITACION" class="bg-slate-900 text-white">⭐ Felicitación</option>
                                <option value="RECLAMO" class="bg-slate-900 text-white">⚠️ PQR / Reclamo</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Nota o Resumen de Interacción</label>
                        <textarea name="nota" required rows="3" class="w-full text-xs bg-slate-950/80 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-3 py-2.5 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition" placeholder="Cliente solicita agendar cada 15 días con Carlos Duque..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        ✓ Guardar Interacción en Bitácora
                    </button>
                </form>
            </div>

            <!-- Bitácora de Interacciones Pasadas -->
            <div class="bg-slate-900/80 rounded-2xl shadow-2xl border border-slate-800/80 p-6 backdrop-blur-sm">
                <h3 class="text-sm font-black text-white mb-3">Historial de Interacciones CRM</h3>
                <div class="space-y-3">
                    @forelse($tercero->crmInteracciones as $int)
                    <div class="p-3.5 bg-slate-950/60 rounded-xl border border-slate-800/80 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white uppercase text-[10px] tracking-wider">{{ $int->canal }} · {{ $int->tipo }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $int->fecha_contacto ? $int->fecha_contacto->format('d/m/Y') : '' }}</span>
                        </div>
                        <p class="text-slate-300 text-xs">{{ $int->nota }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-3 text-center">Sin interacciones CRM registradas aún.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
