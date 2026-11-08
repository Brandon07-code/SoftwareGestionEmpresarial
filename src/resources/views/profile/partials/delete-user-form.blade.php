<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-rose-400">
            Zona de Riesgo: Eliminar Cuenta
        </h2>

        <p class="mt-1 text-xs text-slate-400">
            Una vez eliminada su cuenta de usuario, todos sus accesos serán revocados permanentemente. Esta acción no se puede deshacer.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-rose-600 hover:bg-rose-500"
    >Eliminar Cuenta</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-slate-900 text-slate-100 border border-slate-800 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-white">
                ¿Está seguro de que desea eliminar su cuenta?
            </h2>

            <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                Una vez eliminada la cuenta, todos sus recursos y datos asociados serán eliminados permanentemente. Por favor, ingrese su contraseña actual para confirmar la eliminación definitiva.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña de Confirmación" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Ingrese su contraseña actual"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="bg-rose-600 hover:bg-rose-500">
                    Confirmar Eliminación
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
