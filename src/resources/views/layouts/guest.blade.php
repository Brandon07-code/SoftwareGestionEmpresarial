<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active {
                -webkit-box-shadow: 0 0 0 1000px #020617 inset !important;
                -webkit-text-fill-color: #f8fafc !important;
                transition: background-color 5000s ease-in-out 0s;
            }
        </style>
    </head>
    <body class="font-sans text-slate-100 antialiased bg-[#090D16] selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#090D16] px-4 relative">
            <!-- Ambient subtle glow -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="mb-6 relative z-10">
                <a href="/" class="transition hover:opacity-90">
                    <x-application-logo :dark="true" />
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 sm:px-8 bg-slate-900/90 backdrop-blur-md shadow-2xl shadow-indigo-950/20 border border-slate-800/80 rounded-2xl relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
