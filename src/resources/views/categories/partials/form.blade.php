<div>
    <x-input-label for="name" value="Nombre" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>
<div>
    <x-input-label for="description" value="Descripción (opcional)" />
    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $category->description ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('description')" />
</div>
<div>
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="active" value="1" class="rounded border-gray-300" @checked(old('active', $category->active ?? true))>
        <span>Categoría activa</span>
    </label>
</div>
