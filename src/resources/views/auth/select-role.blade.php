<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center space-y-1.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                Sesión Multirrol
            </span>
            <h1 class="text-xl font-black text-white tracking-tight">
                Seleccionar Rol Activo
            </h1>
            <p class="text-xs text-slate-300">
                Tu usuario <strong class="text-white">{{ auth()->user()->name }}</strong> tiene varios roles asignados. Elige con cuál deseas operar en esta sesión.
            </p>
        </div>

        @if ($errors->any())
            <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300 font-medium">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('role.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-2.5">
                @foreach ($roles as $role)
                    <label class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 hover:border-indigo-500/60 cursor-pointer transition">
                        <input type="radio"
                               name="role"
                               value="{{ $role->name }}"
                               @checked(old('role', $currentRole ?? $roles->first()?->name) === $role->name)
                               class="mt-1 text-indigo-500 bg-slate-900 border-slate-700 focus:ring-indigo-500 focus:ring-offset-0"
                               required>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-bold text-white capitalize">{{ $role->name }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                    {{ $role->permissions->count() }} permisos
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Solo estarán habilitadas las acciones autorizadas para este rol (más tus permisos directos).
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 transition">
                Ingresar con el rol seleccionado →
            </button>
        </form>

        <div class="pt-3 border-t border-slate-800 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-semibold text-rose-400 hover:text-rose-300 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
