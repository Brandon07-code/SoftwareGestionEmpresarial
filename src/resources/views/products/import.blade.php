<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Importar productos desde Excel</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow rounded p-6 space-y-3 text-sm text-gray-700">
                <p><strong>Paso 1.</strong> Descargue la plantilla y llénela (una fila por producto).
                    <a href="{{ route('products.template') }}" class="text-blue-700 underline font-semibold">Descargar plantilla</a></p>
                <p><strong>Paso 2.</strong> Columnas: <code>nombre</code>, <code>descripcion</code>, <code>precio</code>,
                    <code>stock</code> y <code>categoria</code> (debe existir con ese nombre exacto).</p>
                <p><strong>Paso 3.</strong> Suba el archivo. Si un producto ya existe (mismo nombre) se actualizan precio,
                    descripción y categoría; el stock <strong>no</strong> se modifica (eso se hace con un ajuste de inventario).</p>
            </div>

            <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data"
                  class="bg-white shadow rounded p-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="file" value="Archivo (.xlsx, .xls o .csv, máximo 2 MB)" />
                    <input id="file" type="file" name="file" accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>Importar</x-primary-button>
                    <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Volver a productos</a>
                </div>
            </form>

            @if (session('failures'))
                <div class="bg-white shadow rounded p-6 border-l-4 border-red-500">
                    <h3 class="font-semibold text-red-700 mb-2">Filas omitidas por errores ({{ count(session('failures')) }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b text-left bg-red-50 text-red-800">
                                    <th class="p-2 font-semibold">Fila</th>
                                    <th class="p-2 font-semibold">Columna</th>
                                    <th class="p-2 font-semibold">Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (session('failures') as $failure)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 font-medium text-gray-900">{{ $failure['fila'] }}</td>
                                        <td class="p-2 font-mono text-xs text-red-600">{{ $failure['columna'] }}</td>
                                        <td class="p-2 text-red-700">{{ implode(' ', (array) $failure['errores']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
