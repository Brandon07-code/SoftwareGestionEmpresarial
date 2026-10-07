<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Productos {{ $showTrashed ? '(papelera)' : '' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6 space-y-4">

                @if (session('success'))
                    <div class="p-3 rounded bg-green-100 text-green-800 font-semibold">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="p-3 rounded bg-red-100 text-red-800 font-semibold">{{ session('error') }}</div>
                @endif

                <div class="flex flex-wrap gap-4">
                    @can('crear-productos')
                        <a href="{{ route('products.create') }}" class="text-blue-700 underline font-semibold">+ Nuevo producto</a>
                    @endcan

                    @can('eliminar-productos')
                        @if ($showTrashed)
                            <a href="{{ route('products.index') }}" class="text-blue-700 underline font-semibold">Ver productos activos</a>
                        @else
                            <a href="{{ route('products.index', ['trashed' => 1]) }}" class="text-blue-700 underline font-semibold">Ver papelera ({{ $trashedCount }})</a>
                        @endif
                    @endcan
                </div>

                <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap gap-2">
                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="border-gray-300 rounded-md">

                    <select name="category" class="border-gray-300 rounded-md">
                        <option value="">Todas las categorías</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="border-gray-300 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="active" @selected(request('status') === 'active')>Activos</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                    </select>

                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left bg-gray-50">
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Categoría</th>
                            <th class="p-2">Precio</th>
                            <th class="p-2">Stock</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2 font-medium">{{ $product->name }}</td>
                                <td class="p-2">{{ $product->category?->name }}</td>
                                <td class="p-2">$ {{ number_format((float) $product->price, 2) }}</td>
                                <td class="p-2">{{ $product->stock }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $product->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="p-2 space-x-2">
                                    @if ($product->trashed())
                                        <form method="POST" action="{{ route('products.restore', $product) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-700 underline font-semibold">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-productos')
                                            <a href="{{ route('products.edit', $product) }}" class="text-blue-700 underline font-semibold">Editar</a>
                                        @endcan
                                        @can('eliminar-productos')
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('¿Enviar este producto a la papelera?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-700 underline font-semibold">Eliminar</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">No hay productos para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $products->links() }}

            </div>
        </div>
    </div>
</x-app-layout>
