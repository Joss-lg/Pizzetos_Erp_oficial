<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzetos - Sistema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        svg { flex-shrink: 0; }
        .nav-link-active { @apply bg-black text-amber-400 shadow-lg; }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 overflow-x-hidden">

    {{-- Overlay con Blur --}}
    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:opacity.duration.300ms
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 z-40 backdrop-blur-sm">
    </div>

    <div class="min-h-screen flex flex-col">
        
        {{-- SIDEBAR --}}
        <aside 
            x-show="sidebarOpen"
            x-cloak
            @keydown.window.escape="sidebarOpen = false"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-amber-400 text-slate-900 sidebar-transition transform-gpu flex flex-col shadow-2xl border-r border-amber-500/20"
            x-transition:enter="sidebar-transition -translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="sidebar-transition translate-x-0"
            x-transition:leave-end="-translate-x-full">
            
            {{-- Logo Area --}}
            <div class="h-24 flex items-center justify-between px-6 border-b border-black/5 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-2xl shadow-sm">
                        <img src="{{ asset('pizzetos.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                    </div>
                    <span class="text-xl font-black italic tracking-tighter uppercase">Pizzetos</span>
                </div>
                <button @click="sidebarOpen = false" class="p-2 hover:bg-black/5 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Nav Links --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-1 scrollbar-hide">
                
                <p class="px-4 py-2 text-[10px] font-black text-black/40 uppercase tracking-[0.2em]">Operación</p>

                <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="text-sm">Inicio</span>
                </a>

                <a href="{{ route('ventas.pos') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('ventas.pos') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span class="text-sm">Venta POS</span>
                </a>

                <a href="{{ route('ventas.pedidos') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('ventas.pedidos') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">Repartidor</span>
                </a>

                <a href="{{ route('flujo.caja.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('flujo.caja.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-sm">Flujo Caja</span>
                </a>

                <a href="{{ route('ventas.resume') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('ventas.resume') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span class="text-sm">Historial</span>
                </a>

                @if(Auth::user()->id_ca == 1)
                    <p class="px-4 py-4 text-[10px] font-black text-black/40 uppercase tracking-[0.2em]">Administración</p>

                    <a href="{{ route('clientes.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('clientes.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm">Clientes</span>
                    </a>

                    <a href="{{ route('gastos.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('gastos.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm">Gastos</span>
                    </a>

                    <a href="{{ route('corte.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('corte.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-sm">Corte Mensual</span>
                    </a>

                    {{-- Catálogo Submenu --}}
                    <div x-data="{ open: {{ request()->is('productos/*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-black/5 font-bold transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span class="text-sm">Catálogo</span>
                            </div>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-collapse class="pl-12 pr-4 space-y-1 pb-2">
                            <a href="{{ route('pizzas.index') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Pizzas</a>
                            <a href="{{ route('alitas.index') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Alitas</a>
                            <a href="{{ route('hamburguesas.index') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Hamb/Cost</a>
                            <a href="{{ route('refrescos.index') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Bebidas</a>
                        </div>
                    </div>

                    {{-- Config Submenu --}}
                    <div x-data="{ open: {{ request()->is('recursos/*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-black/5 font-bold transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-sm">Configuración</span>
                            </div>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-collapse class="pl-12 pr-4 space-y-1">
                            <a href="{{ route('empleados.index') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Personal</a>
                            <a href="{{ route('sucursales.index') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Sucursales</a>
                            <a href="{{ route('ventas.configuracion') }}" @click="sidebarOpen = false" class="block py-2 text-xs font-black uppercase tracking-widest hover:text-white transition-colors">Sistema</a>
                        </div>
                    </div>
                @endif
            </nav>

            {{-- Logout --}}
            <div class="p-4 border-t border-black/5 shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-4 rounded-2xl bg-black text-white hover:bg-slate-800 transition-all font-black text-xs uppercase tracking-widest italic shadow-xl">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- HEADER SUPERIOR --}}
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 shrink-0 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="p-2.5 bg-amber-400 rounded-2xl text-slate-900 shadow-sm hover:scale-105 transition-all active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 6h16M4 12h16m-7 6h7"/></svg>
                </button>
                <div class="hidden md:block">
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic leading-none">Pizzetos Management</h2>
                    <p class="text-xs font-bold text-slate-600 mt-1">Pizzetos-POS</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Usuario</p>
                    <p class="text-sm font-black text-slate-900 uppercase italic leading-none">{{ Auth::user()->nombre }}</p>
                </div>
                <div class="h-11 w-11 bg-amber-400 rounded-2xl flex items-center justify-center font-black text-lg text-slate-900 border-2 border-white shadow-md">
                    {{ substr(Auth::user()->nombre, 0, 1) }}
                </div>
            </div>
        </header>

        {{-- CONTENIDO --}}
        <main class="flex-1 p-4 lg:p-8 overflow-y-auto scrollbar-hide">
            <div class="max-w-[1600px] mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>