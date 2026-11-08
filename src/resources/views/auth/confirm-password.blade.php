<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-white tracking-tight">Confirmar Identidad</h1>
        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
            Esta es un área protegida y sensible del ERP. Por favor, confirme su contraseña actual antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Contraseña de Seguridad" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-500">
                Confirmar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
