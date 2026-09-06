<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JyM ERP') - Software de Gestión Empresarial</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col bg-slate-100 text-slate-900 antialiased selection:bg-slate-900 selection:text-white">

    <!-- Barra de Navegación Minimalista Oscura -->
    <header class="bg-slate-900 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Marca / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('citas.index') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-lg group-hover:bg-slate-700 transition">
                            💈
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-extrabold tracking-wide text-white">JyM <span class="text-slate-400 font-light">ERP</span></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-semibold uppercase tracking-wider">Cartago</span>
                            </div>
                            <span class="block text-[11px] text-slate-400 font-medium">Software de Gestión Empresarial</span>
                        </div>
                    </a>
                </div>

                <!-- Enlaces de Navegación -->
                <nav class="flex items-center gap-2">
                    <a href="{{ route('citas.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('citas.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Agenda & Turnos
                    </a>
                    <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-slate-950 text-sm font-bold rounded-xl shadow hover:bg-slate-200 transition">
                        <span>+ Agendar Cita</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <!-- Alertas de Sesión sin bordes -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-900 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-lg">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 text-sm font-bold">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-900 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-lg">⚠️</span>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 text-sm font-bold">✕</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Pie de página sin bordes visibles -->
    <footer class="bg-white py-6 text-center text-xs text-slate-500 shadow-inner">
        <p><strong>Barbería y Perfumería JyM ERP</strong> — COTECNOVA 2026 · Cartago, Valle del Cauca</p>
    </footer>

</body>
</html>
