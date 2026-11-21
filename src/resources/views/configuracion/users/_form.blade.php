@php
    $assignedRoles = old('roles', $user->exists ? $user->roles->pluck('name')->all() : []);
    $assignedDirectPermissions = old('permissions', $user->exists ? $user->permissions->pluck('name')->all() : []);
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                Nombre Completo <span class="text-indigo-400">*</span>
            </label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                   class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                Correo Electrónico <span class="text-indigo-400">*</span>
            </label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                   class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />
        </div>

        <div>
            <label for="telefono" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                Teléfono
            </label>
            <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $user->telefono) }}"
                   class="w-full bg-slate-950/80 border border-slate-700 text-white placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
            <x-input-error class="mt-1.5" :messages="$errors->get('telefono')" />
        </div>

        <div>
            <label for="empresa_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                Empresa Asignada
            </label>
            <select id="empresa_id" name="empresa_id" class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                <option value="">Sin empresa asignada</option>
                @foreach ($empresas as $emp)
                    <option value="{{ $emp->id }}" @selected(old('empresa_id', $user->empresa_id) == $emp->id)>
                        {{ $emp->nombre_comercial }} ({{ $emp->nit }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-1.5" :messages="$errors->get('empresa_id')" />
        </div>

        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                Contraseña {{ $user->exists ? '(dejar vacío para mantener)' : '*' }}
            </label>
            <input id="password" name="password" type="password" {{ $user->exists ? '' : 'required' }}
                   class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
            <x-input-error class="mt-1.5" :messages="$errors->get('password')" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                Confirmar Contraseña
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" {{ $user->exists ? '' : 'required' }}
                   class="w-full bg-slate-950/80 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
        </div>
    </div>

    <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
        <label for="activo" class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" id="activo" name="activo" value="1"
                   class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                   @checked(old('activo', $user->activo ?? true))>
            <div>
                <span class="block text-sm font-semibold text-white">Usuario Activo</span>
                <span class="block text-xs text-slate-300">Los usuarios inactivos no pueden iniciar sesión web ni recibir notificaciones operativas.</span>
            </div>
        </label>
        <x-input-error class="mt-1.5" :messages="$errors->get('activo')" />
    </div>

    <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3">
        <div>
            <h3 class="text-sm font-bold text-white">Roles Asignados (Multirrol)</h3>
            <p class="text-xs text-slate-300">Si se asignan múltiples roles, el usuario elegirá su rol activo al iniciar sesión.</p>
            <x-input-error class="mt-1.5" :messages="$errors->get('roles')" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($roles as $role)
                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-900 border border-slate-700 hover:border-indigo-500/50 cursor-pointer transition">
                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                               @checked(in_array($role->name, $assignedRoles, true))
                               class="w-4 h-4 rounded bg-slate-950 border-slate-600 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0">
                        <span class="text-xs font-bold text-white capitalize">{{ $role->name }}</span>
                    </div>
                    <span class="text-[10px] font-mono text-indigo-300">{{ $role->permissions_count }} perm.</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-4">
        <div>
            <h3 class="text-sm font-bold text-white">Permisos Directos (Excepcionales)</h3>
            <p class="text-xs text-slate-300">Se suman al rol activo del usuario independientemente del rol seleccionado en sesión.</p>
            <x-input-error class="mt-1.5" :messages="$errors->get('permissions')" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($permissionGroups as $group => $perms)
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-indigo-300 border-b border-slate-800 pb-1.5">{{ $group }}</h4>
                    <div class="space-y-1.5">
                        @foreach ($perms as $perm)
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-200 hover:text-white">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                       @checked(in_array($perm->name, $assignedDirectPermissions, true))
                                       class="w-3.5 h-3.5 rounded bg-slate-950 border-slate-600 text-amber-500 focus:ring-amber-500 focus:ring-offset-0">
                                <span class="font-mono text-[11px]">{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
