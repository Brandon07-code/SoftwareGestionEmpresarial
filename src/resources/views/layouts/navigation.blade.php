<nav x-data="{ open: false, jymOpen: false }" class="bg-slate-950/95 backdrop-blur-md border-b border-slate-800/80 text-white shadow-2xl sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="w-full max-w-[1600px] mx-auto px-3 sm:px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 w-full">
            
            <!-- Left: Logo & Desktop Links -->
            <div class="flex items-center gap-2 xl:gap-3 min-w-0">
                <!-- Logo Turnix ERP -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition hover:opacity-90">
                        <x-application-logo :dark="true" />
                    </a>
                </div>

                <!-- Desktop Navigation Links (Texto puro y limpio para cero desbordes) -->
                <div class="hidden lg:flex items-center space-x-0.5 xl:space-x-1 h-16">
                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                        Dashboard
                    </x-nav-link>

                    {{-- Productos --}}
                    @can('ver-productos')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                            Productos
                        </x-nav-link>
                    @endcan

                    {{-- Categorías --}}
                    @can('ver-categorias')
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                            Categorías
                        </x-nav-link>
                    @endcan

                    {{-- Ventas --}}
                    @can('ver-ventas')
                        <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                            Ventas
                        </x-nav-link>
                    @endcan

                    {{-- Clientes --}}
                    @can('ver-clientes')
                        <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                            Clientes
                        </x-nav-link>
                    @endcan

                    {{-- Compras (Clase 10) --}}
                    @can('ver-compras')
                        <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                            Compras
                        </x-nav-link>
                    @endcan

                    {{-- Proveedores (Clase 10) --}}
                    @can('ver-proveedores')
                        <x-nav-link :href="route('providers.index')" :active="request()->routeIs('providers.*')" class="text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap">
                            Proveedores
                        </x-nav-link>
                    @endcan



                    {{-- Configuración RBAC (visible como texto en pantallas 2xl+) --}}
                    @canany(['gestionar-usuarios', 'gestionar-roles', 'gestionar-permisos'])
                        <x-nav-link :href="route('configuracion.index')" :active="request()->routeIs('configuracion.*')" class="hidden 2xl:inline-flex text-[11px] xl:text-xs px-1.5 py-1 font-semibold whitespace-nowrap text-indigo-300">
                            Configuración
                        </x-nav-link>
                    @endcanany

                    {{-- Dropdown Servicios / Vertical Barbería JyM --}}
                    @canany(['ver-citas', 'ver-terceros', 'ver-pagos', 'ver-crm'])
                        @php
                            $isJymActive = request()->routeIs('citas.*', 'terceros.*', 'pagos.*', 'crm.*');
                        @endphp
                        <div class="relative flex items-center h-full" @click.outside="jymOpen = false">
                            <button @click="jymOpen = !jymOpen" type="button"
                                class="inline-flex items-center gap-1 px-1.5 xl:px-2 py-1 border-b-2 text-xs font-semibold transition whitespace-nowrap shrink-0 {{ $isJymActive ? 'border-cyan-400 text-cyan-300' : 'border-transparent text-slate-300 hover:text-white hover:border-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                <span>Servicios JyM</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': jymOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="jymOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute top-14 right-0 xl:left-0 w-64 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-2 z-50 backdrop-blur-xl" style="display: none;">
                                <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                                    Módulos Barbería & Belleza
                                </div>
                                @can('ver-citas')
                                    <a href="{{ route('citas.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">💈</span>
                                        <div>
                                            <div class="font-semibold">Turnos (Citas)</div>
                                            <div class="text-[10px] text-slate-400">Agenda y turnos de barberos</div>
                                        </div>
                                    </a>
                                @endcan
                                @can('ver-terceros')
                                    <a href="{{ route('terceros.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                        <span class="w-7 h-7 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center font-bold text-sm">👥</span>
                                        <div>
                                            <div class="font-semibold">Terceros (Directorio)</div>
                                            <div class="text-[10px] text-slate-400">Clientes, especialistas y roles</div>
                                        </div>
                                    </a>
                                @endcan
                                @can('ver-pagos')
                                    <a href="{{ route('pagos.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                        <span class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold text-sm">💳</span>
                                        <div>
                                            <div class="font-semibold">Pasarelas de Pago</div>
                                            <div class="text-[10px] text-slate-400">Recaudos QR Nequi y Wompi</div>
                                        </div>
                                    </a>
                                @endcan
                                @can('ver-crm')
                                    <a href="{{ route('crm.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800/80 hover:text-white transition">
                                        <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm">⭐</span>
                                        <div>
                                            <div class="font-semibold">CRM & Fidelización</div>
                                            <div class="text-[10px] text-slate-400">Kárdex de puntos y fidelidad</div>
                                        </div>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endcanany
                </div>
            </div>

            <!-- Right: Configuración, Notificaciones & User Dropdown (Desktop) -->
            <div class="hidden lg:flex items-center gap-2 xl:gap-2.5 ms-auto shrink-0">
                {{-- Acceso directo a Configuración RBAC (evita desborde horizontal en portátiles) --}}
                @canany(['gestionar-usuarios', 'gestionar-roles', 'gestionar-permisos'])
                    <a href="{{ route('configuracion.index') }}" title="Configuración RBAC (Usuarios, Roles y Permisos)"
                       class="p-2 rounded-xl text-slate-400 hover:text-indigo-300 hover:bg-slate-800 border {{ request()->routeIs('configuracion.*') ? 'border-indigo-500/50 bg-indigo-500/10 text-indigo-300' : 'border-slate-700/80' }} transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </a>
                @endcanany
                <!-- Campana de Notificaciones (Clase 14 - Requisito 14) -->
                @php
                    $unreadNotifications = Auth::user()->unreadNotifications()->take(5)->get();
                    $unreadCount = Auth::user()->unreadNotifications()->count();
                @endphp
                <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen" type="button" class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-700/80 transition focus:outline-none" title="Notificaciones del sistema">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-black text-white shadow-lg animate-pulse">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown de Notificaciones -->
                    <div x-show="notifOpen" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl py-3 z-50 overflow-hidden" style="display: none;">
                        <div class="px-4 pb-2.5 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-white uppercase tracking-wider">Notificaciones</span>
                                @if($unreadCount > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-black">
                                        {{ $unreadCount }} nuevas
                                    </span>
                                @endif
                            </div>
                            @if($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.markAllAsRead') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-slate-400 hover:text-indigo-400 font-medium transition">
                                        Marcar todas leídas
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-800/60">
                            @forelse($unreadNotifications as $notif)
                                <div class="p-3 hover:bg-slate-850/80 transition flex items-start gap-3">
                                    <div class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20 shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-white leading-tight">
                                            {{ $notif->data['title'] ?? $notif->data['product_name'] ?? 'Notificación del sistema' }}
                                        </p>
                                        <p class="text-[11px] text-slate-300 mt-0.5 leading-snug">
                                            {{ $notif->data['message'] ?? 'Stock crítico detectado.' }}
                                        </p>
                                        <div class="mt-1 flex items-center justify-between">
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                                            <form method="POST" action="{{ route('notifications.markAsRead', $notif->id) }}">
                                                @csrf
                                                <button type="submit" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 underline">
                                                    {{ isset($notif->data['product_id']) ? 'Ver producto →' : 'Ver detalle →' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 px-4 text-center">
                                    <div class="inline-flex p-2 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <p class="text-xs font-medium text-slate-300">¡Al día! No tienes alertas pendientes.</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">El inventario se encuentra en niveles normales.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="p-2.5 border-t border-slate-800 text-center bg-slate-950/40">
                            <a href="{{ route('notifications.index') }}" class="text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 transition">
                                Ver todas las notificaciones →
                            </a>
                        </div>
                    </div>
                </div>

                @php
                    $activeRole = Auth::user()->activeRole();
                @endphp
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2.5 py-1.5 border border-slate-700/80 text-xs font-semibold rounded-xl text-slate-200 bg-slate-800/80 hover:bg-slate-800 hover:text-white focus:outline-none transition shadow-sm shrink-0">
                            <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-300 font-bold flex items-center justify-center text-xs me-1.5 border border-indigo-500/40 shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[85px] 2xl:max-w-[130px] truncate">{{ Auth::user()->name }}</span>
                            <span class="ms-1.5 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 capitalize">
                                {{ $activeRole ? $activeRole->name : 'Sin rol' }}
                            </span>
                            <svg class="fill-current h-3.5 w-3.5 ms-1 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-slate-400 border-b border-slate-800">
                            Conectado como <strong class="text-white block truncate">{{ Auth::user()->email }}</strong>
                            <span class="inline-block mt-1 text-[11px] text-indigo-300 font-semibold capitalize">
                                Rol activo: {{ $activeRole ? $activeRole->name : 'Sin rol asignado' }}
                            </span>
                        </div>

                        @if(Auth::user()->hasMultipleRoles())
                            <div class="px-3 py-2 border-b border-slate-800 space-y-1">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">
                                    Cambiar rol activo
                                </div>
                                @foreach(Auth::user()->roles as $r)
                                    <form method="POST" action="{{ route('role.switch') }}">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $r->name }}">
                                        <button type="submit" class="w-full text-left px-2 py-1.5 rounded-lg text-xs capitalize flex items-center justify-between transition {{ $activeRole?->name === $r->name ? 'bg-indigo-600/25 text-indigo-200 font-bold border border-indigo-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                            <span>{{ $r->name }}</span>
                                            @if($activeRole?->name === $r->name)
                                                <span class="text-[10px] text-indigo-300">Activo</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif

                        @if(Auth::user()->activeRole()?->name === 'admin')
                            <x-dropdown-link :href="route('empresas.index')" class="text-indigo-300 font-semibold">
                                🏢 Empresas (Multiempresa)
                            </x-dropdown-link>
                        @endif

                        @canany(['gestionar-usuarios', 'gestionar-roles', 'gestionar-permisos'])
                            <x-dropdown-link :href="route('configuracion.index')" class="text-indigo-300 font-semibold">
                                ⚙️ Configuración RBAC
                            </x-dropdown-link>
                        @endcanany

                        <x-dropdown-link :href="route('profile.edit')" class="text-slate-300">
                            👤 Mi Perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-rose-400 font-semibold">
                                Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                <form method="POST" action="{{ route('logout') }}" class="inline-flex shrink-0">
                    @csrf
                    <button type="submit" title="Cerrar sesión activa" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-rose-500/15 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 transition shadow-sm shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Salir</span>
                    </button>
                </form>
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
        @if(Auth::user()->activeRole()?->name === 'admin')
            <x-responsive-nav-link :href="route('empresas.index')" :active="request()->routeIs('empresas.*')">
                🏢 Empresas (Multiempresa)
            </x-responsive-nav-link>
        @endif
        @canany(['gestionar-usuarios', 'gestionar-roles', 'gestionar-permisos'])
            <x-responsive-nav-link :href="route('configuracion.index')" :active="request()->routeIs('configuracion.*')">
                ⚙️ Configuración RBAC
            </x-responsive-nav-link>
        @endcanany

        @canany(['ver-citas', 'ver-terceros', 'ver-pagos', 'ver-crm'])
            <div class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 px-2 pt-3 pb-1 border-t border-slate-800">
                Servicios Barbería JyM
            </div>
            @can('ver-citas')
                <x-responsive-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.*')">
                    💈 Turnos (Citas)
                </x-responsive-nav-link>
            @endcan
            @can('ver-terceros')
                <x-responsive-nav-link :href="route('terceros.index')" :active="request()->routeIs('terceros.*')">
                    👥 Terceros (Directorio)
                </x-responsive-nav-link>
            @endcan
            @can('ver-pagos')
                <x-responsive-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.*')">
                    💳 Pasarelas (QR Nequi/Wompi)
                </x-responsive-nav-link>
            @endcan
            @can('ver-crm')
                <x-responsive-nav-link :href="route('crm.index')" :active="request()->routeIs('crm.*')">
                    ⭐ CRM (Fidelización)
                </x-responsive-nav-link>
            @endcan
        @endcanany

        <div class="pt-4 pb-2 border-t border-slate-800">
            <div class="px-2">
                <div class="font-medium text-sm text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
                <div class="mt-1 text-[11px] font-semibold text-indigo-300 capitalize">
                    Rol activo: {{ $activeRole ? $activeRole->name : 'Sin rol asignado' }}
                </div>
            </div>
            @if(Auth::user()->hasMultipleRoles())
                <div class="mt-2 px-2 space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cambiar rol activo</div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(Auth::user()->roles as $r)
                            <form method="POST" action="{{ route('role.switch') }}">
                                @csrf
                                <input type="hidden" name="role" value="{{ $r->name }}">
                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs capitalize {{ $activeRole?->name === $r->name ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800 text-slate-300 border border-slate-700' }}">
                                    {{ $r->name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="mt-3 space-y-1">
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <div class="px-3 py-1.5 text-xs text-rose-300 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center justify-between mb-2">
                        <span>🔔 {{ Auth::user()->unreadNotifications->count() }} alertas de inventario</span>
                        <form method="POST" action="{{ route('notifications.markAllAsRead') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[10px] text-rose-400 hover:text-white underline font-bold">Leídas</button>
                        </form>
                    </div>
                @endif
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
