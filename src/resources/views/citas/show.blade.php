@extends('layouts.app')

@section('title', 'Detalle del Turno #' . $cita->id . ' - ERP')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Encabezado con Estados y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('citas.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 transition">
                    ← Volver a Citas
                </a>
                <span class="text-slate-600">/</span>
                <span class="text-xs text-slate-400">Turno #{{ $cita->id }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-white tracking-tight">Turno Operativo #{{ $cita->id }}</h1>
                @if($cita->estado === 'COMPLETADA')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">✓ Completada</span>
                @elseif($cita->estado === 'EN_ATENCION')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 animate-pulse">💈 En Atención</span>
                @elseif($cita->estado === 'CANCELADA')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/15 border border-rose-500/30 text-rose-400">✗ Cancelada</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/15 border border-amber-500/30 text-amber-400">⏳ Programada</span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Empresa: <strong class="text-slate-200">{{ $cita->empresa->razon_social ?? 'Barbería y Perfumería JyM' }}</strong> · 
                Fecha: <strong class="text-slate-200">{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y - h:i A') : 'N/A' }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('citas.edit', $cita) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl shadow-sm border border-slate-700 transition">
                ✏️ Editar
            </a>
            <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl shadow-sm border border-slate-700 transition">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Grid Principal de Información -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Columna Izquierda: Información de Terceros y Servicio -->
        <div class="md:col-span-2 space-y-6">

            <!-- Card: Tercero Cliente -->
            <div class="bg-slate-800/90 rounded-2xl p-6 shadow-xl border border-slate-700/80 backdrop-blur-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-700/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold text-lg border border-indigo-500/30">
                            👤
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tercero (Cliente)</span>
                            <h2 class="text-lg font-black text-white">{{ $cita->cliente->nombre_completo ?? 'Cliente Desconocido' }}</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-500/15 text-amber-300 rounded-full text-xs font-black border border-amber-500/30">
                            ⭐ {{ $cita->cliente->puntos_fidelidad ?? 0 }} Puntos CRM
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Documento:</span>
                        <span class="font-bold text-slate-200">{{ $cita->cliente->tipo_documento ?? 'CC' }}: {{ $cita->cliente->numero_documento ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Teléfono / WhatsApp:</span>
                        <span class="font-bold text-slate-200">📞 {{ $cita->cliente->telefono ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Ciudad:</span>
                        <span class="font-bold text-slate-200">📍 {{ $cita->cliente->ciudad ?? 'Cartago' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-700/60 flex justify-end">
                    <a href="{{ route('terceros.show', $cita->cliente->id ?? 1) }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                        Ver Perfil 360° del Tercero →
                    </a>
                </div>
            </div>

            <!-- Card: Especialista / Empleado Responsable -->
            <div class="bg-slate-800/90 rounded-2xl p-6 shadow-xl border border-slate-700/80 backdrop-blur-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-700/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/30">
                            ✂️
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tercero (Empleado Asignado)</span>
                            <h2 class="text-lg font-black text-white">{{ $cita->especialista->nombre_completo ?? 'Sin asignar' }}</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2.5 py-1 bg-slate-900 text-slate-300 rounded-lg font-bold border border-slate-700">
                            Cargo: {{ $cita->especialista->cargo ?? 'Especialista' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Comisión pactada:</span>
                        <span class="font-bold text-emerald-400 text-sm">{{ $cita->especialista->porcentaje_comision ?? 0 }}%</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Liquidación estimada:</span>
                        @php
                            $comision = ($cita->total * ($cita->especialista->porcentaje_comision ?? 0)) / 100;
                        @endphp
                        <span class="font-extrabold text-white text-sm">${{ number_format($comision, 0, ',', '.') }} COP</span>
                    </div>
                </div>
            </div>

            <!-- Card: Detalle del Servicio -->
            <div class="bg-slate-800/90 rounded-2xl p-6 shadow-xl border border-slate-700/80 backdrop-blur-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Ítem Liquidado en Venta</span>
                <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-700/60 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base">{{ $cita->servicio->nombre ?? 'Servicio Estándar' }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            ⏱️ Duración: {{ $cita->servicio->duracion_minutos ?? 30 }} minutos · 
                            SKU/Código: #{{ $cita->servicio->id ?? 'SRV' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-white font-mono">
                            ${{ number_format($cita->total, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">COP</span>
                    </div>
                </div>

                @if($cita->notas)
                    <div class="mt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-300">
                        <strong>Observaciones de atención:</strong> {{ $cita->notas }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Columna Derecha: Pasarela de Pago y Arqueo -->
        <div class="space-y-6">

            <!-- Card Pasarela de Pagos (Wompi, Nequi, etc.) -->
            <div class="bg-slate-800/90 rounded-2xl p-6 shadow-xl border border-slate-700/80 backdrop-blur-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-700/80">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pasarela & Finanzas</span>
                        @if($cita->transaccionPago && $cita->transaccionPago->isAprobado())
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">APROBADO</span>
                        @elseif($cita->transaccionPago && $cita->transaccionPago->isPendiente())
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-500/15 border border-amber-500/30 text-amber-400 animate-pulse">PENDIENTE</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-700 text-slate-300">POR DEFINIR</span>
                        @endif
                    </div>

                    <div class="mt-4 space-y-3 text-xs">
                        <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-700/60">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Método / Pasarela</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @if(optional($cita->transaccionPago)->pasarela === 'WOMPI')
                                    <span class="font-extrabold text-indigo-400 text-sm">💳 WOMPI Bancolombia</span>
                                @elseif(optional($cita->transaccionPago)->pasarela === 'NEQUI')
                                    <span class="font-extrabold text-pink-400 text-sm">📱 Nequi QR Dinámico</span>
                                @elseif(optional($cita->transaccionPago)->pasarela === 'EFECTIVO')
                                    <span class="font-extrabold text-emerald-400 text-sm">💵 Efectivo en Caja</span>
                                @else
                                    <span class="font-extrabold text-slate-300 text-sm">🏦 Transferencia Directa</span>
                                @endif
                            </div>
                        </div>

                        <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-700/60">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Referencia Interna</span>
                            <span class="font-mono font-bold text-slate-200 text-xs">{{ $cita->transaccionPago->referencia_interna ?? 'TRX-DEFAULT' }}</span>
                        </div>

                        @if(optional($cita->transaccionPago)->referencia_pasarela)
                        <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-700/60">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Ref. Pasarela (Auth Code)</span>
                            <span class="font-mono font-bold text-emerald-400 text-xs">{{ $cita->transaccionPago->referencia_pasarela }}</span>
                        </div>
                        @endif

                        <div class="p-4 bg-slate-950/80 text-white rounded-xl border border-slate-700">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Monto Total a Recaudar</span>
                            <span class="text-2xl font-black text-white font-mono">${{ number_format($cita->total, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">COP · Impuestos incluidos</span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción Pasarela / Checkout -->
                <div class="mt-6 space-y-2">
                    @if($cita->transaccionPago && $cita->transaccionPago->isPendiente())
                        <a href="{{ route('pagos.checkout', $cita->transaccionPago->id) }}" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                            <span>🚀 Abrir Pasarela Wompi / Nequi</span>
                        </a>
                    @elseif($cita->transaccionPago && $cita->transaccionPago->isAprobado())
                        <div class="text-center py-2.5 px-3 bg-emerald-500/15 border border-emerald-500/30 rounded-xl text-xs font-bold text-emerald-300 flex items-center justify-center gap-1.5">
                            <span>✅ Transacción Liquidada</span>
                        </div>
                    @endif

                    @if($cita->estado !== 'COMPLETADA')
                    <form action="{{ route('citas.update', $cita) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tercero_cliente_id" value="{{ $cita->tercero_cliente_id }}">
                        <input type="hidden" name="tercero_especialista_id" value="{{ $cita->tercero_especialista_id }}">
                        <input type="hidden" name="servicio_id" value="{{ $cita->servicio_id }}">
                        <input type="hidden" name="fecha_hora" value="{{ $cita->fecha_hora ? $cita->fecha_hora->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                        <input type="hidden" name="estado" value="COMPLETADA">
                        <input type="hidden" name="pasarela" value="{{ $cita->transaccionPago->pasarela ?? 'EFECTIVO' }}">
                        <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow transition">
                            ✓ Marcar como Completada en Caja
                        </button>
                    </form>
                    @endif
                </div>

            </div>

            <!-- Card Kárdex de Puntos Ganados -->
            <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-2xl p-5 text-indigo-300 text-xs space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🎁</span>
                    <h4 class="font-extrabold text-sm text-white">Fidelización por Venta</h4>
                </div>
                <p class="text-slate-300 text-[11px] leading-relaxed">
                    Al completar este turno, el cliente acumula <strong>{{ intdiv($cita->total, 10000) }} puntos</strong> directos a su kárdex de fidelización (1 punto por cada $10.000 COP).
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
