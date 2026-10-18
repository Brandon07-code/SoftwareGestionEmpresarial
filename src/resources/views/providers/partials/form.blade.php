<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="document_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Tipo de Documento</label>
        <select id="document_type" name="document_type" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition shadow-sm" required>
            @foreach (['NIT' => 'NIT (Persona Jurídica)', 'CC' => 'Cédula de Ciudadanía', 'CE' => 'Cédula de Extranjería', 'PP' => 'Pasaporte'] as $value => $label)
                <option value="{{ $value }}" @selected(old('document_type', $provider->document_type) === $value) class="bg-slate-900 text-white">{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('document_type')" />
    </div>

    <div>
        <label for="document_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Número de Documento / NIT</label>
        <x-text-input id="document_number" name="document_number" type="text" class="w-full"
            :value="old('document_number', $provider->document_number)" placeholder="Ej. 900887654-2" required />
        <x-input-error class="mt-2" :messages="$errors->get('document_number')" />
    </div>
</div>

<div>
    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Razón Social / Nombre Comercial</label>
    <x-text-input id="name" name="name" type="text" class="w-full"
        :value="old('name', $provider->name)" placeholder="Ej. Distribuidora Cosméticos del Eje S.A.S." required />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <label for="contact_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Persona de Contacto / Asesor Comercial (Opcional)</label>
    <x-text-input id="contact_name" name="contact_name" type="text" class="w-full"
        :value="old('contact_name', $provider->contact_name)" placeholder="Ej. Diana Marcela Mejía" />
    <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Correo Electrónico (Opcional)</label>
        <x-text-input id="email" name="email" type="email" class="w-full"
            :value="old('email', $provider->email)" placeholder="ventas@proveedor.com" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Teléfono / WhatsApp (Opcional)</label>
        <x-text-input id="phone" name="phone" type="text" class="w-full"
            :value="old('phone', $provider->phone)" placeholder="Ej. 3206654433" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>
</div>

<div>
    <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Dirección de Despacho (Opcional)</label>
    <x-text-input id="address" name="address" type="text" class="w-full"
        :value="old('address', $provider->address)" placeholder="Ej. Cartago, Carrera 4 #12-30" />
    <x-input-error class="mt-2" :messages="$errors->get('address')" />
</div>

<div class="pt-2">
    <label class="inline-flex items-center gap-3 cursor-pointer">
        <input type="checkbox" name="active" value="1" class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-indigo-600 focus:ring-indigo-500/30"
            @checked(old('active', $provider->active))>
        <span class="text-sm font-medium text-slate-200">Proveedor activo para generación de compras</span>
    </label>
</div>
