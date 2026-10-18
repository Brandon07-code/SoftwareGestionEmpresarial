<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Turnix ERP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS & Config -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                950: '#020617',
                                900: '#0B132B',
                                800: '#1C2541',
                                royal: '#2563EB',
                                indigo: '#4F46E5',
                                light: '#EEF2FF',
                            }
                        }
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased text-slate-100 bg-[#090D16] selection:bg-indigo-500 selection:text-white overflow-x-hidden min-h-screen">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-slate-900/60 backdrop-blur-md border-b border-slate-800/80 shadow-md">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    <div class="py-8">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            @yield('content')
                        </div>
                    </div>
                @endif
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
