<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-white tracking-tight">Recuperar Contraseña</h1>
        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
            ¿Olvidó su contraseña? No hay problema. Ingrese su correo electrónico corporativo y le enviaremos un enlace seguro para restablecerla.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="usuario@empresa.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-xs text-slate-400 hover:text-indigo-400 transition" href="{{ route('login') }}">
                ← Volver al Inicio de Sesión
            </a>

            <x-primary-button class="bg-indigo-600 hover:bg-indigo-500">
                Enviar Enlace
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
