<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nuevo producto</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('products.store') }}" class="bg-white shadow rounded p-6 space-y-4">
                @csrf
                @include('products.partials.form')

                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button>Guardar</x-primary-button>
                    <a href="{{ route('products.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
