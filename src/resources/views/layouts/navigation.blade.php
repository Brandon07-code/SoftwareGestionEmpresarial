<nav x-data="{ open: false }" class="bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80 text-white shadow-2xl sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center min-w-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center me-3 xl:me-6">
                    <a href="{{ route('dashboard') }}" class="transition hover:opacity-90">
                        <x-application-logo :dark="true" />
                    </a>
                </div>

                <!-- Navigation Links (Visibles en laptops y escritorios: lg+) -->
                <div class="hidden lg:flex items-center space-x-1 xl:space-x-2 h-16">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </x-nav-link>

                    @can('ver-productos')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Productos
                        </x-nav-link>
                    @endcan

                    @can('ver-categorias')
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Categorías
                        </x-nav-link>
                    @endcan

                    <x-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.*')">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Turnos
                    </x-nav-link>

                    <x-nav-link :href="route('terceros.index')" :active="request()->routeIs('terceros.*')">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Terceros
                    </x-nav-link>

                    <x-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.*')">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Pasarelas
                    </x-nav-link>

                    <x-nav-link :href="route('crm.index')" :active="request()->routeIs('crm.*')">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 me-1 xl:me-1.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        CRM
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (Visible solo en lg y superiores) -->
            <div class="hidden lg:flex items-center ms-2 xl:ms-4 shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2.5 py-1.5 border border-slate-700/80 text-xs xl:text-sm font-medium rounded-lg text-slate-200 bg-slate-800/80 hover:bg-slate-800 hover:text-white focus:outline-none transition ease-in-out duration-150 shadow-sm shrink-0">
                            <span class="w-6 h-6 rounded-full bg-indigo-500/20 text-indigo-300 font-bold flex items-center justify-center text-xs me-1.5 border border-indigo-500/40 shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[70px] xl:max-w-[130px] truncate">{{ Auth::user()->name }}</span>

                            <svg class="fill-current h-3.5 w-3.5 ms-1 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-slate-400 border-b border-slate-800/80">
                            Conectado como <strong class="text-white">{{ Auth::user()->email }}</strong>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-rose-400 font-medium">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button (Visible en pantallas < lg: móviles y tablets) -->
            <div class="-me-1 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:bg-slate-800 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (para pantallas menores a lg) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden bg-slate-900 border-t border-slate-800 px-4 pt-3 pb-4 space-y-1">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-responsive-nav-link>
        @can('ver-productos')
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                Productos
            </x-responsive-nav-link>
        @endcan
        @can('ver-categorias')
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                Categorías
            </x-responsive-nav-link>
        @endcan
        <x-responsive-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.*')">
            Turnos (Citas)
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('terceros.index')" :active="request()->routeIs('terceros.*')">
            Terceros (Clientes / Proveedores)
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.*')">
            Pasarelas (Recaudo Nequi/QR)
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('crm.index')" :active="request()->routeIs('crm.*')">
            CRM (Fidelización & Puntos)
        </x-responsive-nav-link>

        <div class="pt-4 pb-2 border-t border-slate-800">
            <div class="px-2">
                <div class="font-medium text-base text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-slate-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-2 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-slate-300">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-400">
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
