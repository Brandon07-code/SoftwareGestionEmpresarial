@extends('layouts.app')

@section('title', 'Perfil 360° - ' . $tercero->nombre_completo . ' - ERP')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de Perfil 360 -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-md">
                {{ strtoupper(substr($tercero->nombre_completo, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $tercero->nombre_completo }}</h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700">
                        {{ $tercero->tipo_documento }}: {{ $tercero->numero_documento }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    @if($tercero->es_cliente)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                            ⭐ Cliente
                        </span>
                    @endif
                    @if($tercero->es_empleado)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ✂️ Empleado ({{ $tercero->cargo ?? 'Especialista' }})
                        </span>
                    @endif
                    @if($tercero->es_proveedor)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">
                            📦 Proveedor
                        </span>
                    @endif
                    <span class="text-slate-400 text-xs font-medium">📍 {{ $tercero->ciudad }} · 📞 {{ $tercero->telefono }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('citas.create', ['cliente_id' => $tercero->id]) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                + Agendar Cita a este Tercero
            </a>
            <a href="{{ route('terceros.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Métricas 360 del Actor -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Puntos Fidelidad CRM</span>
            <div class="text-2xl font-black text-amber-600 mt-1">⭐ {{ $tercero->puntos_fidelidad }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Kárdex de premios activo</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Turnos Consumidos</span>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ $tercero->citasComoCliente->count() }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Historial como cliente</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Atenciones Realizadas</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $tercero->citasComoEspecialista->count() }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Comisión: {{ $tercero->porcentaje_comision }}%</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Interacciones CRM</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $tercero->crmInteracciones->count() }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Contactos y seguimiento</span>
        </div>
    </div>

    <!-- Contenido en 2 Columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna Izquierda (2 cols): Turnos e Historial de Puntos -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Historial de Turnos y Servicios -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-extrabold text-slate-900">Historial de Turnos y Atenciones</h2>
                    <span class="text-xs text-slate-400 font-medium">Últimos registros</span>
                </div>

                @if($tercero->citasComoCliente->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Fecha</th>
                                <th class="px-4 py-3">Servicio</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 rounded-r-xl text-right">Ver</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($tercero->citasComoCliente as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->fecha_hora ? $c->fecha_hora->format('d/m/Y h:i A') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $c->servicio->nombre ?? 'Servicio' }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">${{ number_format($c->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $c->estado === 'COMPLETADA' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $c->estado }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('citas.show', $c) }}" class="text-indigo-600 hover:text-indigo-800 font-bold">Detalle →</a>
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
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Kárdex de Fidelización (Ledger de Puntos)</h2>
                        <p class="text-xs text-slate-500">Trazabilidad contable e inmutable de acumulación y redención de puntos.</p>
                    </div>
                    <span class="px-3 py-1 bg-amber-50 text-amber-800 rounded-full text-xs font-black border border-amber-200">
                        Saldo: {{ $tercero->puntos_fidelidad }} pts
                    </span>
                </div>

                @if($tercero->puntosMovimientos->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Puntos</th>
                                <th class="px-4 py-3">Saldo Ant. → Nuevo</th>
                                <th class="px-4 py-3 rounded-r-xl">Concepto / Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($tercero->puntosMovimientos as $pm)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-medium text-slate-600">{{ $pm->created_at ? $pm->created_at->format('d/m/Y h:i A') : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($pm->tipo === 'ACUMULACION')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">+ ACUMULACIÓN</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">- REDENCIÓN</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-black text-slate-900">{{ $pm->puntos }} pts</td>
                                <td class="px-4 py-3 font-mono text-[11px] text-slate-500">{{ $pm->saldo_anterior }} → {{ $pm->saldo_nuevo }}</td>
                                <td class="px-4 py-3 text-slate-700 font-medium">{{ $pm->motivo }}</td>
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
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 mb-1">Registrar Contacto CRM</h3>
                <p class="text-xs text-slate-500 mb-4">Anota llamadas, mensajes de WhatsApp o notas de seguimiento.</p>

                <form action="{{ route('crm.interacciones.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="tercero_id" value="{{ $tercero->id }}">

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Canal</label>
                            <select name="canal" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium">
                                <option value="WHATSAPP">📱 WhatsApp</option>
                                <option value="LLAMADA">📞 Llamada</option>
                                <option value="PRESENCIAL">🏢 Presencial</option>
                                <option value="EMAIL">✉️ Email</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Tipo</label>
                            <select name="tipo" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium">
                                <option value="PREFERENCIA">💡 Preferencia</option>
                                <option value="SEGUIMIENTO">🎯 Seguimiento</option>
                                <option value="FELICITACION">⭐ Felicitación</option>
                                <option value="RECLAMO">⚠️ PQR / Reclamo</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Nota o Resumen de Interacción</label>
                        <textarea name="nota" required rows="3" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium" placeholder="Cliente solicita agendar cada 15 días con Carlos Duque..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow transition">
                        ✓ Guardar Interacción en Bitácora
                    </button>
                </form>
            </div>

            <!-- Bitácora de Interacciones Pasadas -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Historial de Interacciones CRM</h3>
                <div class="space-y-3">
                    @forelse($tercero->crmInteracciones as $int)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800 uppercase text-[10px] tracking-wider">{{ $int->canal }} · {{ $int->tipo }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $int->fecha_contacto ? $int->fecha_contacto->format('d/m/Y') : '' }}</span>
                        </div>
                        <p class="text-slate-700 text-xs">{{ $int->nota }}</p>
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
