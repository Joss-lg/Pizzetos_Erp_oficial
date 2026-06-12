<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzetos - ERP</title>
    
    {{-- FAVICON --}}
    <link rel="icon" type="image/png" href="{{ asset('pizzetos2.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        svg { flex-shrink: 0; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Hacemos la scrollbar general más profesional para PC */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .logo-container:hover img { transform: rotate(-5deg) scale(1.1); }
        .logo-container img { transition: all 0.3s ease; }

        /* ==========================================================================
           SOLUCIÓN GLOBAL PARA TABLAS RESPONSIVAS
           Envuelve automáticamente el desbordamiento en móviles y suaviza el touch en iOS
           ========================================================================== */
        .responsive-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

    </style>
</head>

{{-- APPSHELL: Congelamos el body (h-screen overflow-hidden) para que solo el main tenga scroll --}}
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 h-screen overflow-hidden"
      x-data="{ 
          sidebarOpen: false, 
          sidebarExpanded: $persist(false), /* Magia: Guardamos el estado en LocalStorage */

          freezeSidebarScrollSave: false,
          sidebarNavScrollTop: parseInt(localStorage.getItem('sidebarNavScrollTop') || '0', 10),
          restoreSidebarScroll() {
              this.$nextTick(() => {
                  if (this.$refs.sidebarNav) {
                      const nav = this.$refs.sidebarNav;
                      nav.scrollTop = this.sidebarNavScrollTop || 0;

                      const activeLink = nav.querySelector('a.bg-black');
                      if (activeLink) {
                          const navRect = nav.getBoundingClientRect();
                          const activeRect = activeLink.getBoundingClientRect();
                          const centeredTop = nav.scrollTop + (activeRect.top - navRect.top) - Math.floor(nav.clientHeight * 0.35);
                          nav.scrollTop = Math.max(0, centeredTop);
                          this.sidebarNavScrollTop = nav.scrollTop;
                          localStorage.setItem('sidebarNavScrollTop', String(this.sidebarNavScrollTop));
                      }
                  }
              });
          },
          saveSidebarScroll(event) {
              if (this.freezeSidebarScrollSave) {
                  return;
              }
              this.sidebarNavScrollTop = event.target.scrollTop;
              localStorage.setItem('sidebarNavScrollTop', String(this.sidebarNavScrollTop));
          },
          handleSidebarNavigation(event) {
              if (this.$refs.sidebarNav) {
                  const nav = this.$refs.sidebarNav;
                  const target = event.currentTarget;
                  const navRect = nav.getBoundingClientRect();
                  const targetRect = target.getBoundingClientRect();
                  const offsetTop = targetRect.top - navRect.top;
                  const desiredTop = nav.scrollTop + offsetTop - Math.floor(nav.clientHeight * 0.35);

                  this.sidebarNavScrollTop = Math.max(0, desiredTop);
                  localStorage.setItem('sidebarNavScrollTop', String(this.sidebarNavScrollTop));
              }

              if (window.innerWidth < 1024) {
                  this.sidebarOpen = false;
                  return;
              }

              if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                  return;
              }

              const href = event.currentTarget.getAttribute('href');
              if (!href) {
                  return;
              }

              event.preventDefault();
              this.freezeSidebarScrollSave = true;
              this.sidebarExpanded = false;

              setTimeout(() => {
                  window.location.href = href;
              }, 320);
          }
      }">


    {{-- Overlay con Blur dinámico (Solo visible en móviles) --}}
    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:opacity.duration.300ms
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 z-40 backdrop-blur-sm lg:hidden">
    </div>

    {{-- CONTENEDOR MAESTRO: Ajusta el padding izquierdo dependiendo de la barra --}}
    <div class="h-screen flex flex-col transition-all duration-300 w-full" :class="sidebarExpanded ? 'lg:pl-60' : 'lg:pl-20'">
        

{{-- SIDEBAR LATERAL (Amarillo) --}}
        <aside 
            x-cloak

            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarExpanded ? 'w-60' : 'w-60 lg:w-20'
            ]"
            @keydown.window.escape="sidebarOpen = false"

            class="fixed inset-y-0 left-0 z-50 bg-amber-400 text-slate-900 transition-all duration-300 ease-in-out transform lg:translate-x-0 flex flex-col justify-between shadow-2xl border-r border-amber-500/20 overflow-hidden">
            
            {{-- Contenedor superior: Agrupa Logo y Navegación con Scroll propio --}}
            <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                
                {{-- Logo Area con Botón Integrado --}}
                <div class="h-20 lg:h-24 flex px-4 border-b border-black/5 shrink-0 transition-all items-center justify-between" :class="sidebarExpanded ? '' : 'lg:flex-col lg:items-center lg:justify-center lg:gap-2 lg:py-3'">
                    
                    <div class="flex items-center gap-2 logo-container" :class="sidebarExpanded ? '' : 'lg:justify-center'">
                        <div class="bg-white p-1.5 rounded-xl shadow-sm shrink-0">
                            <img src="{{ asset('pizzetos.png') }}" alt="Logo" class="h-7 w-7 lg:h-8 lg:w-8 object-contain">
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-lg font-black italic tracking-tighter uppercase whitespace-nowrap transition-opacity">Pizzetos</span>
                    </div>

                    {{-- Botón Toggle Escritorio --}}
                    <button @click="sidebarExpanded = !sidebarExpanded; if (sidebarExpanded) { freezeSidebarScrollSave = false; restoreSidebarScroll(); setTimeout(() => restoreSidebarScroll(), 220); }" class="hidden lg:flex p-1.5 bg-black/5 hover:bg-black/10 rounded-xl transition-colors text-slate-800 shrink-0" title="Expandir/Contraer">
                        <svg x-show="sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        <svg x-show="!sidebarExpanded" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </button>

                    {{-- Botón de cerrar (Oculto en PC, para móviles) --}}
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 hover:bg-black/5 rounded-xl transition-colors text-black">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Navegación Principal (Scrollable de forma segura) --}}
                <nav class="flex-1 overflow-y-auto overflow-x-hidden p-3 space-y-1 content-start" x-ref="sidebarNav" x-init="restoreSidebarScroll(); setTimeout(() => restoreSidebarScroll(), 220)" @scroll.passive="saveSidebarScroll($event)">
                    
                    <p :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="px-4 py-2 text-[10px] font-black text-black/40 uppercase tracking-[0.2em]">Operación</p>
                    <div :class="sidebarExpanded ? 'hidden' : 'hidden lg:block'" class="h-px w-8 bg-black/10 mx-auto my-4"></div>

                    <a href="{{ route('dashboard') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Inicio</span>
                    </a>

                    <a href="{{ route('ventas.pos') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('ventas.pos') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Venta POS</span>
                    </a>

                    <a href="{{ route('ventas.pedidos') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('ventas.pedidos') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Repartidor</span>
                    </a>

                    <a href="{{ route('especiales.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('especiales.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Especiales</span>
                    </a>

                    <a href="{{ route('flujo.caja.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('flujo.caja.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Flujo Caja</span>
                    </a>

                    <a href="{{ route('gastos.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('gastos.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.5 1M12 8V7m0 1c-1.11 0-2.08-.407-2.5-1M12 8V9m0 7v1m0-1c-1.11 0-2.08-.407-2.5-1M12 16v-1m0 1c1.11 0 2.08.407 2.5 1M12 16V15" /><circle cx="12" cy="12" r="10" /></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Gastos</span>
                    </a>

                    <a href="{{ route('ventas.resume') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('ventas.resume') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Historial</span>
                    </a>

                    <a href="{{ route('clientes.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('clientes.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Clientes</span>
                    </a>

                    {{-- SECCIÓN DE ADMINISTRACIÓN RESTRINGIDA --}}
                    @if(Auth::user()->id_ca == 1)
                        @php
                            $catActive = request()->routeIs('categorias.*') || request()->routeIs('pizzas.*') || request()->routeIs('especialidades.*');
                            $prodActive = request()->routeIs('alitas.*') || request()->routeIs('costillas.*') || request()->routeIs('hamburguesas.*') || request()->routeIs('papas.*') || request()->routeIs('mariscos.*') || request()->routeIs('rectangular.*') || request()->routeIs('spaguetty.*') || request()->routeIs('refrescos.*') || request()->routeIs('barra.*');
                            $ajustesActive = request()->routeIs('empleados.*') || request()->routeIs('cargos.*') || request()->routeIs('sucursales.*') || request()->routeIs('ventas.configuracion') || request()->is('Conf*');
                        @endphp
                        <p :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="px-4 py-4 text-[10px] font-black text-black/40 uppercase tracking-[0.2em]">Administración</p>
                        <div :class="sidebarExpanded ? 'hidden' : 'hidden lg:block'" class="h-px w-8 bg-black/10 mx-auto my-4"></div>

                        <a href="{{ route('corte.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('corte.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 font-bold' }}" :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Corte Mensual</span>
                        </a>

                        {{-- 1. MENÚ DESPLEGABLE: CATEGORÍAS --}}
                        <div x-data="{ openCat: @js($catActive) }">
                            <button @click="if(window.innerWidth >= 1024 && !sidebarExpanded) { sidebarExpanded = true; openCat = true; } else { openCat = !openCat; }" class="w-full flex items-center px-4 py-3 rounded-xl font-bold transition-all {{ $catActive ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 text-slate-900' }}" :class="sidebarExpanded ? 'justify-between' : 'justify-start lg:justify-center'">
                                <div class="flex items-center" :class="sidebarExpanded ? 'gap-3' : 'gap-3 lg:gap-0'">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Categorías</span>
                                </div>
                                <svg :class="[sidebarExpanded ? 'block' : 'block lg:hidden', openCat ? 'rotate-180' : '']" class="w-4 h-4 transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openCat && (sidebarExpanded || window.innerWidth < 1024)" x-cloak x-collapse class="pl-8 pr-4 space-y-1 pb-2 mt-1 whitespace-nowrap lg:pl-8" :class="sidebarExpanded ? '' : 'lg:hidden'">
                                <a href="{{ route('categorias.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('categorias.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">General</a>
                                <a href="{{ route('pizzas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('pizzas.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Pizzas</a>
                                <a href="{{ route('especialidades.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('especialidades.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Especialidades</a>
                            </div>
                        </div>

                        {{-- 2. MENÚ DESPLEGABLE: PRODUCTOS --}}
                        <div x-data="{ openProd: @js($prodActive) }">
                            <button @click="if(window.innerWidth >= 1024 && !sidebarExpanded) { sidebarExpanded = true; openProd = true; } else { openProd = !openProd; }" class="w-full flex items-center px-4 py-3 rounded-xl font-bold transition-all {{ $prodActive ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 text-slate-900' }}" :class="sidebarExpanded ? 'justify-between' : 'justify-start lg:justify-center'">
                                <div class="flex items-center" :class="sidebarExpanded ? 'gap-3' : 'gap-3 lg:gap-0'">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Productos</span>
                                </div>
                                <svg :class="[sidebarExpanded ? 'block' : 'block lg:hidden', openProd ? 'rotate-180' : '']" class="w-4 h-4 transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <div x-show="openProd && (sidebarExpanded || window.innerWidth < 1024)" x-cloak x-collapse class="pl-8 pr-4 space-y-1 pb-2 mt-1 whitespace-nowrap lg:pl-8" :class="sidebarExpanded ? '' : 'lg:hidden'">
                                <a href="{{ route('alitas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('alitas.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Alitas</a>
                                <a href="{{ route('costillas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('costillas.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Costillas</a>
                                <a href="{{ route('hamburguesas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('hamburguesas.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Hamburguesas</a>
                                <a href="{{ route('papas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('papas.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Papas</a>
                                <a href="{{ route('mariscos.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('mariscos.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Mariscos</a>
                                <a href="{{ route('rectangular.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('rectangular.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Rectangular</a>
                                <a href="{{ route('spaguetty.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('spaguetty.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Spaguetty</a>
                                <a href="{{ route('refrescos.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('refrescos.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Refrescos</a>
                                <a href="{{ route('barra.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('barra.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Barra</a>
                            </div>
                        </div>

                        {{-- 3. MENÚ DESPLEGABLE: AJUSTES --}}
                        <div x-data="{ openAjustes: @js($ajustesActive) }">
                            <button @click="if(window.innerWidth >= 1024 && !sidebarExpanded) { sidebarExpanded = true; openAjustes = true; } else { openAjustes = !openAjustes; }" class="w-full flex items-center px-4 py-3 rounded-xl font-bold transition-all {{ $ajustesActive ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/5 text-slate-900' }}" :class="sidebarExpanded ? 'justify-between' : 'justify-start lg:justify-center'">
                                <div class="flex items-center" :class="sidebarExpanded ? 'gap-3' : 'gap-3 lg:gap-0'">
                                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 28 28" fill="currentColor"><path clip-rule="evenodd" d="M14 20C17.3137 20 20 17.3137 20 14C20 10.6863 17.3137 8 14 8C10.6863 8 8 10.6863 8 14C8 17.3137 10.6863 20 14 20ZM18 14C18 16.2091 16.2091 18 14 18C11.7909 18 10 16.2091 10 14C10 11.7909 11.7909 10 14 10C16.2091 10 18 11.7909 18 14Z" fill-rule="evenodd"/><path clip-rule="evenodd" d="M0 12.9996V14.9996C0 16.5478 1.17261 17.822 2.67809 17.9826C2.80588 18.3459 2.95062 18.7011 3.11133 19.0473C2.12484 20.226 2.18536 21.984 3.29291 23.0916L4.70712 24.5058C5.78946 25.5881 7.49305 25.6706 8.67003 24.7531C9.1044 24.9688 9.55383 25.159 10.0163 25.3218C10.1769 26.8273 11.4511 28 12.9993 28H14.9993C16.5471 28 17.8211 26.8279 17.9821 25.3228C18.4024 25.175 18.8119 25.0046 19.2091 24.8129C20.3823 25.6664 22.0344 25.564 23.0926 24.5058L24.5068 23.0916C25.565 22.0334 25.6674 20.3813 24.814 19.2081C25.0054 18.8113 25.1757 18.4023 25.3234 17.9824C26.8282 17.8211 28 16.5472 28 14.9996V12.9996C28 11.452 26.8282 10.1782 25.3234 10.0169C25.1605 9.55375 24.9701 9.10374 24.7541 8.66883C25.6708 7.49189 25.5882 5.78888 24.5061 4.70681L23.0919 3.29259C21.9846 2.18531 20.2271 2.12455 19.0485 3.1103C18.7017 2.94935 18.3459 2.80441 17.982 2.67647C17.8207 1.17177 16.5468 0 14.9993 0H12.9993C11.4514 0 10.1773 1.17231 10.0164 2.6775C9.60779 2.8213 9.20936 2.98653 8.82251 3.17181C7.64444 2.12251 5.83764 2.16276 4.70782 3.29259L3.2936 4.7068C2.16377 5.83664 2.12352 7.64345 3.17285 8.82152C2.98737 9.20877 2.82199 9.60763 2.67809 10.0167C1.17261 10.1773 0 11.4515 0 12.9996ZM15.9993 3C15.9993 2.44772 15.5516 2 14.9993 2H12.9993C12.447 2 11.9993 2.44772 11.9993 3V3.38269C11.9993 3.85823 11.6626 4.26276 11.2059 4.39542C10.4966 4.60148 9.81974 4.88401 9.18495 5.23348C8.76836 5.46282 8.24425 5.41481 7.90799 5.07855L7.53624 4.70681C7.14572 4.31628 6.51256 4.31628 6.12203 4.7068L4.70782 6.12102C4.31729 6.51154 4.31729 7.14471 4.70782 7.53523L5.07958 7.90699C5.41584 8.24325 5.46385 8.76736 5.23451 9.18395C4.88485 9.8191 4.6022 10.4963 4.39611 11.2061C4.2635 11.6629 3.85894 11.9996 3.38334 11.9996H3C2.44772 11.9996 2 12.4474 2 12.9996V14.9996C2 15.5519 2.44772 15.9996 3 15.9996H3.38334C3.85894 15.9996 4.26349 16.3364 4.39611 16.7931C4.58954 17.4594 4.85042 18.0969 5.17085 18.6979C5.39202 19.1127 5.34095 19.6293 5.00855 19.9617L4.70712 20.2632C4.3166 20.6537 4.3166 21.2868 4.70712 21.6774L6.12134 23.0916C6.51186 23.4821 7.14503 23.4821 7.53555 23.0916L7.77887 22.8483C8.11899 22.5081 8.65055 22.4633 9.06879 22.7008C9.73695 23.0804 10.4531 23.3852 11.2059 23.6039C11.6626 23.7365 11.9993 24.1411 11.9993 24.6166V25C11.9993 25.5523 12.447 26 12.9993 26H14.9993C15.5516 26 15.9993 25.5523 15.9993 25V24.6174C15.9993 24.1418 16.3361 23.7372 16.7929 23.6046C17.5032 23.3985 18.1809 23.1157 18.8164 22.7658C19.233 22.5365 19.7571 22.5845 20.0934 22.9208L20.2642 23.0916C20.6547 23.4821 21.2879 23.4821 21.6784 23.0916L23.0926 21.6774C23.4831 21.2868 23.4831 20.6537 23.0926 20.2632L22.9218 20.0924C22.5855 19.7561 22.5375 19.232 22.7669 18.8154C23.1166 18.1802 23.3992 17.503 23.6053 16.7931C23.7379 16.3364 24.1425 15.9996 24.6181 15.9996H25C25.5523 15.9996 26 15.5519 26 14.9996V12.9996C26 12.4474 25.5523 11.9996 25 11.9996H24.6181C24.1425 11.9996 23.7379 11.6629 23.6053 11.2061C23.3866 10.4529 23.0817 9.73627 22.7019 9.06773C22.4643 8.64949 22.5092 8.11793 22.8493 7.77781L23.0919 7.53523C23.4824 7.14471 23.4824 6.51154 23.0919 6.12102L21.6777 4.7068C21.2872 4.31628 20.654 4.31628 20.2635 4.7068L19.9628 5.00748C19.6304 5.33988 19.1137 5.39096 18.6989 5.16979C18.0976 4.84915 17.4596 4.58815 16.7929 4.39467C16.3361 4.2621 15.9993 3.85752 15.9993 3.38187V3Z" fill-rule="evenodd"/></svg>
                                    <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-sm font-bold uppercase italic tracking-tighter whitespace-nowrap">Ajustes</span>
                                </div>
                                <svg :class="[sidebarExpanded ? 'block' : 'block lg:hidden', openAjustes ? 'rotate-180' : '']" class="w-4 h-4 transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openAjustes && (sidebarExpanded || window.innerWidth < 1024)" x-cloak x-collapse class="pl-12 pr-4 space-y-1 pb-2 mt-1 whitespace-nowrap lg:pl-12" :class="sidebarExpanded ? '' : 'lg:hidden'">
                                <a href="{{ route('empleados.index') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('empleados.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Personal</a>
                                <a href="{{ route('cargos.index') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('cargos.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Cargos</a>
                                <a href="{{ route('sucursales.index') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('sucursales.*') ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Sucursales</a>
                                <a href="{{ route('ventas.configuracion') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ (request()->routeIs('ventas.configuracion') || request()->is('Conf*')) ? 'bg-black text-amber-400 shadow-sm' : 'text-black hover:bg-black/5 hover:translate-x-1' }}">Sistema</a>
                            </div>
                        </div>
                    @endif
                </nav>
            </div>

            {{-- Logout Footer --}}
            <div class="sticky bottom-0 z-10 p-3 border-t border-black/5 shrink-0 bg-amber-400" style="padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-3 py-3.5 rounded-xl bg-black text-white hover:bg-slate-800 transition-all font-black text-[10px] uppercase tracking-widest italic shadow-xl" :class="sidebarExpanded ? 'gap-2' : 'gap-2 lg:gap-0'">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'">Cerrar Sesión</span>

                    </button>
                </form>
            </div>
        </aside>


        {{-- Contenedor Principal Derecho (con el Header y el @yield) --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300 relative w-full">
            
            {{-- HEADER (Blanco) --}}
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-10 shrink-0 sticky top-0 z-30 shadow-sm transition-all duration-300 w-full">
                <div class="flex items-center gap-4">
                    
                    {{-- Botón Hamburger Solo para Móviles (Abre el sidebar flotante) --}}
                    <button @click="sidebarOpen = true" class="lg:hidden p-2.5 bg-amber-400 rounded-2xl text-slate-900 shadow-sm hover:scale-105 transition-all active:scale-95">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>

                    <div class="hidden md:block">
                        <h2 class="text-[10px] font-black text-slate-400 tracking-[0.3em] italic leading-none uppercase">Pizzetos Management</h2>
                        <p class="text-xs font-bold text-slate-600 mt-1 italic tracking-tighter">By Ollintem Sistema POS</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Usuario Activo</p>
                        <p class="text-sm font-black text-gray-900 uppercase italic leading-none tracking-tighter">{{ Auth::user()->nombre ?? 'Admin' }}</p>
                    </div>
                    <div class="h-11 w-11 bg-amber-400 rounded-2xl flex items-center justify-center font-black text-lg text-slate-900 border-2 border-white shadow-md">
                        {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                    </div>
                </div>
            </header>

            {{-- MAIN CONTENT: Aquí ocurre el scroll interno --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#f8fafc] relative w-full">
                <div class="p-4 lg:p-8 max-w-[1600px] mx-auto w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>