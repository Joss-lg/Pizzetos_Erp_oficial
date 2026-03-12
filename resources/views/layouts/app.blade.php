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
    </style>
</head>
<body class="bg-[#f4f4f4] font-sans antialiased overflow-x-hidden">

    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:opacity
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/40 z-40 backdrop-blur-sm">
    </div>

    <div class="min-h-screen flex flex-col">
        
        <aside 
            x-show="sidebarOpen"
            x-cloak
            x-transition:enter="sidebar-transition -translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="sidebar-transition translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="w-80 bg-amber-400 text-black flex flex-col fixed h-full z-50 shadow-2xl overflow-y-auto">
            
            <div class="p-10 text-center border-b border-black/10 relative shrink-0">
                <button @click="sidebarOpen = false" class="absolute top-4 right-4 p-2 hover:bg-black/10 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="bg-white p-3 rounded-full shadow-xl inline-block mb-4">
                    <img src="{{ asset('pizzetos.png') }}" alt="Logo" class="h-16 w-16 object-contain">
                </div>
                <h1 class="text-2xl font-black italic tracking-tighter uppercase leading-none">Pizzetos</h1>
            </div>

            <nav class="flex-1 px-8 py-10 space-y-2">
                
                {{-- 1. INICIO --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('dashboard') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 576 512"><path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c.2 35.5-28.5 64.3-64 64.3H128.1c-35.3 0-64-28.7-64-64V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c12.2-11.4 31.2-11.4 43.4 0l256 223.5c6.5 7.4 10 15.4 10 24zM396 288H180v160h216V288z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Inicio</span>
                </a>

                {{-- 2. RESUMEN (HISTORIAL) --}}
                <a href="{{ route('ventas.resume') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('ventas.resume') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 384 512"><path d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM112 192H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Historial</span>
                </a>

                {{-- 3. FLUJO CAJA --}}
                <a href="{{ route('flujo.caja.index') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('flujo.caja.*') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H448c17.7 0 32-14.3 32-32s-14.3-32-32-32H64zM416 272a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Flujo Caja</span>
                </a>

                {{-- 4. VENTA POS --}}
                <a href="{{ route('ventas.pos') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('ventas.pos') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 576 512"><path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Venta POS</span>
                </a>

                {{-- 5. PEDIDOS --}}
                <a href="{{ route('ventas.pedidos') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('ventas.pedidos') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M256 0C114.6 0 0 114.6 0 256c0 105.1 63.6 195 155.6 235.6c11 4.8 23.5-3.3 23.5-15.4V432c0-26.5 21.5-48 48-48h57.8c26.5 0 48-21.5 48-48V288c0-26.5-21.5-48-48-48H138.5c-20 0-38.1-12.7-44.5-31.9C85.7 183.1 161.4 160 256 160c94.6 0 170.3 23.1 162.1 48.1c-6.4 19.3-24.5 31.9-44.5 31.9H288c-26.5 0-48 21.5-48 48v48c0 26.5 21.5 48 48 48h44.2v44.2c0 12.1 12.5 20.2 23.5 15.4C448.4 451 512 361.1 512 256C512 114.6 397.4 0 256 0z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Repartidor</span>
                </a>

                {{-- 6. ANTICIPOS --}}
                <a href="{{ route('ventas.anticipos') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('ventas.anticipos') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 576 512"><path d="M312 24V34.5c6.4 1.2 12.6 2.7 18.2 4.2c12.8 3.4 20.4 16.6 17 29.4s-16.6 20.4-29.4 17c-10.9-2.9-21.1-4.9-30.2-5c-7.3-.1-14.7 1.7-19.4 4.4c-2.1 1.3-3.1 2.4-3.5 3c-.3 .5-.7 1.2-.7 2.8c0 .3 0 .5 0 .6c.2 .2 .9 1.2 3.3 2.6c5.8 3.5 14.4 6.2 27.4 10.1l.9 .3c11.1 3.3 25.9 7.8 37.9 15.3c13.7 8.6 26.1 22.9 26.4 44.9c.3 22.5-11.4 38.9-26.7 48.5c-6.7 4.1-13.9 7-21.3 8.8V232c0 13.3-10.7 24-24 24s-24-10.7-24-24V220.6c-9.5-2.3-18.2-5.3-25.6-7.8c-12.1-4.1-18.6-17.3-14.5-29.4s17.3-18.6 29.4-14.5c3.7 1.3 8.2 2.8 13.5 4.3c7.5 2.1 15 3.3 21 3.5c7.5 .2 15.1-1.6 20-4.6c2.4-1.4 3.6-2.8 4.1-3.6c.5-.9 .9-1.9 .8-3.5c0-.4-.1-.7-.2-.9c-.3-.4-1.2-1.5-3.9-3.1c-6.3-3.8-15.5-6.6-28.9-10.5l-.9-.3c-11.1-3.3-25.9-7.8-37.9-15.3c-13.7-8.6-26.1-22.9-26.4-44.9c-.3-22.5 11.4-38.9 26.7-48.5c6.5-4 13.5-6.8 20.8-8.6V24c0-13.3 10.7-24 24-24s24 10.7 24 24zM568.2 336.3c13.1 17.8 9.3 42.8-8.5 55.9L433.1 485.5c-23.4 17.2-51.6 26.5-80.7 26.5H192 32c-17.7 0-32-14.3-32-32V416c0-17.7 14.3-32 32-32H68.8l44.9-36c22.7-18.2 50.9-28 80-28H272h16 64c17.7 0 32 14.3 32 32s-14.3 32-32 32H288 272c-8.8 0-16 7.2-16 16s7.2 16 16 16H392.6l119.7-88.2c17.8-13.1 42.8-9.3 55.9 8.5zM193.6 384l0 0-.9 0c.3 0 .6 0 .9 0z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Anticipos</span>
                </a>

                {{-- 7. GASTOS --}}
                <a href="{{ route('gastos.index') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('gastos.*') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 576 512"><path d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm64 320H64V336c35.3 0 64 28.7 64 64zM64 192V128h64c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64v64H448zm64-192c-35.3 0-64-28.7-64-64h64v64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Gastos</span>
                </a>

                {{-- 8. PRODUCTOS (Desplegable con Iconos Temáticos) --}}
                <div x-data="{ productosOpen: {{ request()->is('productos/*') ? 'true' : 'false' }} }" class="w-full">
                    <button @click="productosOpen = !productosOpen" 
                            class="w-full flex items-center justify-between px-6 py-4 rounded-[2rem] transition-all hover:bg-black/10 font-black">
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M290.7 311L95 269.7 86.8 309l195.7 41.3c4.1-13.3 8-26.3 8.2-39.3zm211-132.3L275.5 28.5C266.3 10.1 245.8 1 224.2 1H214.5c-20 0-38.6 10.9-48.4 28.5l-153 273.6c-9.1 16.3-10.7 35.8-4.4 53.4C14.1 372 26 385 41.7 392l253.3 113.3c15 6.7 32 8 47.7 4 15.7-4 29-13.6 38.3-27L510.5 220.5c10.4-15 13-33.8 7-50.5-6-16.7-18.4-30-34.8-37.3zM151 228c-13.3 0-24-10.7-24-24s10.7-24 24-24 24 10.7 24 24-10.7 24-24 24zm84-76c-13.3 0-24-10.7-24-24s10.7-24 24-24 24 10.7 24 24-10.7 24-24 24zm88 76c-13.3 0-24-10.7-24-24s10.7-24 24-24 24 10.7 24 24-10.7 24-24 24z"/></svg>
                            <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Productos</span>
                        </div>
                        <svg :class="{'rotate-180': productosOpen}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="productosOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         x-cloak 
                         class="mt-2 px-2 flex flex-col space-y-1 pb-4">
                        
                        <a href="{{ route('pizzas.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('pizzas.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M290.7 311L95 269.7 86.8 309l195.7 41.3c4.1-13.3 8-26.3 8.2-39.3zm211-132.3L275.5 28.5C266.3 10.1 245.8 1 224.2 1H214.5c-20 0-38.6 10.9-48.4 28.5l-153 273.6c-9.1 16.3-10.7 35.8-4.4 53.4C14.1 372 26 385 41.7 392l253.3 113.3c15 6.7 32 8 47.7 4 15.7-4 29-13.6 38.3-27L510.5 220.5c10.4-15 13-33.8 7-50.5-6-16.7-18.4-30-34.8-37.3z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('pizzas.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Pizzas</span>
                        </a>
                        <a href="{{ route('alitas.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('alitas.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M441 58.9A105.7 105.7 0 0 0 291.6 59l-190.5 190.5-47.5-47.5C39.4 187.8 16 195 16 215.1c0 8.4 3.4 16.5 9.4 22.4l50.2 50.2c-15.6 18.3-25 41.5-25 66.8 0 57.4 46.6 104 104 104 25.4 0 48.6-9.5 66.8-25l50.2 50.2c5.9 6 14 9.4 22.4 9.4 20 0 27.2-23.4 13-37.6l-47.5-47.5L441 208.5c41.4-41.5 41.4-108.3 0-149.6z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('alitas.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Alitas</span>
                        </a>
                        <a href="{{ route('costillas.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('costillas.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M512 256c0-114.9-93.1-208-208-208c-39.4 0-76.3 11-108.3 30.1c-17.7 10.6-39.6 6.8-52.6-9.5L132.8 55c-11.2-14.1-31.5-16.1-45.5-4.5L52.8 79.9C19.7 108.5 0 150.3 0 195.4v13.2c0 23.9 14.1 45.1 35.8 54.1l203.2 84c37.5 15.5 56.4 57.3 42.4 95.5l-4.5 12.3c-5.8 15.8 3.5 33 19.8 37.1l36.9 9.2c16.5 4.1 33.6-5.8 38-22.2l5.1-19.1C392.8 400.9 512 341 512 256z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('costillas.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Costillas</span>
                        </a>
                        <a href="{{ route('hamburguesas.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('hamburguesas.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M464 256H48a48 48 0 0 0 0 96h416a48 48 0 0 0 0-96zm16 128H32a16 16 0 0 0-16 16v16a64 64 0 0 0 64 64h352a64 64 0 0 0 64-64v-16a16 16 0 0 0-16-16zM58.6 224h394.8c34.6 0 54.6-43.9 34.8-75.9C448.9 84.4 358.5 32 256 32S63.1 84.4 23.8 148.1c-19.8 32 .2 75.9 34.8 75.9z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('hamburguesas.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Hamburguesas</span>
                        </a>
                        <a href="{{ route('magno.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('magno.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M256 0c11 0 20 9 20 20v65.4l82.4-78.5c7.9-7.5 20.3-6.5 27.1 2.3l48 64c5.9 7.9 5.3 19.1-1.4 26.3L371.5 160h44.5c11 0 20 9 20 20v288c0 24.3-19.7 44-44 44H120c-24.3 0-44-19.7-44-44V180c0-11 9-20 20-20h44.5L79.9 99.5c-6.7-7.2-7.3-18.4-1.4-26.3l48-64c6.8-8.8 19.2-9.8 27.1-2.3L236 85.4V20c0-11 9-20 20-20z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('magno.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Magno</span>
                        </a>
                        <a href="{{ route('papas.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('papas.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M106.5 180.1L81 78C75.2 55.1 52.4 41 29.5 46.8s-37 28.5-31.2 51.4l50.5 197c-9.1 12.3-13.8 27.5-12.8 43.1C38.3 365.1 60.1 384 86.8 384H425.2c26.7 0 48.5-18.9 50.8-45.7c1-15.6-3.7-30.8-12.8-43.1l50.5-197c5.8-22.9-8.3-45.6-31.2-51.4s-45.7 14.1-51.5 37l-25.5 102.1L365.2 62.4C355.4 34.3 325 21 297 30.7s-41.2 36-31.4 64.1l29.8 85.5-56-118c-9.9-20.9-33.8-29.8-54.7-19.9s-29.8 33.8-19.9 54.7l46 96.9-57.9-74c-14.3-18.3-40.8-21.5-59.1-7.1s-21.5 40.8-7.1 59.1l60 76.6zM64 416v32c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V416H64z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('papas.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Papas</span>
                        </a>
                        <a href="{{ route('mariscos.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('mariscos.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M495 186.2C449.6 142.9 388 128 388 128c-98.3 0-191 38.6-261.2 105.4L64 160C28.7 160 0 188.7 0 224v128c0 35.3 28.7 64 64 64l62.8-73.4C197 409.4 289.7 448 388 448c0 0 61.6-14.9 107-58.2C508.8 376.5 512 355.2 512 336v-96c0-19.2-3.2-40.5-17-53.8zM240 256c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('mariscos.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Mariscos</span>
                        </a>
                        <a href="{{ route('rectangular.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('rectangular.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('rectangular.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Rectangular</span>
                        </a>
                        <a href="{{ route('refrescos.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('refrescos.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 384 512"><path d="M192 512C86 512 0 426 0 320C0 228.8 130.2 57.7 166.6 11.7C172.6 4.2 181.5 0 191.1 0h1.8c9.6 0 18.5 4.2 24.5 11.7C253.8 57.7 384 228.8 384 320c0 106-86 192-192 192z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('refrescos.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Refrescos</span>
                        </a>
                        <a href="{{ route('spaguetty.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('spaguetty.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M0 256C0 397.4 114.6 512 256 512s256-114.6 256-256S397.4 0 256 0 0 114.6 0 256zm128-48a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm224 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-128 32a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('spaguetty.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Spaguetty</span>
                        </a>
                        <a href="{{ route('especialidades.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('especialidades.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M315.4 15.5C309.7 5.9 299.2 0 288 0s-21.7 5.9-27.4 15.5l-96 160c-5.9 9.9-6.1 22.2-.4 32.2s16.3 16.2 27.8 16.2H384c11.5 0 22.2-6.2 27.8-16.2s5.5-22.3-.4-32.2l-96-160zM288 312V456c0 22.1 17.9 40 40 40H472c22.1 0 40-17.9 40-40V312c0-22.1-17.9-40-40-40H328c-22.1 0-40 17.9-40 40zM128 512a128 128 0 1 0 0-256 128 128 0 1 0 0 256z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('especialidades.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Especialidad</span>
                        </a>
                        <a href="{{ route('barra.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('barra.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M448 256c0-106-86-192-192-192V448c106 0 192-86 192-192zm64 0c0 141.4-114.6 256-256 256S0 397.4 0 256 114.6 0 256 0 512 114.6 512 256z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('barra.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Barra</span>
                        </a>
                    </div>
                </div>

                {{-- 9. CLIENTES --}}
                <a href="{{ route('clientes.index') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('clientes.*') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 640 512"><path d="M144 160a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm352 0a80 80 0 1 0 0-160 80 80 0 1 0 0 160zM320 256a96 96 0 1 0 0-192 96 96 0 1 0 0 192zm-64 32h-8c-57.4 0-104 46.6-104 104v48c0 35.3 28.7 64 64 64H336h56c35.3 0 64-28.7 64-64V392c0-57.4-46.6-104-104-104h-8-88zM48 224H36c-19.9 0-36 16.1-36 36v52c0 24.3 19.7 44 44 44h56.6c-12-14.8-20.2-32.9-23.4-52.9H48v-80zm544 0H560v80h-29.2c-3.2 20-11.4 38.1-23.4 52.9H596c24.3 0 44-19.7 44-44V260c0-19.9-16.1-36-36-36z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Clientes</span>
                </a>

                {{-- 10. CORTE MENSUAL --}}
                <a href="{{ route('corte.index') }}" class="flex items-center gap-4 px-6 py-4 rounded-[2rem] transition-all {{ request()->routeIs('corte.*') ? 'bg-black text-white shadow-xl' : 'hover:bg-black/10 font-black' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 448 512"><path d="M128 0c17.7 0 32 14.3 32 32V64H288V32c0-17.7 14.3-32 32-32s32 14.3 32 32V64h48c26.5 0 48 21.5 48 48v48H0V112C0 85.5 21.5 64 48 64H96V32c0-17.7 14.3-32 32-32zM0 192H448V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V192zm64 80v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16zm128 0v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H208c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H336zM64 400v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H208zm112 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H336c-8.8 0-16 7.2-16 16z"/></svg>
                    <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Corte Mensual</span>
                </a>

                {{-- 11. CONFIGURACIÓN (Desplegable Master) --}}
                <div x-data="{ configOpen: {{ (request()->routeIs('ventas.configuracion') || request()->routeIs('empleados.*') || request()->routeIs('sucursales.*') || request()->routeIs('categorias.*') || request()->routeIs('cargos.*')) ? 'true' : 'false' }} }" class="w-full">
                    <button @click="configOpen = !configOpen" 
                            class="w-full flex items-center justify-between px-6 py-4 rounded-[2rem] transition-all hover:bg-black/10 font-black">
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>
                            <span class="text-xs uppercase tracking-[0.3em] font-black italic mt-0.5">Configuración</span>
                        </div>
                        <svg :class="{'rotate-180': configOpen}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="configOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         x-cloak 
                         class="mt-2 px-2 flex flex-col space-y-1 pb-4">
                        
                        <a href="{{ route('ventas.configuracion') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('ventas.configuracion') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M0 416c0 17.7 14.3 32 32 32l54.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48L480 448c17.7 0 32-14.3 32-32s-14.3-32-32-32l-246.7 0c-12.3-28.3-40.5-48-73.3-48s-61 19.7-73.3 48L32 384c-17.7 0-32 14.3-32 32zm128 0a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zM320 256a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm32-80c-32.8 0-61 19.7-73.3 48L32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l246.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48l54.7 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-54.7 0c-12.3-28.3-40.5-48-73.3-48zM192 128a32 32 0 1 1 0-64 32 32 0 1 1 0 64zm73.3-64C253 35.7 224.8 16 192 16s-61 19.7-73.3 48L32 64C14.3 64 0 78.3 0 96s14.3 32 32 32l86.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48L480 128c17.7 0 32-14.3 32-32s-14.3-32-32-32L265.3 64z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('ventas.configuracion') ? 'font-black' : 'font-bold' }} italic mt-0.5">General</span>
                        </a>

                        <a href="{{ route('empleados.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('empleados.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('empleados.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Empleados</span>
                        </a>

                        <a href="{{ route('sucursales.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('sucursales.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 640 512"><path d="M320 0c17.7 0 32 14.3 32 32V96H472c39.8 0 72 32.2 72 72V480c0 17.7-14.3 32-32 32H128c-17.7 0-32-14.3-32-32V168c0-39.8 32.2-72 72-72H288V32c0-17.7 14.3-32 32-32zM208 384c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H208zm112 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H320zm112 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H432zM208 256c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H208zm112 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H320zm112 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H432z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('sucursales.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Sucursal</span>
                        </a>

                        <a href="{{ route('categorias.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('categorias.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M345 39.1L472.8 168.4c52.4 53 52.4 138.2 0 191.2L360.8 472.9c-9.3 9.4-24.5 9.5-33.9 .2s-9.5-24.5-.2-33.9L438.6 325.9c33.9-34.3 33.9-89.4 0-123.7L310.9 72.9c-9.3-9.4-9.2-24.6 .2-33.9s24.6-9.2 33.9 .2zM0 229.5V80C0 35.8 35.8 0 80 0h149.5c17 0 33.3 6.7 45.3 18.7l168 168c25 25 25 65.5 0 90.5L277.3 442.7c-25 25-65.5 25-90.5 0l-168-168C6.7 262.7 0 246.5 0 229.5zM144 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('categorias.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Categorías</span>
                        </a>

                        <a href="{{ route('cargos.index') }}" class="flex items-center gap-3 px-6 py-3.5 rounded-[1.5rem] transition-all {{ request()->routeIs('cargos.*') ? 'bg-black text-amber-400 shadow-xl' : 'hover:bg-black/10 text-black' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 384 512"><path d="M64 48c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16h256c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H64zM0 64C0 28.7 28.7 0 64 0H320c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zm112 72a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zm40-88a88 88 0 1 0 0 176 88 88 0 1 0 0-176zm-76 226c-17-7.9-38-1-45.9 16s-1 38 16 45.9c37.8 17.5 83.2 22.1 127 12.3l49.5-11.1c11.9-2.7 23.3-7.5 33.7-14.1l14.2-9c15.1-9.6 19.6-29.6 10-44.7s-29.6-19.6-44.7-10l-14.2 9c-3.1 2-6.5 3.4-10.1 4.2l-49.5 11.1c-29.2 6.5-59.5 3.5-86.1-8.7z"/></svg>
                            <span class="text-[10px] uppercase tracking-[0.2em] {{ request()->routeIs('cargos.*') ? 'font-black' : 'font-bold' }} italic mt-0.5">Cargos</span>
                        </a>

                    </div>
                </div>

            </nav>

            <div class="p-8 border-t border-black/10 shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-4 px-6 py-5 rounded-[2rem] bg-black text-white hover:bg-white hover:text-black transition-all font-black text-xs uppercase tracking-[0.3em] italic shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <header class="w-full bg-white border-b border-gray-100 sticky top-0 z-30 px-6 py-3">
            <div class="max-w-[1600px] mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="p-2.5 bg-amber-400 rounded-xl shadow-sm hover:scale-105 transition-transform active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-[9px] font-black text-gray-400 uppercase tracking-[0.4em] mb-0 italic">By Ollintem</h2>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] leading-none mb-1">Usuario Activo</p>
                        <p class="text-sm font-black text-gray-900 uppercase italic tracking-tighter leading-none">{{ Auth::user()->nombre }}</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-400 rounded-xl shadow-inner flex items-center justify-center font-black text-base italic border-[3px] border-white shadow-md">
                        {{ substr(Auth::user()->nombre, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-6 flex flex-col">
            <div class="max-w-[1600px] w-full mx-auto h-full flex flex-col">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>