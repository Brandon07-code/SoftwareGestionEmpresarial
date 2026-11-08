<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-white tracking-tight">Verificación de Correo</h1>
        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
            ¡Gracias por unirse a Turnix ERP! Antes de comenzar a operar, por favor verifique su dirección de correo electrónico haciendo clic en el enlace que acabamos de enviar a su bandeja de entrada.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
            Se ha enviado un nuevo enlace de verificación a la dirección de correo proporcionada.
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="bg-indigo-600 hover:bg-indigo-500">
                    Reenviar Correo
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-xs text-slate-400 hover:text-rose-400 transition">
                Cerrar Sesión
            </button>
        </form>
    </div>
</x-guest-layout>
