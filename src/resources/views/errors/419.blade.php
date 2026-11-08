<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 — Sesión Expirada | Turnix ERP</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-lg w-full bg-slate-900/90 border border-slate-800/80 rounded-3xl p-8 sm:p-10 text-center shadow-2xl shadow-cyan-950/20 backdrop-blur-md relative z-10">
        <div class="mx-auto w-16 h-16 rounded-2xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-6 shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <span class="inline-block px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-bold uppercase tracking-wider mb-3">
            Seguridad de Sesión • Código 419
        </span>

        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-3">
            Sesión Expirada
        </h1>

        <p class="text-sm text-slate-300 leading-relaxed mb-6">
            Por políticas de seguridad corporativa, su sesión ha caducado por inactividad. Por favor inicie sesión nuevamente para continuar trabajando en el ERP.
        </p>

        <div class="flex items-center justify-center">
            <a href="{{ route('login') }}" 
               class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition">
                Iniciar Sesión Nuevamente
            </a>
        </div>
    </div>
</body>
</html>
