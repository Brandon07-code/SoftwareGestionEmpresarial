<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="document_type" value="Tipo de documento" />
        <select id="document_type" name="document_type" class="mt-1 block w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" required>
            @foreach (['CC' => 'Cédula de Ciudadanía', 'NIT' => 'NIT (Empresas)', 'CE' => 'Cédula de Extranjería', 'PP' => 'Pasaporte'] as $value => $label)
                <option value="{{ $value }}" class="bg-slate-900 text-white" @selected(old('document_type', $client->document_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('document_type')" />
    </div>

    <div>
        <x-input-label for="document_number" value="Número de documento" />
        <x-text-input id="document_number" name="document_number" type="text" class="mt-1 block w-full"
            :value="old('document_number', $client->document_number)" required placeholder="Ej: 1088123456" />
        <x-input-error class="mt-2" :messages="$errors->get('document_number')" />
    </div>
</div>

<div>
    <x-input-label for="name" value="Nombre / Razón social" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
        :value="old('name', $client->name)" required placeholder="Ej: Juan Pérez o Distribuidora SAS" />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="email" value="Correo electrónico (opcional)" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
            :value="old('email', $client->email)" placeholder="cliente@correo.com" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="phone" value="Teléfono (opcional)" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
            :value="old('phone', $client->phone)" placeholder="Ej: 3001234567" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>
</div>

<div>
    <x-input-label for="address" value="Dirección (opcional)" />
    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
        :value="old('address', $client->address)" placeholder="Ej: Calle 10 # 20-30" />
    <x-input-error class="mt-2" :messages="$errors->get('address')" />
</div>

<div class="pt-2">
    <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="active" value="1" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-indigo-500"
            @checked(old('active', $client->active))>
        <span class="text-sm font-semibold text-slate-300">Cliente activo en el sistema</span>
    </label>
</div>
