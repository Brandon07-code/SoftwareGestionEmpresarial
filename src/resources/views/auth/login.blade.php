<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-white tracking-tight">Acceso Corporativo</h1>
        <p class="text-xs text-slate-400 mt-1">Ingrese sus credenciales para acceder a Turnix ERP</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="usuario@test.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded bg-slate-900 border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs text-slate-400 font-medium">Recordar sesión</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-xs text-slate-400 hover:text-indigo-400 underline transition" href="{{ route('password.request') }}">
                    ¿Olvidó su contraseña?
                </a>
            @else
                <div></div>
            @endif

            <x-primary-button class="ms-3 bg-indigo-600 hover:bg-indigo-500">
                Iniciar Sesión
            </x-primary-button>
        </div>
    </form>

    <!-- Credenciales de Prueba Rápidas -->
    <div class="mt-8 pt-6 border-t border-slate-800/80">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2 flex items-center justify-between">
            <span>Usuarios de Demostración</span>
            <span class="text-indigo-400 font-mono text-[10px]">Clave: password</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="document.getElementById('email').value='admin@test.com';document.getElementById('password').value='password';" class="p-2 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-indigo-500/50 text-left transition">
                <span class="block font-bold text-white text-[11px]">Administrador</span>
                <span class="block text-[10px] text-slate-400 font-mono truncate">admin@test.com</span>
            </button>
            <button type="button" onclick="document.getElementById('email').value='vendedor@test.com';document.getElementById('password').value='password';" class="p-2 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-indigo-500/50 text-left transition">
                <span class="block font-bold text-white text-[11px]">Vendedor</span>
                <span class="block text-[10px] text-slate-400 font-mono truncate">vendedor@test.com</span>
            </button>
            <button type="button" onclick="document.getElementById('email').value='almacenista@test.com';document.getElementById('password').value='password';" class="p-2 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-indigo-500/50 text-left transition">
                <span class="block font-bold text-white text-[11px]">Almacenista</span>
                <span class="block text-[10px] text-slate-400 font-mono truncate">almacenista@test.com</span>
            </button>
            <button type="button" onclick="document.getElementById('email').value='auditor@test.com';document.getElementById('password').value='password';" class="p-2 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-indigo-500/50 text-left transition">
                <span class="block font-bold text-white text-[11px]">Auditor Interno</span>
                <span class="block text-[10px] text-slate-400 font-mono truncate">auditor@test.com</span>
            </button>
        </div>
    </div>
</x-guest-layout>
