<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Acceso No Autorizado | Turnix ERP</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient subtle background glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-lg w-full bg-slate-900/90 border border-slate-800/80 rounded-3xl p-8 sm:p-10 text-center shadow-2xl shadow-rose-950/20 backdrop-blur-md relative z-10">
        <!-- Shield Icon with Rose Glow -->
        <div class="mx-auto w-16 h-16 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 mb-6 shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <span class="inline-block px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold uppercase tracking-wider mb-3">
            Seguridad RBAC • Código 403
        </span>

        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-3">
            Acceso No Autorizado
        </h1>

        <p class="text-sm text-slate-300 leading-relaxed mb-6">
            Su usuario o rol actual no posee los permisos requeridos para acceder a este módulo. Si considera que requiere este acceso para sus funciones laborales, comuníquese con el <strong>Administrador del Sistema</strong>.
        </p>

        @if(auth()->check())
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 mb-6 text-xs text-slate-400 flex items-center justify-between">
                <span>Usuario activo: <strong class="text-white">{{ auth()->user()->name }}</strong></span>
                <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-semibold uppercase text-[10px]">
                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Sin Rol' }}
                </span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" 
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition">
                ← Volver Atrás
            </a>
            <a href="{{ route('dashboard') }}" 
               class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition">
                Ir al Panel Principal
            </a>
        </div>
    </div>
</body>
</html>
