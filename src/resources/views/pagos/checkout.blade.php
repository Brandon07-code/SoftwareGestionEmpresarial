@extends('layouts.app')

@section('title', 'Checkout Pasarela ' . $transaccion->pasarela . ' - ERP')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Encabezado con Botón Volver -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Pasarela de Pago Electrónico</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $transaccion->pasarela === 'WOMPI' ? 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30' : 'bg-pink-500/15 text-pink-300 border border-pink-500/30' }}">
                    {{ $transaccion->pasarela }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Simulador de procesamiento transaccional y respuesta de webhook oficial.</p>
        </div>
        <a href="{{ route('pagos.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700/80 text-xs font-bold rounded-xl transition shadow-sm">
            ← Volver al Panel
        </a>
    </div>

    <!-- Simulación del Widget de la Pasarela -->
    <div class="bg-slate-900/90 rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden backdrop-blur-sm">

        <!-- Barra Superior del Widget -->
        <div class="p-6 {{ $transaccion->pasarela === 'WOMPI' ? 'bg-indigo-950/80 border-b border-indigo-900/50 text-white' : 'bg-pink-950/80 border-b border-pink-900/50 text-white' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl font-bold">
                        {{ $transaccion->pasarela === 'WOMPI' ? '💳' : '📱' }}
                    </div>
                    <div>
                        <h2 class="text-base font-black tracking-wide">
                            {{ $transaccion->pasarela === 'WOMPI' ? 'Checkout Oficial WOMPI Bancolombia' : 'Pasarela Móvil NEQUI' }}
                        </h2>
                        <span class="text-[11px] text-slate-300 font-mono">Ref: {{ $transaccion->referencia_interna }}</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold tracking-wider block text-slate-300">Total a Pagar</span>
                    <span class="text-2xl font-black">${{ number_format($transaccion->monto, 0, ',', '.') }}</span>
                    <span class="text-[10px] block text-slate-300">COP</span>
                </div>
            </div>
        </div>

        <!-- Cuerpo del Checkout -->
        <div class="p-6 sm:p-8 space-y-6">

            <!-- Estado de la Transacción -->
            <div class="p-4 rounded-2xl flex items-center justify-between {{ $transaccion->estado === 'APPROVED' ? 'bg-emerald-950/40 text-emerald-300 border border-emerald-800/60' : ($transaccion->estado === 'DECLINED' ? 'bg-rose-950/40 text-rose-300 border border-rose-800/60' : 'bg-indigo-950/40 text-indigo-300 border border-indigo-800/60') }}">
                <div class="flex items-center gap-3">
                    <span class="text-xl">
                        @if($transaccion->estado === 'APPROVED') ✅ @elseif($transaccion->estado === 'DECLINED') ❌ @else ⏳ @endif
                    </span>
                    <div>
                        <div class="text-xs font-black uppercase tracking-wider">Estado de Transacción: {{ $transaccion->estado }}</div>
                        <p class="text-[11px] text-slate-300 mt-0.5">
                            @if($transaccion->estado === 'APPROVED')
                                El pago fue procesado con éxito y liquidado en las cuentas del ERP.
                            @elseif($transaccion->estado === 'DECLINED')
                                La transacción fue declinada por el banco emisor.
                            @else
                                Esperando confirmación de la pasarela o simulación de webhook.
                            @endif
                        </p>
                    </div>
                </div>
                @if($transaccion->referencia_pasarela)
                    <span class="text-xs font-mono font-bold px-2.5 py-1 bg-slate-900 border border-slate-700 text-white rounded-lg shadow-sm">
                        {{ $transaccion->referencia_pasarela }}
                    </span>
                @endif
            </div>

            <!-- Interfaz Específica según Pasarela -->
            @if($transaccion->pasarela === 'NEQUI')
            <!-- Simulación NEQUI: QR Dinámico y Notificación Push -->
            <div class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 text-center space-y-4">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black bg-pink-500/15 text-pink-300 border border-pink-500/30 uppercase tracking-wider">
                    Escanea con tu App Nequi
                </span>
                
                <!-- QR Code Simulado SVG -->
                <div class="w-48 h-48 mx-auto bg-white p-3 rounded-2xl shadow-lg border border-slate-700 flex items-center justify-center">
                    <svg class="w-full h-full text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                        <path d="M10,10 h30 v30 h-30 z M15,15 v20 h20 v-20 z M20,20 h10 v10 h-10 z" />
                        <path d="M60,10 h30 v30 h-30 z M65,15 v20 h20 v-20 z M70,20 h10 v10 h-10 z" />
                        <path d="M10,60 h30 v30 h-30 z M15,65 v20 h20 v-20 z M20,70 h10 v10 h-10 z" />
                        <circle cx="50" cy="50" r="8" fill="#e11d48" />
                        <rect x="48" y="20" width="4" height="15" />
                        <rect x="70" y="55" width="20" height="4" />
                        <rect x="55" y="70" width="10" height="20" />
                        <rect x="75" y="75" width="15" height="15" />
                    </svg>
                </div>

                <div class="text-xs text-slate-400 space-y-1">
                    <p>Abre Nequi &gt; Escanear código QR o acepta la notificación push enviada a:</p>
                    <p class="font-extrabold text-white text-sm">📱 {{ optional($transaccion->cita->cliente ?? $transaccion->venta->cliente)->telefono ?? '315 222 3344' }}</p>
                </div>
            </div>

            @else
            <!-- Simulación WOMPI Bancolombia -->
            <div class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 space-y-4">
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                    Opciones de Pago Disponibles en Wompi
                </span>

                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 bg-slate-900 rounded-xl border-2 border-indigo-500 text-center shadow-sm">
                        <span class="text-xl block mb-1">🏦</span>
                        <span class="text-[11px] font-bold text-white block">Bancolombia</span>
                        <span class="text-[10px] text-indigo-400 font-semibold">Transferencia</span>
                    </div>

                    <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-center opacity-70">
                        <span class="text-xl block mb-1">⚡</span>
                        <span class="text-[11px] font-bold text-slate-300 block">PSE</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Cualquier banco</span>
                    </div>

                    <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-center opacity-70">
                        <span class="text-xl block mb-1">💳</span>
                        <span class="text-[11px] font-bold text-slate-300 block">Tarjetas</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Crédito / Débito</span>
                    </div>
                </div>

                <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-xs text-slate-300">
                    <strong class="text-white">Comercio Receptor:</strong> {{ $transaccion->empresa->razon_social ?? 'Empresa Matriz ERP S.A.S.' }} (NIT: {{ $transaccion->empresa->nit ?? '901.884.231-1' }})
                </div>
            </div>
            @endif

            <!-- Datos del Cliente y Servicio Vinculado -->
            <div class="p-4 bg-slate-950/60 rounded-2xl border border-slate-800 text-xs grid grid-cols-2 gap-4">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Cliente Receptor</span>
                    <span class="font-bold text-white text-sm">
                        {{ optional($transaccion->cita->cliente ?? $transaccion->venta->cliente)->nombre_completo ?? 'Consumidor Final' }}
                    </span>
                    <span class="text-slate-400 block text-[11px] font-mono">
                        CC/NIT: {{ optional($transaccion->cita->cliente ?? $transaccion->venta->cliente)->numero_documento ?? 'N/A' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Concepto Transaccional</span>
                    @if($transaccion->cita)
                        <span class="font-bold text-white text-sm">{{ $transaccion->cita->servicio->nombre ?? 'Servicio Profesional' }}</span>
                        <span class="text-slate-400 block text-[11px]">Turno #{{ $transaccion->cita->id }}</span>
                    @else
                        <span class="font-bold text-white text-sm">Venta Directa ERP</span>
                    @endif
                </div>
            </div>

            <!-- Botones Interactivos de Simulación de Webhooks (Wompi / Nequi) -->
            <div class="pt-4 border-t border-slate-800 space-y-3">
                <span class="block text-xs font-black uppercase tracking-wider text-slate-400 text-center">
                    Simulación de Evento Webhook de la Pasarela
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form action="{{ route('pagos.simular', $transaccion->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="accion" value="APPROVED">
                        <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                            <span>✅ Simular Aprobación Webhook</span>
                        </button>
                    </form>

                    <form action="{{ route('pagos.simular', $transaccion->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="accion" value="DECLINED">
                        <button type="submit" class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                            <span>❌ Simular Rechazo Webhook</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Visor de Payload Webhook (JSON) -->
            @if($transaccion->respuesta_payload)
            <div class="pt-4 border-t border-slate-800">
                <span class="block text-[11px] font-bold uppercase text-slate-400 mb-2">Payload JSON Procesado por el Servidor</span>
                <pre class="bg-slate-950 text-emerald-400 border border-slate-800 p-4 rounded-2xl text-[11px] font-mono overflow-x-auto">{{ json_encode($transaccion->respuesta_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection
