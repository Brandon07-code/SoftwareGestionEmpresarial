@extends('layouts.app')

@section('title', 'Comprobante de Turno #' . $cita->id . ' - ERP')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Encabezado con Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Turno / Servicio #{{ $cita->id }}</h1>
                @if($cita->estado === 'COMPLETADA')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">✅ Completada</span>
                @elseif($cita->estado === 'EN_ATENCION')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">✂️ En Atención</span>
                @elseif($cita->estado === 'CANCELADA')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">❌ Cancelada</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">⏳ Programada</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Empresa: <strong class="text-slate-700">{{ $cita->empresa->razon_social ?? 'Empresa Matriz' }}</strong> · 
                Fecha: <strong class="text-slate-700">{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y - h:i A') : 'N/A' }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('citas.edit', $cita) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-sm border border-slate-200 transition">
                ✏️ Editar
            </a>
            <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-sm border border-slate-200 transition">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Grid Principal de Información -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Columna Izquierda: Información de Terceros y Servicio -->
        <div class="md:col-span-2 space-y-6">

            <!-- Card: Tercero Cliente -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                            👤
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tercero (Cliente)</span>
                            <h2 class="text-lg font-black text-slate-900">{{ $cita->cliente->nombre_completo ?? 'Cliente Desconocido' }}</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-800 rounded-full text-xs font-black border border-amber-200">
                            ⭐ {{ $cita->cliente->puntos_fidelidad ?? 0 }} Puntos CRM
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Documento:</span>
                        <span class="font-bold text-slate-800">{{ $cita->cliente->tipo_documento ?? 'CC' }}: {{ $cita->cliente->numero_documento ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Teléfono / WhatsApp:</span>
                        <span class="font-bold text-slate-800">📞 {{ $cita->cliente->telefono ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Ciudad:</span>
                        <span class="font-bold text-slate-800">📍 {{ $cita->cliente->ciudad ?? 'Cartago' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-50 flex justify-end">
                    <a href="{{ route('terceros.show', $cita->cliente->id ?? 1) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        Ver Perfil 360° del Tercero →
                    </a>
                </div>
            </div>

            <!-- Card: Especialista / Empleado Responsable -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                            ✂️
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tercero (Empleado Asignado)</span>
                            <h2 class="text-lg font-black text-slate-900">{{ $cita->especialista->nombre_completo ?? 'Sin asignar' }}</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-bold">
                            Cargo: {{ $cita->especialista->cargo ?? 'Especialista' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Comisión pactada:</span>
                        <span class="font-bold text-emerald-700 text-sm">{{ $cita->especialista->porcentaje_comision ?? 0 }}%</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Liquidación estimada:</span>
                        @php
                            $comision = ($cita->total * ($cita->especialista->porcentaje_comision ?? 0)) / 100;
                        @endphp
                        <span class="font-extrabold text-slate-900 text-sm">${{ number_format($comision, 0, ',', '.') }} COP</span>
                    </div>
                </div>
            </div>

            <!-- Card: Detalle del Servicio -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Ítem Liquidado en Venta</span>
                <div class="bg-slate-50 p-4 rounded-2xl flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $cita->servicio->nombre ?? 'Servicio Estándar' }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            ⏱️ Duración: {{ $cita->servicio->duracion_minutos ?? 30 }} minutos · 
                            SKU/Código: #{{ $cita->servicio->id ?? 'SRV' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-slate-900">
                            ${{ number_format($cita->total, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">COP</span>
                    </div>
                </div>

                @if($cita->notas)
                    <div class="mt-4 p-3 bg-amber-50/70 border border-amber-100 rounded-xl text-xs text-amber-900">
                        <strong>Observaciones de atención:</strong> {{ $cita->notas }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Columna Derecha: Pasarela de Pago y Arqueo -->
        <div class="space-y-6">

            <!-- Card Pasarela de Pagos (Wompi, Nequi, etc.) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pasarela & Finanzas</span>
                        @if($cita->transaccionPago && $cita->transaccionPago->isAprobado())
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800">APROBADO</span>
                        @elseif($cita->transaccionPago && $cita->transaccionPago->isPendiente())
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 animate-pulse">PENDIENTE</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700">POR DEFINIR</span>
                        @endif
                    </div>

                    <div class="mt-4 space-y-3 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Método / Pasarela</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @if(optional($cita->transaccionPago)->pasarela === 'WOMPI')
                                    <span class="font-extrabold text-indigo-700 text-sm">💳 WOMPI Bancolombia</span>
                                @elseif(optional($cita->transaccionPago)->pasarela === 'NEQUI')
                                    <span class="font-extrabold text-pink-700 text-sm">📱 Nequi QR Dinámico</span>
                                @elseif(optional($cita->transaccionPago)->pasarela === 'EFECTIVO')
                                    <span class="font-extrabold text-emerald-700 text-sm">💵 Efectivo en Caja</span>
                                @else
                                    <span class="font-extrabold text-slate-800 text-sm">🏦 Transferencia Directa</span>
                                @endif
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Referencia Interna</span>
                            <span class="font-mono font-bold text-slate-800 text-xs">{{ $cita->transaccionPago->referencia_interna ?? 'TRX-DEFAULT' }}</span>
                        </div>

                        @if(optional($cita->transaccionPago)->referencia_pasarela)
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Ref. Pasarela (Auth Code)</span>
                            <span class="font-mono font-bold text-emerald-800 text-xs">{{ $cita->transaccionPago->referencia_pasarela }}</span>
                        </div>
                        @endif

                        <div class="p-4 bg-slate-900 text-white rounded-2xl">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Monto Total a Recaudar</span>
                            <span class="text-2xl font-black text-white">${{ number_format($cita->total, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">COP · Impuestos incluidos</span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción Pasarela / Checkout -->
                <div class="mt-6 space-y-2">
                    @if($cita->transaccionPago && $cita->transaccionPago->isPendiente())
                        <a href="{{ route('pagos.checkout', $cita->transaccionPago->id) }}" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                            <span>🚀 Abrir Checkout Wompi / Nequi</span>
                        </a>
                    @elseif($cita->transaccionPago && $cita->transaccionPago->isAprobado())
                        <div class="text-center py-2.5 px-3 bg-emerald-50 border border-emerald-100 rounded-xl text-xs font-bold text-emerald-800 flex items-center justify-center gap-1.5">
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
                        <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow transition">
                            ✓ Marcar como Completada en Caja
                        </button>
                    </form>
                    @endif
                </div>

            </div>

            <!-- Card Kárdex de Puntos Ganados -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-5 text-indigo-950 text-xs space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🎁</span>
                    <h4 class="font-extrabold text-sm">Fidelización por Venta</h4>
                </div>
                <p class="text-indigo-800 text-[11px] leading-relaxed">
                    Al completar este turno, el cliente acumula <strong>{{ intdiv($cita->total, 10000) }} puntos</strong> directos a su kárdex de fidelización (1 punto por cada $10.000 COP).
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
