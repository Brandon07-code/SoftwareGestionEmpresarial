<div>
    <x-input-label for="name" value="Nombre" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>
<div>
    <x-input-label for="description" value="Descripción (opcional)" />
    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $product->description ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('description')" />
</div>
<div>
    <x-input-label for="price" value="Precio" />
    <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $product->price ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('price')" />
</div>
<div>
    <x-input-label for="stock" value="Stock" />
    <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full" :value="old('stock', $product->stock ?? 0)" required />
    <x-input-error class="mt-2" :messages="$errors->get('stock')" />
</div>
<div>
    <x-input-label for="category_id" value="Categoría" />
    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="">Selecciona una categoría</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
</div>
<div>
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="active" value="1" class="rounded border-gray-300" @checked(old('active', $product->active ?? true))>
        <span>Producto activo</span>
    </label>
</div>
