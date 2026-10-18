<nav x-data="{ open: false, jymOpen: false }" class="bg-slate-950/95 backdrop-blur-md border-b border-slate-800/80 text-white shadow-2xl sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 w-full">
            
            <!-- Left: Logo & Desktop Links -->
            <div class="flex items-center gap-2 sm:gap-3 xl:gap-5">
                <!-- Logo Turnix ERP -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition hover:opacity-90">
                        <x-application-logo :dark="true" />
                    </a>
                </div>

                <!-- Desktop Navigation Links (Texto puro y limpio para cero desbordes) -->
                <div class="hidden lg:flex items-center space-x-1 xl:space-x-2 h-16">
                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                        Dashboard
                    </x-nav-link>

                    {{-- Productos --}}
                    @can('ver-productos')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                            Productos
                        </x-nav-link>
                    @endcan

                    {{-- Categorías --}}
                    @can('ver-categorias')
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                            Categorías
                        </x-nav-link>
                    @endcan

                    {{-- Ventas --}}
                    @can('ver-ventas')
                        <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                            Ventas
                        </x-nav-link>
                    @endcan

                    {{-- Clientes --}}
                    @can('ver-clientes')
                        <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                            Clientes
                        </x-nav-link>
                    @endcan

                    {{-- Compras (Clase 10) --}}
                    @can('ver-compras')
                        <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                            Compras
                        </x-nav-link>
                    @endcan

                    {{-- Proveedores (Clase 10) --}}
                    @can('ver-proveedores')
                        <x-nav-link :href="route('providers.index')" :active="request()->routeIs('providers.*')" class="text-xs xl:text-sm px-2 py-1 font-semibold">
                            Proveedores
                        </x-nav-link>
                    @endcan

                    {{-- Dropdown Servicios / Vertical Barbería JyM --}}
                    @php
                        $isJymActive = request()->routeIs('citas.*', 'terceros.*', 'pagos.*', 'crm.*');
                    @endphp
                    <div class="relative flex items-center h-full" @click.outside="jymOpen = false">
                        <button @click="jymOpen = !jymOpen" type="button"
                            class="inline-flex items-center gap-1.5 px-2 py-1 border-b-2 text-xs xl:text-sm font-semibold transition whitespace-nowrap {{ $isJymActive ? 'border-cyan-400 text-cyan-300' : 'border-transparent text-slate-300 hover:text-white hover:border-slate-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                            <span>Servicios JyM</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': jymOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="jymOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-14 right-0 xl:left-0 w-64 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-2 z-50 backdrop-blur-xl" style="display: none;">
                            <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-800">
                                Módulos Barbería & Belleza
                            </div>
                            <a href="{{ route('citas.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">💈</span>
                                <div>
                                    <div class="font-semibold">Turnos (Citas)</div>
                                    <div class="text-[10px] text-slate-400">Agenda y turnos de barberos</div>
                                </div>
                            </a>
                            <a href="{{ route('terceros.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                <span class="w-7 h-7 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center font-bold text-sm">👥</span>
                                <div>
                                    <div class="font-semibold">Terceros (Party Model)</div>
                                    <div class="text-[10px] text-slate-400">Clientes, especialistas y roles</div>
                                </div>
                            </a>
                            <a href="{{ route('pagos.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                <span class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold text-sm">💳</span>
                                <div>
                                    <div class="font-semibold">Pasarelas de Pago</div>
                                    <div class="text-[10px] text-slate-400">Recaudos QR Nequi y Wompi</div>
                                </div>
                            </a>
                            <a href="{{ route('crm.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm">⭐</span>
                                <div>
                                    <div class="font-semibold">CRM & Fidelización</div>
                                    <div class="text-[10px] text-slate-400">Kárdex de puntos y fidelidad</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: User Dropdown (Desktop) -->
            <div class="hidden lg:flex items-center ms-auto shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2.5 py-1.5 border border-slate-700/80 text-xs font-semibold rounded-xl text-slate-200 bg-slate-800/80 hover:bg-slate-800 hover:text-white focus:outline-none transition shadow-sm shrink-0">
                            <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-300 font-bold flex items-center justify-center text-xs me-1.5 border border-indigo-500/40 shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[70px] xl:max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-3.5 w-3.5 ms-1 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-slate-400 border-b border-slate-800">
                            Conectado como <strong class="text-white block truncate">{{ Auth::user()->email }}</strong>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="text-slate-300">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-rose-400 font-semibold">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button (Mobile / Tablets < lg) -->
            <div class="-me-1 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Drawer Menu (Mobile & Tablets < lg) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden bg-slate-900 border-t border-slate-800 px-4 pt-3 pb-5 space-y-1 max-h-[85vh] overflow-y-auto">
        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-2 pb-1">
            Módulos del Sistema
        </div>
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
        @can('ver-ventas')
            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                Ventas
            </x-responsive-nav-link>
        @endcan
        @can('ver-clientes')
            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                Clientes
            </x-responsive-nav-link>
        @endcan
        @can('ver-compras')
            <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                Compras
            </x-responsive-nav-link>
        @endcan
        @can('ver-proveedores')
            <x-responsive-nav-link :href="route('providers.index')" :active="request()->routeIs('providers.*')">
                Proveedores
            </x-responsive-nav-link>
        @endcan

        <div class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 px-2 pt-3 pb-1 border-t border-slate-800">
            Servicios Barbería JyM
        </div>
        <x-responsive-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.*')">
            💈 Turnos (Citas)
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('terceros.index')" :active="request()->routeIs('terceros.*')">
            👥 Terceros (Party Model)
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.*')">
            💳 Pasarelas (QR Nequi/Wompi)
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('crm.index')" :active="request()->routeIs('crm.*')">
            ⭐ CRM (Fidelización)
        </x-responsive-nav-link>

        <div class="pt-4 pb-2 border-t border-slate-800">
            <div class="px-2">
                <div class="font-medium text-sm text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-slate-300">
                    Perfil
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-400 font-semibold">
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
