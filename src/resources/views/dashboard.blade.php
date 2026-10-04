<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6 space-y-6">

                <div>
                    <p class="text-lg font-semibold">Bienvenido, {{ $user->name }}</p>
                    <p class="text-sm text-gray-600">
                        Rol: {{ $roles->isNotEmpty() ? $roles->join(', ') : 'Sin rol asignado' }}
                    </p>
                </div>

                @if ($stats->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($stats as $stat)
                            <div class="border rounded p-4 bg-slate-50">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ $stat['label'] }}</p>
                                <p class="text-xl font-black text-slate-900 mt-1">{{ $stat['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <h3 class="font-semibold mb-2">Módulos disponibles</h3>
                    <ul class="list-disc ps-6 space-y-1">
                        @forelse ($modules as $module)
                            <li>
                                <a href="{{ route($module['route']) }}" class="text-blue-700 underline font-medium">{{ $module['title'] }}</a>
                                - {{ $module['description'] }}
                            </li>
                        @empty
                            <li class="text-gray-500">No tienes módulos disponibles. Pide a un administrador que te asigne un rol.</li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
