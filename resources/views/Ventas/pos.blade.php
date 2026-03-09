@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
</style>

@if(!$cajaAbierta)
    <div class="w-full flex flex-col items-center justify-center min-h-[70vh]">
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center max-w-lg shadow-sm">
            <h3 class="text-2xl font-black text-gray-800 mb-2">¡Caja Cerrada!</h3>
            <p class="text-gray-500 mb-6">Para empezar a vender, necesitas abrir el turno de caja primero.</p>
            <a href="{{ route('flujo.caja.index') }}" class="bg-[#fd7e14] text-white font-bold py-3 px-8 rounded-lg shadow-sm inline-block">
                Ir a Abrir Caja
            </a>
        </div>
    </div>
@else

    <script>
        const dbPizzas = {!! json_encode($pizzas) !!};
        const dbMariscos = {!! json_encode($mariscos) !!};
        const dbBebidas = {!! json_encode($bebidas) !!}; 
        const dbDirectos = {!! json_encode($directos) !!};
        const dbPaquetes = {!! json_encode($paquetes) !!};
        const dbIngredientes = {!! json_encode($ingredientes) !!};
        const dbTamanosBase = {!! json_encode($tamanos_base) !!};
        const dbEspecialidades = {!! json_encode($especialidades_lista) !!};
        const dbCategoriasExtras = {!! json_encode($categorias_extras) !!}; 
        const dbMagnoPrice = parseFloat({!! json_encode($magno_precio) !!}); 
        const dbPreciosOrilla = {!! json_encode($precios_orilla) !!}; 
        
        let rawClientes = {!! json_encode($clientes) !!};
        const dbClientes = Array.isArray(rawClientes) ? rawClientes : Object.values(rawClientes || {});
        
        let rawDirs = {!! json_encode($direcciones) !!};
        const dbDirecciones = Array.isArray(rawDirs) ? rawDirs : Object.values(rawDirs || {});
    </script>

    <div class="w-full min-h-[90vh] bg-[#f8f9fa] p-4 lg:p-6 font-sans text-[#212529]" x-data="posApp()">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 max-w-[1600px] mx-auto">
            
            <div class="lg:col-span-8 flex flex-col gap-5">
                
                {{-- BOTONES SUPERIORES --}}
                <div class="flex flex-wrap gap-2">
                    <button @click="abrirPaquete(1)" class="bg-[#ffc107] text-[#212529] px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-colors">Paquete 1</button>
                    <button @click="abrirPaquete(2)" class="bg-[#ffc107] text-[#212529] px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-colors">Paquete 2</button>
                    <button @click="abrirPaquete(3)" class="bg-[#ffc107] text-[#212529] px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-colors">Paquete 3</button>
                    <button @click="modalIngredientes = true" class="bg-[#fd7e14] text-white px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-colors">Por Ingrediente</button>
                    <button @click="modalMitades = true; mitSel = []; mitTam = null;" class="bg-[#dc3545] text-white px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-colors">Mitad y Mitad</button>
                </div>

                {{-- BARRA DE CATEGORÍAS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 flex flex-col xl:flex-row justify-between items-center gap-4">
                    <div class="flex flex-wrap gap-1.5 items-center w-full xl:w-auto">
                        <button @click="cat = 12; view = 'pizzas'" :class="cat === 12 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Pizzas</button>
                        <button @click="cat = 2; view = 'pizzas'" :class="cat === 2 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Mariscos</button>
                        
                        <button @click="abrirRectangularGeneral()" :class="modalRectangular ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Rectangular</button>
                        <button @click="abrirBarraGeneral()" :class="modalBarra ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Barra</button>
                        
                        {{-- MENÚ EXTRAS --}}
                        <div class="relative" x-data="{ openExtras: false }">
                            <button @click="openExtras = !openExtras" :class="dbCategoriasExtras.map(c=>c.id_cat).includes(cat) || cat === 1 ? 'bg-[#adb5bd] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors flex items-center gap-1">
                                Extras <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="openExtras" @click.away="openExtras = false" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1 max-h-72 overflow-y-auto">
                                
                                {{-- Categorías Dinámicas --}}
                                <template x-for="catEx in dbCategoriasExtras" :key="catEx.id_cat">
                                    <button @click="
                                        cat = parseInt(catEx.id_cat); 
                                        view = 'otros'; 
                                        openExtras = false;
                                    " class="w-full text-left px-4 py-2.5 text-[13px] font-bold text-[#495057] hover:bg-gray-50" x-text="catEx.descripcion"></button>
                                </template>

                                {{-- Botón Refrescos Obligatorio --}}
                                <button @click="cat = 1; view = 'bebidas'; openExtras = false;" class="w-full text-left px-4 py-2.5 text-[13px] font-bold text-[#495057] hover:bg-gray-50 border-t border-gray-100">Refrescos</button>
                                
                                {{-- Botón Exclusivo MAGNO --}}
                                <button @click="abrirMagnoGeneral(); openExtras = false" class="w-full text-left px-4 py-2.5 text-[13px] font-bold text-[#495057] hover:bg-gray-50 border-t border-gray-100">Magno</button>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full xl:w-[220px]">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" x-model="search" placeholder="Buscar producto." class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-md text-[13px] focus:outline-none focus:border-[#fd7e14]">
                    </div>
                </div>

                {{-- GRID PRODUCTOS --}}
                <div class="overflow-y-auto max-h-[65vh] pb-10 scrollbar-hide pr-1">
                    
                    {{-- Pizzas / Mariscos --}}
                    <div x-show="view === 'pizzas'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        <template x-for="p in getListaTamanos()" :key="p.nombre">
                            <button @click="abrirOpciones(p)" class="bg-white rounded-[10px] shadow-sm border border-gray-100 border-l-[4px] border-l-[#ffc107] p-5 flex flex-col justify-between items-start text-left h-[105px] hover:shadow-md transition">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight" x-text="p.nombre"></span>
                                <span class="text-[#fd7e14] text-[13px] font-bold">Ver opciones &rarr;</span>
                            </button>
                        </template>
                    </div>

                    {{-- Bebidas --}}
                    <div x-show="view === 'bebidas'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" x-cloak>
                        <template x-for="b in getListaBebidas()" :key="'beb_'+b.nombre">
                            <button @click="abrirBebida(b)" class="bg-white rounded-[10px] shadow-sm border border-gray-100 border-l-[4px] border-l-[#17a2b8] p-5 flex flex-col justify-between items-start text-left h-[105px] hover:shadow-md transition">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight" x-text="b.nombre"></span>
                                <span class="text-[#17a2b8] text-[13px] font-bold">Elegir tamaño &rarr;</span>
                            </button>
                        </template>
                    </div>

                    {{-- Otros Productos (Hamburguesas, etc) --}}
                    <div x-show="view === 'otros'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mt-4" x-cloak>
                        <template x-for="p in getListaDirectos()" :key="p.id">
                            <button @click="addDirecto(p)" class="bg-white rounded-[10px] shadow-sm border border-gray-100 border-l-[4px] border-l-blue-400 p-5 flex flex-col justify-between items-start text-left h-[105px] hover:shadow-md transition">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight" x-text="p.nombre"></span>
                                <div class="flex items-center gap-1 mt-auto">
                                    <span class="text-gray-400 text-[12px]">Precio</span>
                                    <span class="text-[#fd7e14] text-[16px] font-black" x-text="'$' + parseFloat(p.precio).toFixed(2)"></span>
                                </div>
                            </button>
                        </template>
                    </div>

                </div>
            </div>

            <div class="lg:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-[calc(100vh-2rem)] sticky top-4">
                <div class="p-6 pb-4 border-b border-gray-100 flex justify-between items-end">
                    <div>
                        <h2 class="text-[24px] font-black text-[#212529] leading-none">Pedido Actual</h2>
                        <p x-show="cartGroups.length === 0" class="text-[#6c757d] text-[14px] mt-1.5">Sin productos en el carrito</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 scrollbar-hide bg-[#f8f9fa]">
                    <template x-for="(group, gIdx) in cartGroups" :key="group.id_grupo">
                        <div>
                            
                            {{-- TARJETAS PARA PIZZAS AGRUPADAS --}}
                            <template x-if="group.type === 'pizza_pair'">
                                <div class="bg-white border border-gray-200 rounded-[8px] shadow-sm mb-4">
                                    <div class="bg-gray-100 border-b border-gray-200 px-4 py-2.5 rounded-t-[8px] flex justify-between items-center">
                                        <h3 class="font-bold text-[#212529] text-[13px]" x-text="'Pizzas Tamaño ' + group.size"></h3>
                                        <span class="font-bold text-[11px] bg-white border border-gray-200 px-2 py-0.5 rounded text-gray-600" x-text="group.items.length + ' Pizza(s)'"></span>
                                    </div>
                                    
                                    <div class="p-4 space-y-4">
                                        <template x-for="p in group.items" :key="p.item.uid">
                                            <div class="relative border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                                <div class="flex justify-between items-start w-full">
                                                    <div class="pr-8">
                                                        <h4 class="font-black text-[#212529] text-[14px] leading-tight mb-0.5" x-text="p.item.variante || p.item.nombre_base"></h4>
                                                        <p class="text-[12px] text-gray-500 font-medium" x-text="p.item.nombre_base"></p>
                                                    </div>
                                                    <button @click="eliminarItemByUid(p.item.uid)" class="text-[#dc3545] hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded absolute right-0 top-0 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 10-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                    </button>
                                                </div>

                                                <div class="flex items-center justify-between mt-2 mb-2">
                                                    <div class="flex items-center bg-[#e9ecef] rounded border border-gray-200">
                                                        <button @click="eliminarItemByUid(p.item.uid)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">-</button>
                                                        <span class="w-8 h-7 flex justify-center items-center font-bold text-[#212529] bg-white border-x border-gray-200 text-[13px]">1</span>
                                                        <button @click="clonePizza(p.item)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">+</button>
                                                    </div>
                                                    <span class="text-[12px] text-[#6c757d] font-medium" x-text="'Base: $' + parseFloat(p.price).toFixed(2)"></span>
                                                </div>

                                                <div class="flex justify-between items-end mt-2">
                                                    <label class="flex items-center gap-2 text-[12px] text-[#495057] cursor-pointer w-max bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                                        <input type="checkbox" :checked="p.item.orilla_queso" @change="toggleOrilla(p.item.uid, $event.target.checked)" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-3.5 h-3.5">
                                                        Orilla Queso <span class="font-bold text-[#fd7e14]" x-text="'+$' + p.item.precio_orilla"></span>
                                                    </label>
                                                    <span class="text-[14px] font-black text-[#212529]" x-text="'$' + p.item.precioFinal.toFixed(2)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-50 text-right border-t border-gray-200 rounded-b-[8px]">
                                        <span class="text-gray-500 text-[12px] font-bold uppercase mr-2 tracking-wider">Subtotal:</span>
                                        <span class="font-black text-[#212529] text-[18px]" x-text="'$' + group.subtotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </template>

                            {{-- TARJETAS PARA OTROS PRODUCTOS NORMALES (Incluyendo Magno y Paquetes) --}}
                            <template x-if="group.type === 'normal'">
                                <div class="border border-gray-200 rounded-[8px] p-4 bg-white shadow-sm mb-4 relative">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-[#212529] text-[14px] pr-6 leading-tight" x-text="group.item.nombre_base"></h4>
                                        <button @click="eliminarItemByUid(group.item.uid)" class="text-[#dc3545] hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded absolute right-4 top-4 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 10-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="flex items-center bg-[#e9ecef] rounded border border-gray-200">
                                            <button @click="group.item.qty > 1 ? updateNormalQty(group.item, -1) : null" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">-</button>
                                            <span class="w-8 h-7 flex justify-center items-center font-bold text-[#212529] bg-white border-x border-gray-200 text-[13px]" x-text="group.item.qty"></span>
                                            <button @click="updateNormalQty(group.item, 1)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">+</button>
                                        </div>
                                        <span class="text-[12px] text-[#6c757d] font-medium" x-text="'| Base: $' + parseFloat(group.item.precioBase).toFixed(2)"></span>
                                    </div>
                                    
                                    <div x-show="group.item.variante" class="bg-[#f8f9fa] border border-gray-200 rounded-[6px] p-2 mt-2">
                                        <span class="text-[12px] text-[#495057] block font-bold whitespace-pre-wrap" x-text="group.item.variante"></span>
                                    </div>

                                    {{-- Opcion de Orilla Queso exclusiva para la Magno --}}
                                    <template x-if="group.item.is_magno">
                                        <label class="flex items-center gap-2 text-[12px] text-[#495057] cursor-pointer mt-2 w-max bg-white px-2 py-1 rounded border border-gray-200 shadow-sm">
                                            <input type="checkbox" x-model="group.item.orilla_queso" @change="recalc()" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-3.5 h-3.5">
                                            Orilla Queso <span class="font-bold text-[#fd7e14]" x-text="'+$' + group.item.precio_orilla"></span>
                                        </label>
                                    </template>

                                    {{-- Opcion de Orillas Extra para Paquetes --}}
                                    <template x-if="group.item.tipo === 'paq'">
                                        <div class="flex items-center justify-between mt-2 bg-white px-3 py-2 rounded border border-gray-200 shadow-sm">
                                            <span class="text-[12px] text-[#495057] font-bold">Orillas Rellenas <span class="text-[#fd7e14]">(+$<span x-text="group.item.precio_orilla"></span> c/u)</span></span>
                                            <div class="flex items-center bg-[#e9ecef] rounded border border-gray-200">
                                                <button @click="decrementarOrillaPaq(group.item.uid)" class="w-6 h-6 font-bold text-[#495057] hover:bg-gray-300 flex items-center justify-center leading-none">-</button>
                                                <span class="w-6 h-6 flex justify-center items-center font-bold text-[#212529] bg-white border-x border-gray-200 text-[12px]" x-text="group.item.orillas_qty"></span>
                                                <button @click="incrementarOrillaPaq(group.item.uid)" class="w-6 h-6 font-bold text-[#495057] hover:bg-gray-300 flex items-center justify-center leading-none">+</button>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="text-right font-black text-[#212529] text-[16px] mt-3 border-t border-gray-100 pt-2">
                                        <span x-text="'$' + group.subtotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>

                {{-- ZONA COBRO FINAL Y SELECTOR DE SERVICIO --}}
                <div class="p-6 border-t border-gray-200 bg-white rounded-b-xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                    <div class="flex justify-between items-center font-black text-[#212529] mb-4">
                        <span class="text-[16px]">Total a cobrar:</span>
                        <span x-text="'$' + getTotal().toFixed(2)" class="text-[26px] text-[#28a745]"></span>
                    </div>

                    <button @click="modalComentarios = true" class="w-full bg-[#f8f9fa] border border-gray-200 hover:bg-[#e9ecef] text-[#495057] py-2.5 rounded-[6px] font-bold text-[14px] flex justify-center items-center gap-2 mb-4">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                        Nota General del Pedido
                    </button>

                    <div class="mb-4 h-10">
                        <template x-if="servicio === 1">
                            <input type="text" x-model="mesa" placeholder="MESA #" class="w-full h-full bg-white border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] font-bold focus:outline-none focus:border-[#fd7e14]">
                        </template>
                    </div>

                    <div class="flex rounded-[6px] overflow-hidden shadow-sm h-[50px]">
                        <div class="relative w-[45%]" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full h-full bg-[#fd7e14] text-white font-bold text-[13px] flex justify-center items-center gap-2 border-r border-[#e36b0c]">
                                <span x-text="nomServicio()"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute bottom-full left-0 w-[200px] bg-white border border-gray-200 rounded-lg shadow-xl z-50 mb-1 py-1">
                                <button @click="servicio = 3; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#fd7e14] hover:bg-orange-50 border-b border-gray-100 flex items-center gap-2">🏍️ A Domicilio</button>
                                <button @click="servicio = 1; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#495057] hover:bg-gray-50 border-b border-gray-100 flex items-center gap-2">🍽️ Comer Aqui</button>
                                <button @click="servicio = 2; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#495057] hover:bg-gray-50 flex items-center gap-2">🛍️ Para Llevar</button>
                            </div>
                        </div>

                        <button @click="procesarOrden()" :disabled="cart.length === 0" :class="cart.length === 0 ? 'bg-[#e9ecef] text-[#adb5bd] cursor-not-allowed' : 'bg-[#fd7e14] hover:bg-[#e36b0c] text-white'" class="flex-1 font-black text-[15px] transition-colors">
                            Enviar Orden
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODALES DE PRODUCTOS --}}
        {{-- ========================================================================= --}}
        
        {{-- MODAL OPCIONES NORMAL (Catálogo Pizzas) --}}
        <div x-show="modalOpc" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[350px] flex flex-col overflow-hidden" @click.away="modalOpc = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-[18px] font-bold text-[#212529]" x-text="opcItem?.nombre"></h2>
                    <button @click="modalOpc = false" class="text-gray-400 hover:text-black font-bold text-xl">&times;</button>
                </div>
                <div class="p-5 bg-[#f8f9fa] space-y-3 max-h-[50vh] overflow-y-auto scrollbar-hide">
                    <template x-for="t in opcItem?.tamanos" :key="t.id">
                        <button @click="addOpc(t)" class="w-full flex justify-between items-center bg-white border border-gray-200 rounded-[8px] p-4 hover:border-[#fd7e14] hover:shadow-sm transition-all">
                            <span class="font-bold text-[#212529] text-[14px]" x-text="cleanSize(t.tamano)"></span>
                            <span class="font-black text-[#28a745] text-[15px]" x-text="'$' + parseFloat(t.precio).toFixed(2)"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- MODAL BEBIDAS --}}
        <div x-show="modalBebida" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[350px] flex flex-col overflow-hidden" @click.away="modalBebida = false">
                <div class="bg-[#17a2b8] p-5 flex justify-between items-center text-white">
                    <h2 class="text-[18px] font-bold" x-text="bebidaItem?.nombre"></h2>
                    <button @click="modalBebida = false" class="text-white hover:text-gray-200 font-bold text-xl">&times;</button>
                </div>
                <div class="p-5 bg-[#f8f9fa] space-y-3 max-h-[50vh] overflow-y-auto scrollbar-hide">
                    <template x-for="opc in bebidaItem?.opciones" :key="opc.id">
                        <button @click="addBebida(opc)" class="w-full flex justify-between items-center bg-white border border-gray-200 rounded-[8px] p-4 hover:border-[#17a2b8] hover:shadow-sm transition-all">
                            <span class="font-bold text-[#212529] text-[14px]" x-text="opc.tamano"></span>
                            <span class="font-black text-[#17a2b8] text-[15px]" x-text="'$' + parseFloat(opc.precio).toFixed(2)"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- MAGNO --}}
        <div x-show="modalMagno" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[700px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalMagno = false">
                <div class="bg-[#212529] p-5 flex justify-between items-center text-white">
                    <h2 class="text-xl font-bold flex items-center gap-2">Magno</h2>
                    <button @click="modalMagno = false" class="hover:text-gray-300 font-bold text-2xl leading-none">&times;</button>
                </div>
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    <div class="w-full md:w-[60%] p-6 overflow-y-auto border-r border-gray-100 space-y-6 bg-[#f8f9fa] scrollbar-hide">
                        <div class="grid grid-cols-2 gap-2 mb-6">
                            <template x-for="i in 2">
                                <div class="border-2 rounded-[8px] p-3 text-center transition-all h-[60px] flex items-center justify-center relative" 
                                     :class="magnoSel[i-1] ? 'border-[#212529] bg-gray-100' : 'border-dashed border-gray-300 bg-white'">
                                    <template x-if="magnoSel[i-1]">
                                        <div class="w-full flex justify-between items-center px-1">
                                            <span class="text-[12px] font-bold text-[#212529]" x-text="magnoSel[i-1]"></span>
                                            <button @click="removeMagnoEsp(i-1)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                                <button @click="addMagnoEsp(esp.nombre)" :disabled="magnoSel.length >= 2" class="border rounded-[8px] p-2.5 text-[12px] font-bold text-left bg-white text-gray-700 hover:border-black disabled:opacity-50" x-text="esp.nombre"></button>
                            </template>
                        </div>
                    </div>
                    <div class="w-full md:w-[40%] bg-white p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-[18px] font-black text-black border-b border-gray-200 pb-3 mb-5">Resumen Magno</h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-[8px] p-4 text-[13px] font-bold text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="formatearMagnoPreview()"></div>
                            <div class="mt-3 bg-gray-50 border border-gray-200 px-3 py-2 rounded font-bold text-[12px] text-gray-700">
                                • Incluye 1 Refresco de 2L
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-gray-500 text-[14px] font-bold">Total</span>
                                <span class="font-black text-[#28a745] text-[26px] leading-none" x-text="'$' + (magnoItem ? parseFloat(magnoItem.precio).toFixed(2) : '0.00')"></span>
                            </div>
                            <button @click="addMagno()" :disabled="magnoSel.length !== 2" :class="magnoSel.length !== 2 ? 'bg-[#ced4da] text-gray-500 cursor-not-allowed' : 'bg-[#212529] text-white hover:bg-black'" class="w-full font-bold py-3.5 rounded-[8px] text-[14px] transition-all mb-2">Añadir al Carrito</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RECTANGULAR --}}
        <div x-show="modalRectangular" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[700px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalRectangular = false">
                <div class="bg-[#fd7e14] p-5 flex justify-between items-center text-white">
                    <h2 class="text-xl font-bold">Pizza Rectangular (4 Cuartos)</h2>
                    <button @click="modalRectangular = false" class="hover:text-gray-200 font-bold text-2xl leading-none">&times;</button>
                </div>
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    <div class="w-full md:w-[60%] p-6 overflow-y-auto border-r border-gray-100 bg-[#f8f9fa] scrollbar-hide">
                        <div class="grid grid-cols-2 gap-2 mb-6">
                            <template x-for="i in 4">
                                <div class="border-2 rounded-[8px] p-3 text-center transition-all h-[60px] flex items-center justify-center relative" :class="rectSel[i-1] ? 'border-[#fd7e14] bg-orange-50' : 'border-dashed border-gray-300 bg-white'">
                                    <template x-if="rectSel[i-1]">
                                        <div class="w-full flex justify-between items-center px-1">
                                            <span class="text-[12px] font-bold text-[#fd7e14]" x-text="rectSel[i-1]"></span>
                                            <button @click="removeRectEsp(i-1)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                                <button @click="addRectEsp(esp.nombre)" :disabled="rectSel.length >= 4" class="border rounded-[8px] p-2.5 text-[12px] font-bold text-left bg-white text-gray-700 hover:border-orange-400 disabled:opacity-50" x-text="esp.nombre"></button>
                            </template>
                        </div>
                    </div>
                    <div class="w-full md:w-[40%] bg-white p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-[18px] font-black text-black border-b border-gray-200 pb-3 mb-5">Resumen</h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-[8px] p-4 text-[13px] font-bold text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="formatearCuartosPreview()"></div>
                        </div>
                        <div class="mt-6 text-center">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-gray-500 text-[14px] font-bold">Total</span>
                                <span class="font-black text-[#28a745] text-[26px]" x-text="'$' + (rectItem ? parseFloat(rectItem.precio).toFixed(2) : '0.00')"></span>
                            </div>
                            <button @click="addRectangular()" :disabled="rectSel.length !== 4" :class="rectSel.length !== 4 ? 'bg-[#ced4da] text-gray-500 cursor-not-allowed' : 'bg-[#fd7e14] text-white hover:bg-[#e36b0c]'" class="w-full font-bold py-3.5 rounded-[8px] text-[14px]">Añadir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARRA --}}
        <div x-show="modalBarra" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[700px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalBarra = false">
                <div class="bg-[#17a2b8] p-5 flex justify-between items-center text-white">
                    <h2 class="text-xl font-bold">Pizza de Barra (2 Mitades)</h2>
                    <button @click="modalBarra = false" class="hover:text-gray-200 font-bold text-2xl leading-none">&times;</button>
                </div>
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    <div class="w-full md:w-[60%] p-6 overflow-y-auto border-r border-gray-100 bg-[#f8f9fa] scrollbar-hide">
                        <div class="grid grid-cols-2 gap-2 mb-6">
                            <template x-for="i in 2">
                                <div class="border-2 rounded-[8px] p-3 text-center transition-all h-[60px] flex items-center justify-center relative" :class="barraSel[i-1] ? 'border-[#17a2b8] bg-cyan-50' : 'border-dashed border-gray-300 bg-white'">
                                    <template x-if="barraSel[i-1]">
                                        <div class="w-full flex justify-between items-center px-1">
                                            <span class="text-[12px] font-bold text-[#17a2b8]" x-text="barraSel[i-1]"></span>
                                            <button @click="removeBarraEsp(i-1)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                                <button @click="addBarraEsp(esp.nombre)" :disabled="barraSel.length >= 2" class="border rounded-[8px] p-2.5 text-[12px] font-bold text-left bg-white text-gray-700 hover:border-[#17a2b8] disabled:opacity-50" x-text="esp.nombre"></button>
                            </template>
                        </div>
                    </div>
                    <div class="w-full md:w-[40%] bg-white p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-[18px] font-black text-black border-b border-gray-200 pb-3 mb-5">Resumen Barra</h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-[8px] p-4 text-[13px] font-bold text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="formatearMediosPreview()"></div>
                        </div>
                        <div class="mt-6 text-center">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-gray-500 text-[14px] font-bold">Total</span>
                                <span class="font-black text-[#28a745] text-[26px]" x-text="'$' + (barraItem ? parseFloat(barraItem.precio).toFixed(2) : '0.00')"></span>
                            </div>
                            <button @click="addBarra()" :disabled="barraSel.length !== 2" :class="barraSel.length !== 2 ? 'bg-[#ced4da] text-gray-500 cursor-not-allowed' : 'bg-[#17a2b8] text-white hover:bg-[#138496]'" class="w-full font-bold py-3.5 rounded-[8px] text-[14px]">Añadir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAQUETES (1, 2 y 3) --}}
        <div x-show="modalPaq1" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[400px] flex flex-col overflow-hidden" @click.away="modalPaq1 = false">
                <div class="p-6 pb-4 relative border-b border-gray-100 bg-[#ffc107]">
                    <button @click="modalPaq1 = false" class="absolute top-4 right-4 text-black/60 hover:text-black font-bold text-2xl">&times;</button>
                    <h2 class="text-2xl font-black text-black mb-1">Paquete 1</h2>
                </div>
                <div class="p-6 bg-[#f8f9fa]">
                    <ul class="list-disc pl-5 text-[14px] font-medium text-gray-600 mb-5"><li>2 Pizzas Grandes</li><li>1 Refresco de 2L Jarrito</li></ul>
                    <div class="space-y-3 mb-2">
                        <button @click="paq1Opt = 'Combinado (1 Hawaiana y 1 Pepperoni)'" class="w-full text-left block border rounded-[8px] p-4" :class="paq1Opt === 'Combinado (1 Hawaiana y 1 Pepperoni)' ? 'bg-[#fff9c4] border-[#ffc107]' : 'bg-white'"> <span class="block font-bold">Combinado</span> <span class="block text-xs">1 Hawaiana y 1 Pepperoni</span> </button>
                        <button @click="paq1Opt = '2 Hawaianas'" class="w-full text-left block border rounded-[8px] p-4 font-bold" :class="paq1Opt === '2 Hawaianas' ? 'bg-[#fff9c4] border-[#ffc107]' : 'bg-white'">2 Hawaianas</button>
                        <button @click="paq1Opt = '2 Pepperoni'" class="w-full text-left block border rounded-[8px] p-4 font-bold" :class="paq1Opt === '2 Pepperoni' ? 'bg-[#fff9c4] border-[#ffc107]' : 'bg-white'">2 Pepperoni</button>
                    </div>
                </div>
                <div class="p-5 flex gap-3 border-t border-gray-100 bg-white items-center justify-between">
                    <span class="font-black text-[#28a745] text-[20px] mb-0" x-text="'$' + (paqObj ? parseFloat(paqObj.precio).toFixed(2) : '0.00')"></span>
                    <button @click="addPaq1()" :disabled="!paq1Opt" :class="!paq1Opt ? 'opacity-50' : ''" class="bg-[#ffc107] hover:bg-[#e0a800] text-[#212529] font-bold py-3 px-6 rounded-lg text-[14px]">Agregar</button>
                </div>
            </div>
        </div>

        <div x-show="modalPaq2" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[450px] flex flex-col max-h-[90vh] overflow-hidden" @click.away="modalPaq2 = false">
                <div class="p-6 relative border-b border-gray-100 bg-[#ffc107]"><h2 class="text-2xl font-black text-black mb-1">Paquete 2</h2><button @click="modalPaq2 = false" class="absolute top-4 right-4 text-black/60 hover:text-black font-bold text-2xl">&times;</button></div>
                <div class="p-6 overflow-y-auto flex-1 space-y-5 bg-[#f8f9fa] scrollbar-hide">
                    <ul class="list-disc pl-5 text-[14px] font-medium text-gray-600 mb-2 mt-0"><li>1 Hamburguesa o Alitas</li><li>1 Pizza Grande</li><li>1 Refresco de 2L Jarrito</li></ul>
                    <div>
                        <div class="flex rounded-md overflow-hidden border border-gray-300 bg-white">
                            <button @click="paq2Tipo = 'hamb'; paq2Extra = ''" :class="paq2Tipo === 'hamb' ? 'bg-black text-white font-bold' : 'text-gray-600'" class="flex-1 py-2 text-[13px]">Hamburguesa</button>
                            <button @click="paq2Tipo = 'alitas'; paq2Extra = ''" :class="paq2Tipo === 'alitas' ? 'bg-black text-white font-bold' : 'text-gray-600'" class="flex-1 py-2 text-[13px]">Alitas</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-if="paq2Tipo === 'hamb'">
                            <button @click="paq2Extra = 'Sencilla de Pollo'" :class="paq2Extra === 'Sencilla de Pollo' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] font-bold">Sencilla de Pollo</button>
                        </template>
                        <template x-if="paq2Tipo === 'hamb'">
                            <button @click="paq2Extra = 'Sencilla de Res'" :class="paq2Extra === 'Sencilla de Res' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] font-bold">Sencilla de Res</button>
                        </template>
                        <template x-if="paq2Tipo === 'alitas'">
                            <button @click="paq2Extra = 'Alitas BBQ'" :class="paq2Extra === 'Alitas BBQ' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] font-bold">Alitas BBQ</button>
                        </template>
                        <template x-if="paq2Tipo === 'alitas'">
                            <button @click="paq2Extra = 'Alitas Adobadas'" :class="paq2Extra === 'Alitas Adobadas' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] font-bold">Alitas Adobadas</button>
                        </template>
                    </div>
                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1 scrollbar-hide pb-2">
                        <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                            <button @click="paq2Pizza = esp.nombre" :class="paq2Pizza === esp.nombre ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-2.5 text-[12px] font-bold" x-text="esp.nombre"></button>
                        </template>
                    </div>
                </div>
                <div class="p-5 flex gap-3 border-t border-gray-100 bg-white justify-between items-center">
                    <span class="font-black text-[#28a745] text-[20px] mb-0" x-text="'$' + (paqObj ? parseFloat(paqObj.precio).toFixed(2) : '0.00')"></span>
                    <button @click="addPaq2()" :disabled="!paq2Extra || !paq2Pizza" :class="(!paq2Extra || !paq2Pizza) ? 'opacity-50' : ''" class="bg-[#ffc107] hover:bg-[#e0a800] text-[#212529] font-bold py-3 px-6 rounded-lg text-[14px]">Agregar</button>
                </div>
            </div>
        </div>

        <div x-show="modalPaq3" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[450px] flex flex-col max-h-[90vh] overflow-hidden" @click.away="modalPaq3 = false">
                <div class="p-6 relative border-b border-gray-100 bg-[#ffc107]"><h2 class="text-2xl font-black text-black mb-1">Paquete 3</h2><button @click="modalPaq3 = false" class="absolute top-4 right-4 text-black/60 hover:text-black font-bold text-2xl">&times;</button></div>
                <div class="p-6 overflow-y-auto flex-1 bg-[#f8f9fa] scrollbar-hide">
                    <ul class="list-disc pl-5 text-[14px] font-medium text-gray-600 mb-4 mt-0"><li>3 Pizzas Grandes</li><li>1 Refresco de 2L Jarrito</li></ul>
                    <div class="grid grid-cols-3 gap-2 mb-5">
                        <template x-for="i in 3">
                            <div class="border-2 rounded-[8px] p-2 text-center h-[55px] flex items-center justify-center relative" :class="paq3Pizzas[i-1] ? 'border-[#ffc107] bg-[#fff9c4]' : 'border-dashed border-gray-300 bg-white'">
                                <template x-if="paq3Pizzas[i-1]">
                                    <div class="w-full flex justify-between items-center px-1">
                                        <span class="text-[11px] font-bold text-[#212529]" x-text="paq3Pizzas[i-1]"></span>
                                        <button @click="removePaq3Esp(i-1)" class="text-red-500 font-bold text-[12px]">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border border-gray-100 rounded-lg p-2 bg-white">
                        <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                            <button @click="addPaq3Esp(esp.nombre)" :disabled="paq3Pizzas.length >= 3" class="border rounded-[8px] p-2.5 text-[12px] font-medium text-left text-black shadow-sm disabled:opacity-50 hover:border-[#ffc107] hover:bg-[#fffde7]" x-text="esp.nombre"></button>
                        </template>
                    </div>
                </div>
                <div class="p-5 flex gap-3 border-t border-gray-100 bg-white justify-between items-center">
                    <span class="font-black text-[#28a745] text-[20px] mb-0" x-text="'$' + (paqObj ? parseFloat(paqObj.precio).toFixed(2) : '0.00')"></span>
                    <button @click="addPaq3()" :disabled="paq3Pizzas.length !== 3" :class="paq3Pizzas.length !== 3 ? 'opacity-50' : ''" class="bg-[#ffc107] hover:bg-[#e0a800] text-[#212529] font-bold py-3 px-6 rounded-lg text-[14px]">Agregar</button>
                </div>
            </div>
        </div>

        {{-- MITAD Y MITAD --}}
        <div x-show="modalMitades" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[750px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalMitades = false">
                <div class="bg-[#dc3545] p-5 flex justify-between items-center text-white"><h2 class="text-xl font-bold">Mitades</h2><button @click="modalMitades = false" class="hover:text-gray-200 font-bold text-2xl leading-none">&times;</button></div>
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    <div class="w-full md:w-[65%] p-6 overflow-y-auto border-r border-gray-100 bg-[#f8f9fa] scrollbar-hide">
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                            <template x-for="tam in dbTamanosBase" :key="tam.id_tamañop">
                                <button @click="mitTam = tam; mitSel = []" :class="mitTam?.id_tamañop === tam.id_tamañop ? 'border-red-500 bg-red-50 shadow' : 'border-gray-200 bg-white'" class="border rounded-[8px] py-4 text-center">
                                    <span class="block font-bold text-black text-[14px]" x-text="cleanSize(tam.tamano)"></span>
                                </button>
                            </template>
                        </div>
                        <div x-show="mitTam" class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                            <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                                <button @click="toggleMitad(esp.nombre)" :class="mitSel.includes(esp.nombre) ? 'border-red-500 bg-red-50 text-red-700' : 'bg-white border-gray-200 text-gray-700'" class="border rounded-[8px] p-3 text-[12px] font-bold text-left shadow-sm" x-text="esp.nombre"></button>
                            </template>
                        </div>
                    </div>
                    <div class="w-full md:w-[35%] bg-white p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-[18px] font-black text-black border-b border-gray-200 pb-3 mb-5">Resumen</h3>
                            <div class="space-y-2">
                                <div class="border rounded-[8px] p-3 text-[13px]" :class="!mitSel[0] ? 'text-gray-400 border-dashed bg-gray-50' : 'text-black font-bold border-gray-200 bg-white'" x-text="mitSel[0] ? '1/2 ' + mitSel[0] : 'Selecciona primera mitad'"></div>
                                <div class="border rounded-[8px] p-3 text-[13px]" :class="!mitSel[1] ? 'text-gray-400 border-dashed bg-gray-50' : 'text-black font-bold border-gray-200 bg-white'" x-text="mitSel[1] ? '2/2 ' + mitSel[1] : 'Selecciona segunda mitad'"></div>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-gray-500 text-[14px] font-bold">Total</span>
                                <span class="font-black text-[#28a745] text-[26px] leading-none" x-text="'$' + (mitTam ? parseFloat(mitTam.precio).toFixed(2) : '0.00')"></span>
                            </div>
                            <button @click="addMitad()" :disabled="mitSel.length !== 2 || !mitTam" :class="(mitSel.length !== 2 || !mitTam) ? 'bg-[#ced4da] text-gray-500 cursor-not-allowed' : 'bg-[#dc3545] text-white hover:bg-red-700'" class="w-full font-bold py-3.5 rounded-[8px] text-[14px]">Añadir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- POR INGREDIENTES --}}
        <div x-show="modalIngredientes" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[600px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalIngredientes = false">
                <div class="bg-[#fd7e14] p-5 flex justify-between items-center text-white"><h2 class="text-xl font-bold">Por Ingrediente</h2><button @click="modalIngredientes = false" class="hover:text-orange-200 font-bold text-2xl leading-none">&times;</button></div>
                <div class="flex-1 overflow-y-auto p-6 bg-[#f8f9fa] space-y-6 scrollbar-hide">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <template x-for="tam in dbTamanosBase" :key="tam.id_tamañop">
                            <button @click="ingTam = tam" :class="ingTam?.id_tamañop === tam.id_tamañop ? 'border-orange-500 bg-orange-50 shadow' : 'border-gray-200 bg-white'" class="border rounded-[8px] py-4 text-center transition-all">
                                <span class="block font-bold text-black text-[14px]" x-text="cleanSize(tam.tamano)"></span>
                            </button>
                        </template>
                    </div>
                    <div x-show="ingTam">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white border border-gray-200 rounded-[8px] p-4 shadow-sm">
                            <template x-for="ing in dbIngredientes" :key="ing.id_ingrediente">
                                <label class="flex items-center gap-2 cursor-pointer text-[13px] text-[#495057] font-medium p-1.5 hover:bg-orange-50 rounded">
                                    <input type="checkbox" :value="ing.ingrediente" x-model="ingSel" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-4 h-4">
                                    <span x-text="ing.ingrediente"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="p-5 flex gap-4 bg-white border-t border-gray-200 items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[12px] text-gray-400 font-bold uppercase tracking-wider">Total</span>
                        <span class="font-black text-[#28a745] text-[26px] leading-none" x-text="'$' + precioPizzaIngredientes().toFixed(2)"></span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="modalIngredientes = false" class="bg-[#e9ecef] hover:bg-[#dee2e6] text-[#495057] font-bold px-6 py-3.5 rounded-[8px] text-[14px]">Cancelar</button>
                        <button @click="addIng()" :disabled="!ingTam || ingSel.length === 0" :class="(!ingTam || ingSel.length === 0) ? 'bg-[#ced4da] text-white cursor-not-allowed' : 'bg-[#fd7e14] hover:bg-[#e36b0c] text-white'" class="font-bold px-6 py-3.5 rounded-[8px] text-[14px]">Añadir</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL DOMICILIO --}}
        <div x-show="modalCliente" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[600px] flex flex-col max-h-[90vh] overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#fd7e14]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        Dirección de Entrega
                    </h2>
                    <button @click="modalCliente = false" class="text-gray-400 hover:text-black font-bold text-2xl leading-none">&times;</button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 bg-white space-y-6">
                    
                    <div class="relative">
                        <label class="block text-[14px] font-bold text-gray-700 mb-2">Cliente *</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1" @click.away="showClientesList = false">
                                <input type="text" 
                                       x-model="searchClienteText" 
                                       @input="showClientesList = true; clienteSeleccionado = null" 
                                       @focus="showClientesList = true" 
                                       placeholder="Buscar por nombre o teléfono..." 
                                       autocomplete="off"
                                       class="w-full border border-gray-300 rounded-[8px] py-3.5 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none shadow-sm transition-colors">
                                
                                <div x-show="showClientesList" 
                                     x-transition 
                                     class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-2xl max-h-80 overflow-y-auto">
                                    
                                    <template x-if="clientesFiltrados.length === 0">
                                        <div class="px-4 py-6 text-[15px] text-gray-500 italic text-center bg-gray-50">
                                            No se encontraron clientes en la base de datos.<br>Haz clic en "Nuevo" para registrarlo.
                                        </div>
                                    </template>

                                    <template x-for="cl in clientesFiltrados" :key="cl.id_cliente || cl.id || Math.random()">
                                        <div @click="seleccionarCliente(cl)" class="px-5 py-4 hover:bg-orange-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center transition-colors">
                                            <span class="font-bold text-[16px] text-gray-800" x-text="getClienteNombre(cl)"></span>
                                            <span class="text-[14px] text-gray-600 font-bold bg-gray-100 border border-gray-200 px-3 py-1 rounded-md shadow-sm" x-text="getClienteTelefono(cl)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <button @click="toggleFormNuevoCliente()" class="bg-[#ffc107] hover:bg-[#e0a800] text-black font-bold px-5 py-3 rounded-[8px] text-[14px] flex items-center gap-1 shadow-sm whitespace-nowrap transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path></svg>
                                Nuevo
                            </button>
                        </div>
                        
                        <div x-show="clienteSeleccionado && !clienteFormVisible" class="mt-3 bg-green-50 border border-green-200 p-4 rounded-[8px] flex justify-between items-center transition-all">
                            <div>
                                <span class="font-bold text-[16px] text-green-800 block" x-text="getClienteNombre(clienteSeleccionado)"></span>
                                <span class="text-[14px] text-green-600 font-bold block mt-0.5" x-text="'Tel: ' + getClienteTelefono(clienteSeleccionado)"></span>
                            </div>
                            <button @click="clienteSeleccionado = null; direccionesCliente = []; dirSeleccionada = null; searchClienteText='';" class="text-red-500 text-[14px] font-bold hover:underline">Cambiar Cliente</button>
                        </div>
                    </div>

                    <div x-show="clienteFormVisible" class="bg-gray-50 p-5 rounded-[8px] border border-gray-200 space-y-4">
                        <h4 class="font-bold text-[15px] text-gray-800 border-b border-gray-200 pb-2">Registrar Nuevo Cliente</h4>
                        <div>
                            <label class="block text-[14px] font-bold text-gray-600 mb-1.5">Nombre *</label>
                            <input type="text" x-model="nuevoClienteData.nombre" class="w-full border border-gray-300 rounded-[6px] py-2.5 px-3 text-[15px] focus:border-[#fd7e14] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold text-gray-600 mb-1.5">Teléfono</label>
                            <input type="text" x-model="nuevoClienteData.telefono" class="w-full border border-gray-300 rounded-[6px] py-2.5 px-3 text-[15px] focus:border-[#fd7e14] focus:outline-none">
                        </div>
                    </div>

                    <div x-show="clienteSeleccionado || clienteFormVisible" class="pt-4 border-t border-gray-200 transition-all">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-[14px] font-bold text-gray-700">Dirección de Entrega *</label>
                            <button @click="dirFormVisible = !dirFormVisible; dirSeleccionada = null" class="bg-[#ffc107] hover:bg-[#e0a800] text-black font-bold px-4 py-2 rounded-[6px] text-[13px] flex items-center gap-1 shadow-sm transition-colors">
                                + Nueva Dirección
                            </button>
                        </div>

                        <div x-show="!dirFormVisible && direccionesCliente.length > 0" class="space-y-3 max-h-48 overflow-y-auto pr-1">
                            <template x-for="dir in direccionesCliente" :key="dir.id_direccion || dir.id_dir || Math.random()">
                                <label class="block cursor-pointer">
                                    <div class="border rounded-[8px] p-4 transition-colors" :class="dirSeleccionada === (dir.id_direccion || dir.id_dir) ? 'border-[#fd7e14] bg-orange-50 shadow-sm' : 'border-gray-200 hover:bg-gray-50'">
                                        <div class="flex items-start gap-3">
                                            <input type="radio" :value="dir.id_direccion || dir.id_dir" x-model="dirSeleccionada" class="mt-1.5 text-[#fd7e14] focus:ring-[#fd7e14] w-4 h-4">
                                            <div class="text-[14px] text-gray-700 leading-snug">
                                                <span class="font-bold block text-[15px] text-black mb-1" x-text="dir.calle || dir.Calle || 'Sin calle'"></span>
                                                <span class="block text-gray-500" x-text="'Manzana: ' + (dir.manzana||dir.Manzana||'-') + ' | Lote: ' + (dir.lote||dir.Lote||'-')"></span>
                                                <span class="block text-gray-500" x-text="'Colonia: ' + (dir.colonia||dir.Colonia||'-')"></span>
                                                <span class="block text-gray-500 italic mt-1 font-medium" x-show="dir.referencia || dir.Referencia" x-text="'Ref: ' + (dir.referencia || dir.Referencia)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                        <div x-show="!dirFormVisible && direccionesCliente.length === 0 && !clienteFormVisible" class="text-center p-6 border-2 border-dashed border-gray-300 rounded-[8px] text-[14px] text-gray-500 font-medium">
                            Este cliente no tiene direcciones guardadas. Agrega una nueva.
                        </div>

                        <div x-show="dirFormVisible" class="bg-gray-50 p-5 rounded-[8px] border border-gray-200 space-y-4 mt-2">
                            <h4 class="font-bold text-[15px] text-gray-800 border-b border-gray-200 pb-2">Registrar Nueva Dirección</h4>
                            <div>
                                <label class="block text-[13px] font-bold text-gray-600 mb-1.5">Calle *</label>
                                <input type="text" x-model="nuevaDirData.calle" class="w-full border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] focus:border-[#fd7e14] focus:outline-none">
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-[13px] font-bold text-gray-600 mb-1.5">Manzana</label>
                                    <input type="text" x-model="nuevaDirData.manzana" class="w-full border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] focus:border-[#fd7e14] focus:outline-none">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[13px] font-bold text-gray-600 mb-1.5">Lote</label>
                                    <input type="text" x-model="nuevaDirData.lote" class="w-full border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] focus:border-[#fd7e14] focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-gray-600 mb-1.5">Colonia</label>
                                <input type="text" x-model="nuevaDirData.colonia" class="w-full border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] focus:border-[#fd7e14] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-gray-600 mb-1.5">Referencia</label>
                                <textarea x-model="nuevaDirData.referencia" rows="2" class="w-full border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] focus:border-[#fd7e14] focus:outline-none resize-none"></textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="p-5 flex gap-3 bg-white border-t border-gray-100">
                    <button @click="modalCliente = false" class="flex-1 bg-[#e9ecef] hover:bg-[#dee2e6] text-gray-700 font-bold py-3.5 rounded-[8px] text-[15px]">Cancelar</button>
                    <button @click="confirmarDomicilio()" :disabled="!esDomicilioValido()" :class="!esDomicilioValido() ? 'bg-[#ced4da] text-white cursor-not-allowed' : 'bg-[#fd7e14] hover:bg-[#e36b0c] text-white shadow-md'" class="flex-1 font-bold py-3.5 rounded-[8px] text-[15px] transition-colors">Confirmar Selección</button>
                </div>
            </div>
        </div>

        {{-- MODAL MULTIPAGO --}}
        <div x-show="modalPago" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[450px] max-w-full flex flex-col h-auto max-h-[90vh] overflow-hidden" @click.away="modalPago = false">
                <div class="bg-[#ffc107] p-5 flex justify-between items-center text-black relative">
                    <h2 class="text-xl font-black" x-text="'Cobro - ' + nomServicio()"></h2>
                    <button @click="modalPago = false" class="hover:text-gray-800 font-black text-2xl leading-none">&times;</button>
                </div>
                
                <div class="p-6 text-center border-b border-gray-100 bg-white">
                    <div class="font-black text-[#1a202c] text-[36px] leading-none mb-1" x-text="'$' + getTotal().toFixed(2)"></div>
                    <div class="text-[14px] font-bold" :class="faltaPagar() === 0 ? 'text-green-500' : (faltaPagar() < 0 ? 'text-blue-500' : 'text-red-500')" x-text="faltaPagar() === 0 ? 'Monto completo asignado' : (faltaPagar() < 0 ? 'Cambio: $' + Math.abs(faltaPagar()).toFixed(2) : 'Falta asignar: $' + faltaPagar().toFixed(2))"></div>
                </div>

                <div class="flex-1 overflow-y-auto p-5 bg-[#f8f9fa] space-y-4">
                    <p class="text-[13px] text-gray-500 mb-2">Selecciona métodos de pago (puedes elegir varios):</p>
                    
                    {{-- 1. Transferencia (id_metpago = 3) --}}
                    <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.transferencia.activo ? 'border-[#ffc107]' : 'border-gray-200'">
                        <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                            <input type="checkbox" x-model="pagos.transferencia.activo" @change="autoFillPago('transferencia')" class="w-5 h-5 rounded border-gray-300 text-[#ffc107] focus:ring-[#ffc107]">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            <span class="font-bold text-[15px] text-[#212529]">Transferencia</span>
                        </label>
                        <div x-show="pagos.transferencia.activo" class="px-4 pb-4 pt-1 space-y-3">
                            <div>
                                <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Transferencia</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                    <input type="number" step="0.01" min="0" x-model.number="pagos.transferencia.monto" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded text-[14px] font-bold focus:outline-none focus:border-[#ffc107]">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[12px] text-blue-600 font-bold mb-1">Número de Referencia *</label>
                                <input type="text" x-model="pagos.transferencia.referencia" placeholder="Ref de transferencia" class="w-full px-3 py-2 border border-blue-200 bg-blue-50 rounded text-[14px] focus:outline-none focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Tarjeta (id_metpago = 2) --}}
                    <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.tarjeta.activo ? 'border-[#ffc107]' : 'border-gray-200'">
                        <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                            <input type="checkbox" x-model="pagos.tarjeta.activo" @change="autoFillPago('tarjeta')" class="w-5 h-5 rounded border-gray-300 text-[#ffc107] focus:ring-[#ffc107]">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span class="font-bold text-[15px] text-[#212529]">Tarjeta</span>
                        </label>
                        <div x-show="pagos.tarjeta.activo" class="px-4 pb-4 pt-1">
                            <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Tarjeta</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                <input type="number" step="0.01" min="0" x-model.number="pagos.tarjeta.monto" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded text-[14px] font-bold focus:outline-none focus:border-[#ffc107]">
                            </div>
                            <p class="text-[11px] text-gray-400 italic mt-2">Se enviará terminal.</p>
                        </div>
                    </div>

                    {{-- 3. Efectivo (id_metpago = 1) --}}
                    <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.efectivo.activo ? 'border-[#ffc107]' : 'border-gray-200'">
                        <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                            <input type="checkbox" x-model="pagos.efectivo.activo" @change="autoFillPago('efectivo')" class="w-5 h-5 rounded border-gray-300 text-[#ffc107] focus:ring-[#ffc107]">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-bold text-[15px] text-[#212529]">Efectivo</span>
                        </label>
                        <div x-show="pagos.efectivo.activo" class="px-4 pb-4 pt-1 space-y-3">
                            <div>
                                <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Efectivo</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                    <input type="number" step="0.01" min="0" x-model.number="pagos.efectivo.monto" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded text-[14px] font-bold focus:outline-none focus:border-[#ffc107]">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[12px] text-green-600 font-bold mb-1">¿Con cuánto paga el cliente?</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-green-600 font-bold">$</span>
                                    <input type="number" step="0.01" min="0" x-model.number="pagos.efectivo.entregado" placeholder="Monto entregado" class="w-full pl-7 pr-3 py-2 border border-green-200 bg-green-50 rounded text-[14px] font-bold focus:outline-none focus:border-green-400">
                                </div>
                                <p x-show="pagos.efectivo.entregado > 0 && (pagos.efectivo.entregado - pagos.efectivo.monto) >= 0" class="text-[12px] text-gray-600 mt-2 font-bold bg-white p-2 rounded border border-gray-100 shadow-sm text-center">
                                    Su cambio: <span class="text-green-600 text-[16px]"> $<span x-text="(pagos.efectivo.entregado - pagos.efectivo.monto).toFixed(2)"></span></span>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="p-5 bg-white border-t border-gray-100 flex flex-col gap-2">
                    <button @click="procesarOrdenFinal()" :disabled="!pagosValidos()" :class="!pagosValidos() ? 'bg-[#1a202c]/50 text-white cursor-not-allowed' : 'bg-[#1a202c] hover:bg-black text-white shadow-md'" class="w-full font-bold py-3.5 rounded-[8px] text-[15px] transition-colors flex justify-center items-center gap-2">
                        Confirmar Pagos <span class="bg-white/20 px-2 py-0.5 rounded text-[13px]" x-text="'$' + getTotalPagarInputs().toFixed(2)"></span>
                    </button>
                    <p x-show="!pagosValidos()" class="text-[11px] text-red-500 text-center font-medium">Asegúrate de que la suma coincida con el total y rellenar referencias.</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                cat: 12, view: 'pizzas', search: '', cart: [], cartGroups: [], servicio: 3, mesa: '',
                comentariosGenerales: '', comentariosGeneralesTemp: '', modalComentarios: false,
                modalOpc: false, opcItem: null,

                modalPaq1: false, paq1Opt: 'Combinado (1 Hawaiana y 1 Pepperoni)', paqObj: null,
                modalPaq2: false, paq2Tipo: 'hamb', paq2Extra: '', paq2Pizza: '',
                modalPaq3: false, paq3Pizzas: [],
                modalIngredientes: false, ingTam: null, ingSel: [],
                modalMitades: false, mitTam: null, mitSel: [],
                modalRectangular: false, rectItem: null, rectSel: [],
                modalBarra: false, barraItem: null, barraSel: [],
                modalBebida: false, bebidaItem: null,
                modalMagno: false, magnoItem: null, magnoSel: [],

                modalCliente: false, 
                modalPago: false,
                searchClienteText: '', showClientesList: false,
                clienteSeleccionado: null, direccionesCliente: [], dirSeleccionada: null,
                clienteFormVisible: false, dirFormVisible: false,
                nuevoClienteData: { nombre: '', telefono: '' },
                nuevaDirData: { calle: '', manzana: '', lote: '', colonia: '', referencia: '' },

                pagos: {
                    efectivo: { activo: false, monto: 0, entregado: 0 },
                    tarjeta: { activo: false, monto: 0 },
                    transferencia: { activo: false, monto: 0, referencia: '' }
                },

                getListaTamanos() {
                    let d = this.cat === 12 ? dbPizzas : (this.cat === 2 ? dbMariscos : []);
                    if(this.search) d = d.filter(i => i.nombre.toLowerCase().includes(this.search.toLowerCase()));
                    return d;
                },
                getListaDirectos() {
                    let d = dbDirectos.filter(i => i.cat === this.cat);
                    if(this.search) d = d.filter(i => i.nombre.toLowerCase().includes(this.search.toLowerCase()));
                    return d;
                },
                getListaBebidas() {
                    let d = dbBebidas;
                    if(this.search) d = d.filter(i => i.nombre.toLowerCase().includes(this.search.toLowerCase()));
                    return d;
                },
                
                get clientesFiltrados() {
                    let listaSegura = Array.isArray(dbClientes) ? dbClientes : [];
                    if(!this.searchClienteText) return listaSegura; 
                    
                    let txt = this.searchClienteText.toLowerCase().trim();
                    return listaSegura.filter(c => {
                        return Object.values(c).some(val => 
                            String(val || '').toLowerCase().includes(txt)
                        );
                    });
                },

                getClienteNombre(cl) {
                    let ape = cl.apellido || cl.Apellido || '';
                    let nom = cl.nombre || cl.Nombre || cl.nombre_cliente || cl.cliente || 'Sin Nombre';
                    return (nom + ' ' + ape).trim();
                },
                getClienteTelefono(cl) {
                    return cl.telefono || cl.Telefono || cl.celular || cl.numero || 'Sin Teléfono';
                },
                
                abrirOpciones(item) { this.opcItem = item; this.modalOpc = true; },
                abrirBebida(item) { this.bebidaItem = item; this.modalBebida = true; },
                generateUID() { return Math.random().toString(36).substr(2, 9); },

                cleanSize(str) {
                    if (!str) return '';
                    let s = str.toLowerCase();
                    if(s.includes('chica')) return 'Chica';
                    if(s.includes('mediana') || s.includes('media')) return 'Mediana';
                    if(s.includes('grande')) return 'Grande';
                    if(s.includes('familiar')) return 'Familiar';
                    return str; 
                },

                // Extraccion dinámica de la variable PHP inyectada
                getPrecioOrilla(nombreBase) {
                    let n = nombreBase.toLowerCase();
                    if(n.includes('chica')) return dbPreciosOrilla.chica;
                    if(n.includes('mediana') || n.includes('media')) return dbPreciosOrilla.mediana;
                    if(n.includes('grande')) return dbPreciosOrilla.grande;
                    if(n.includes('familiar')) return dbPreciosOrilla.familiar;
                    return dbPreciosOrilla.chica; 
                },

                actualizarCarrito() {
                    let pizzasFlat = [];
                    let normals = [];

                    this.cart.forEach((cItem, index) => {
                        if (cItem.es_pizza && !cItem.is_magno) { 
                            let baseSize = this.cleanSize(cItem.nombre_base).toUpperCase();
                            cItem.subtotalBase = cItem.precioBase;
                            cItem.subtotal = cItem.precioBase + (cItem.orilla_queso ? cItem.precio_orilla : 0);
                            cItem.descuentoPromo = 0;
                            cItem.precioFinal = cItem.precioBase;

                            if (baseSize !== '') {
                                for (let i = 0; i < cItem.qty; i++) {
                                    pizzasFlat.push({ cartIndex: index, size: baseSize, price: cItem.precioBase, item: cItem });
                                }
                            }
                        } else {
                            cItem.subtotalBase = cItem.precioBase * cItem.qty;
                            
                            let extraOrillasPaq = (cItem.orillas_qty || 0) * (cItem.precio_orilla || 0) * cItem.qty;
                            let extraOrillaUnica = (cItem.orilla_queso ? cItem.precio_orilla * cItem.qty : 0);

                            cItem.subtotal = cItem.subtotalBase + extraOrillaUnica + extraOrillasPaq;
                            cItem.descuentoPromo = 0;
                            cItem.precioFinal = cItem.precioBase + (cItem.orilla_queso ? cItem.precio_orilla : 0) + ((cItem.orillas_qty || 0) * (cItem.precio_orilla || 0));
                            
                            normals.push({ cartIndex: index, item: cItem });
                        }
                    });

                    let grouped = pizzasFlat.reduce((acc, p) => {
                        acc[p.size] = acc[p.size] || [];
                        acc[p.size].push(p);
                        return acc;
                    }, {});

                    this.cartGroups = [];

                    for (let size in grouped) {
                        let pArr = grouped[size];
                        pArr.sort((a, b) => b.price - a.price);

                        for (let i = 0; i < pArr.length; i += 2) {
                            let p1 = pArr[i];
                            let p2 = pArr[i + 1];

                            let groupItems = [p1];
                            let subGroup = p1.price + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);
                            p1.item.precioCobrado = p1.price;
                            p1.item.precioFinal = p1.item.precioCobrado + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);

                            if (p2) {
                                p2.item.descuentoPromo += p2.price; 
                                p2.item.subtotal -= p2.price;
                                p2.item.precioCobrado = 0;
                                p2.item.precioFinal = 0 + (p2.item.orilla_queso ? p2.item.precio_orilla : 0);

                                subGroup += (p2.item.orilla_queso ? p2.item.precio_orilla : 0);
                                groupItems.push(p2);
                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', size: this.cleanSize(size), items: groupItems, subtotal: subGroup });
                            } else {
                                let desc = p1.price * 0.40;
                                p1.item.descuentoPromo += desc;
                                p1.item.subtotal -= desc;
                                p1.item.precioCobrado = p1.price - desc;
                                p1.item.precioFinal = p1.item.precioCobrado + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);

                                subGroup -= desc;
                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', size: this.cleanSize(size), items: groupItems, subtotal: subGroup });
                            }
                        }
                    }

                    normals.forEach(n => {
                        this.cartGroups.push({
                            id_grupo: this.generateUID(),
                            type: 'normal', cIdx: n.cartIndex, item: n.item, subtotal: n.item.subtotal
                        });
                    });

                    if(this.modalPago) this.autoFillPago('efectivo'); 
                },

                addPizzaToMainCart(obj) {
                    this.cart.push({ ...obj, qty: 1, uid: this.generateUID() });
                    this.actualizarCarrito();
                },

                addOpc(t) {
                    let cTam = this.cleanSize(t.tamano);
                    let nomFull = (this.cat === 12 ? 'Pizza ' : 'Mariscos ') + cTam;
                    this.addPizzaToMainCart({
                        db_id: t.id, col: (this.cat === 12 ? 'id_pizza' : 'id_maris'), tipo: 'pizza_normal', es_pizza: true, is_magno: false,
                        nombre_base: nomFull, variante: this.opcItem.nombre, precioBase: parseFloat(t.precio),
                        orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), comentario: ''
                    });
                    this.modalOpc = false;
                },

                addBebida(opc) {
                    let nomFull = this.bebidaItem.nombre + ' ' + opc.tamano;
                    let idx = this.cart.findIndex(i => i.db_id === opc.id && !i.es_pizza);
                    if(idx > -1) { 
                        this.cart[idx].qty++; 
                    } else { 
                        this.cart.push({ 
                            db_id: opc.id, col: 'id_refresco', tipo: 'directo', nombre_base: nomFull, variante: '', 
                            precioBase: parseFloat(opc.precio), qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID() 
                        }); 
                    }
                    this.actualizarCarrito();
                    this.modalBebida = false;
                },

                abrirMagnoGeneral() {
                    // Ahora usa directamente la variable de base de datos extraida desde PHP
                    this.magnoItem = { id: null, col: 'id_pizza', nombre: 'Magno', precio: dbMagnoPrice };
                    this.magnoSel = [];
                    this.modalMagno = true;
                },
                addMagnoEsp(esp) { if(this.magnoSel.length < 2) this.magnoSel.push(esp); },
                removeMagnoEsp(index) { this.magnoSel.splice(index, 1); },
                formatearMagnoPreview() {
                    if(this.magnoSel.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.magnoSel.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + '/2 ' + k); }
                    return parts.join(' / ');
                },
                addMagno() {
                    let pb = parseFloat(this.magnoItem.precio);
                    let varianteFinal = this.formatearMagnoPreview() + '\n• Incluye 1 Refresco de 2L';
                    let idx = this.cart.findIndex(i => i.is_magno && i.variante === varianteFinal && !i.orilla_queso);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { 
                        this.cart.push({ 
                            db_id: null, col: 'id_magno', tipo: 'directo', 
                            nombre_base: 'Magno', variante: varianteFinal, 
                            medios: this.magnoSel, 
                            precioBase: pb, qty: 1, es_pizza: false, is_magno: true, orilla_queso: false, precio_orilla: dbPreciosOrilla.familiar,
                            uid: this.generateUID()
                        }); 
                    }
                    this.actualizarCarrito();
                    this.modalMagno = false;
                },

                abrirRectangularGeneral() {
                    let baseItem = dbDirectos.find(d => d.cat === 11);
                    if(!baseItem) return alert('No hay pizzas rectangulares configuradas en la base de datos.');
                    this.rectItem = { id: baseItem.id, col: baseItem.col, nombre: 'Pizza Rectangular', precio: baseItem.precio };
                    this.rectSel = [];
                    this.modalRectangular = true;
                },
                addRectEsp(esp) { if(this.rectSel.length < 4) this.rectSel.push(esp); },
                removeRectEsp(index) { this.rectSel.splice(index, 1); },
                formatearCuartosPreview() {
                    if(this.rectSel.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.rectSel.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + '/4 ' + k); }
                    return parts.join(', ');
                },
                addRectangular() {
                    let pb = parseFloat(this.rectItem.precio);
                    let varianteFinal = this.formatearCuartosPreview();
                    let idx = this.cart.findIndex(i => i.db_id === this.rectItem.id && i.variante === varianteFinal);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { 
                        this.cart.push({ 
                            db_id: this.rectItem.id, col: this.rectItem.col, tipo: 'directo', 
                            nombre_base: this.rectItem.nombre, variante: varianteFinal, cuartos: this.rectSel,
                            precioBase: pb, qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID()
                        }); 
                    }
                    this.actualizarCarrito();
                    this.modalRectangular = false;
                },

                abrirBarraGeneral() {
                    let baseItem = dbDirectos.find(d => d.cat === 10);
                    if(!baseItem) return alert('No hay pizzas de barra configuradas en la base de datos.');
                    this.barraItem = { id: baseItem.id, col: baseItem.col, nombre: 'Pizza de Barra', precio: baseItem.precio };
                    this.barraSel = [];
                    this.modalBarra = true;
                },
                addBarraEsp(esp) { if(this.barraSel.length < 2) this.barraSel.push(esp); },
                removeBarraEsp(index) { this.barraSel.splice(index, 1); },
                formatearMediosPreview() {
                    if(this.barraSel.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.barraSel.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + '/2 ' + k); }
                    return parts.join(', ');
                },
                addBarra() {
                    let pb = parseFloat(this.barraItem.precio);
                    let varianteFinal = this.formatearMediosPreview();
                    let idx = this.cart.findIndex(i => i.db_id === this.barraItem.id && i.variante === varianteFinal);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { 
                        this.cart.push({ 
                            db_id: this.barraItem.id, col: this.barraItem.col, tipo: 'directo', 
                            nombre_base: this.barraItem.nombre, variante: varianteFinal, medios: this.barraSel,
                            precioBase: pb, qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID()
                        }); 
                    }
                    this.actualizarCarrito();
                    this.modalBarra = false;
                },

                addDirecto(p) {
                    if(p.cat === 11) return this.abrirRectangularGeneral();
                    if(p.cat === 10) return this.abrirBarraGeneral();

                    let idx = this.cart.findIndex(i => i.db_id === p.id && !i.es_pizza);
                    if(idx > -1) { 
                        this.cart[idx].qty++; 
                    } else { 
                        this.cart.push({ 
                            db_id: p.id, col: p.col, tipo: 'directo', nombre_base: p.nombre, variante: '', 
                            precioBase: parseFloat(p.precio), qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID() 
                        }); 
                    }
                    this.actualizarCarrito();
                },

                abrirPaquete(id) {
                    this.paqObj = dbPaquetes.find(p => p.id_paquete === id);
                    if(id === 1) { this.paq1Opt = 'Combinado (1 Hawaiana y 1 Pepperoni)'; this.modalPaq1 = true; }
                    if(id === 2) { this.paq2Tipo = 'hamb'; this.paq2Extra = ''; this.paq2Pizza = ''; this.modalPaq2 = true; }
                    if(id === 3) { this.paq3Pizzas = []; this.modalPaq3 = true; }
                },
                addPaq(id, variante) {
                    let pb = parseFloat(this.paqObj.precio);
                    let maxPizzas = id === 1 ? 2 : (id === 2 ? 1 : 3);
                    let idx = this.cart.findIndex(i => i.db_id === id && i.tipo === 'paq' && i.variante === variante && i.orillas_qty === 0);
                    
                    if(idx > -1) { this.cart[idx].qty++; }
                    else { 
                        this.cart.push({ 
                            db_id: id, col: 'id_paquete', tipo: 'paq', nombre_base: 'Paquete '+id, variante: variante, 
                            precioBase: pb, qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID(),
                            orillas_qty: 0, max_orillas: maxPizzas, precio_orilla: dbPreciosOrilla.grande // Usa PHP variable
                        }); 
                    }
                    this.actualizarCarrito();
                },
                addPaq1() { this.addPaq(1, this.paq1Opt); this.modalPaq1 = false; },
                addPaq2() { this.addPaq(2, this.paq2Extra + ' + Pizza ' + this.paq2Pizza); this.modalPaq2 = false; },
                addPaq3Esp(esp) { if(this.paq3Pizzas.length < 3) this.paq3Pizzas.push(esp); },
                removePaq3Esp(index) { this.paq3Pizzas.splice(index, 1); },
                formatearPaq3Preview() {
                    if(this.paq3Pizzas.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.paq3Pizzas.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + ' ' + k); }
                    return parts.join(', ');
                },
                addPaq3() { this.addPaq(3, this.formatearPaq3Preview()); this.modalPaq3 = false; },

                incrementarOrillaPaq(uid) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1 && this.cart[idx].orillas_qty < this.cart[idx].max_orillas) {
                        this.cart[idx].orillas_qty++;
                        this.actualizarCarrito();
                    }
                },
                decrementarOrillaPaq(uid) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1 && this.cart[idx].orillas_qty > 0) {
                        this.cart[idx].orillas_qty--;
                        this.actualizarCarrito();
                    }
                },

                toggleMitad(nom) { let idx = this.mitSel.indexOf(nom); if(idx > -1) this.mitSel.splice(idx, 1); else if(this.mitSel.length < 2) this.mitSel.push(nom); },
                addMitad() {
                    let cTam = this.cleanSize(this.mitTam.tamano);
                    let nomFull = 'Mitad y Mitad ' + cTam;
                    this.addPizzaToMainCart({ db_id: null, col: 'id_pizza', tipo: 'piz_mitad', nombre_base: nomFull, variante: this.mitSel[0] + ' / ' + this.mitSel[1], precioBase: parseFloat(this.mitTam.precio), es_pizza: true, is_magno: false, orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), mitad1: this.mitSel[0], mitad2: this.mitSel[1], tamano: this.mitTam.tamano });
                    this.modalMitades = false; this.mitTam = null; this.mitSel = [];
                },
                precioPizzaIngredientes() { return !this.ingTam ? 0 : parseFloat(this.ingTam.precio); }, 
                addIng() {
                    let cTam = this.cleanSize(this.ingTam.tamano);
                    let nomFull = 'Personalizada ' + cTam;
                    this.addPizzaToMainCart({ db_id: this.ingTam.id_tamañop, col: 'id_pizza', tipo: 'piz_ing', nombre_base: nomFull, variante: 'Ings: ' + this.ingSel.join(', '), precioBase: this.precioPizzaIngredientes(), es_pizza: true, is_magno: false, orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), ingredientes_extra: this.ingSel });
                    this.modalIngredientes = false; this.ingTam = null; this.ingSel = [];
                },

                recalc() { this.actualizarCarrito(); },
                toggleOrilla(uid, checked) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1) this.cart[idx].orilla_queso = checked;
                    this.actualizarCarrito();
                },
                clonePizza(item) {
                    let clone = JSON.parse(JSON.stringify(item));
                    clone.uid = this.generateUID();
                    clone.orilla_queso = false; 
                    this.cart.push(clone);
                    this.actualizarCarrito();
                },
                eliminarItemByUid(uid) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1) this.cart.splice(idx, 1);
                    this.actualizarCarrito();
                },
                updateNormalQty(item, mod) {
                    let idx = this.cart.findIndex(c => c.uid === item.uid);
                    if(idx > -1) {
                        this.cart[idx].qty += mod;
                        if(this.cart[idx].qty < 1) this.cart[idx].qty = 1;
                    }
                    this.actualizarCarrito();
                },

                abrirModalComentarios() { this.comentariosGeneralesTemp = this.comentariosGenerales; this.modalComentarios = true; },
                guardarComentarios() { this.comentariosGenerales = this.comentariosGeneralesTemp; this.modalComentarios = false; },
                getTotal() { return this.cartGroups.reduce((s, g) => s + g.subtotal, 0); },
                nomServicio() { const s = {1: 'Comer Aqui', 2: 'Para Llevar', 3: 'A Domicilio'}; return s[this.servicio] || 'Seleccionar'; },

                // LOGICA DOMICILIO
                toggleFormNuevoCliente() {
                    this.clienteFormVisible = !this.clienteFormVisible;
                    if(this.clienteFormVisible) {
                        this.clienteSeleccionado = null;
                        this.searchClienteText = '';
                        this.direccionesCliente = [];
                        this.dirSeleccionada = null;
                    }
                },
                seleccionarCliente(cl) {
                    this.clienteSeleccionado = cl;
                    this.searchClienteText = this.getClienteNombre(cl);
                    this.showClientesList = false;
                    this.clienteFormVisible = false;
                    this.dirFormVisible = false;
                    
                    let idClieBuscar = cl.id_cliente || cl.id_clie || cl.id;
                    this.direccionesCliente = dbDirecciones.filter(d => d.id_cliente == idClieBuscar || d.id_clie == idClieBuscar);
                    
                    if(this.direccionesCliente.length > 0) {
                        this.dirSeleccionada = this.direccionesCliente[0].id_direccion || this.direccionesCliente[0].id_dir;
                    } else {
                        this.dirSeleccionada = null;
                    }
                },
                esDomicilioValido() {
                    if (this.clienteFormVisible) {
                        if(!this.nuevoClienteData.nombre) return false;
                        if(!this.dirFormVisible && this.direccionesCliente.length === 0) {
                            this.dirFormVisible = true;
                        }
                    } else {
                        if(!this.clienteSeleccionado) return false;
                    }

                    if(this.dirFormVisible) {
                        if(!this.nuevaDirData.calle) return false;
                    } else {
                        if(!this.dirSeleccionada) return false;
                    }
                    return true;
                },
                confirmarDomicilio() {
                    if(this.esDomicilioValido()) {
                        this.modalCliente = false;
                        this.abrirModalPago();
                    }
                },

                // LOGICA MULTIPAGO
                abrirModalPago() {
                    this.pagos = {
                        efectivo: { activo: true, monto: this.getTotal(), entregado: null },
                        tarjeta: { activo: false, monto: null },
                        transferencia: { activo: false, monto: null, referencia: '' }
                    };
                    this.modalPago = true;
                },
                autoFillPago(tipo) {
                    if(this.pagos[tipo].activo) {
                        let falta = this.faltaPagar();
                        if(falta > 0) {
                            this.pagos[tipo].monto = falta;
                        }
                    } else {
                        this.pagos[tipo].monto = null;
                    }
                },
                getTotalPagarInputs() {
                    let pE = this.pagos.efectivo.activo ? parseFloat(this.pagos.efectivo.monto || 0) : 0;
                    let pT = this.pagos.tarjeta.activo ? parseFloat(this.pagos.tarjeta.monto || 0) : 0;
                    let pTr = this.pagos.transferencia.activo ? parseFloat(this.pagos.transferencia.monto || 0) : 0;
                    return pE + pT + pTr;
                },
                faltaPagar() {
                    let diff = this.getTotal() - this.getTotalPagarInputs();
                    return parseFloat(diff.toFixed(2));
                },
                pagosValidos() {
                    if(this.faltaPagar() !== 0) return false;
                    if(this.pagos.transferencia.activo && (!this.pagos.transferencia.referencia || this.pagos.transferencia.referencia.trim() === '')) return false;
                    return true;
                },

                // ENVIO FINAL
                procesarOrden() {
                    if(this.servicio === 1 && !this.mesa) return alert("Ingrese el número de mesa.");
                    if(this.servicio === 3) {
                        this.modalCliente = true; 
                    } else {
                        this.abrirModalPago(); 
                    }
                },

                procesarOrdenFinal() {
                    if(!this.pagosValidos()) return;

                    let cartPayload = [];
                    this.cartGroups.forEach(g => {
                        if(g.type === 'pizza_pair') {
                            g.items.forEach(p => { cartPayload.push({ ...p.item, precioFinal: p.item.precioFinal, qty: 1 }); });
                        } else {
                            cartPayload.push({ ...g.item, precioFinal: g.item.precioFinal });
                        }
                    });

                    let pagosToSend = [];
                    if(this.pagos.efectivo.activo && this.pagos.efectivo.monto > 0) {
                        pagosToSend.push({ id_metpago: 2, monto: this.pagos.efectivo.monto, entregado: this.pagos.efectivo.entregado || this.pagos.efectivo.monto }); 
                    }
                    if(this.pagos.tarjeta.activo && this.pagos.tarjeta.monto > 0) {
                        pagosToSend.push({ id_metpago: 1, monto: this.pagos.tarjeta.monto }); 
                    }
                    if(this.pagos.transferencia.activo && this.pagos.transferencia.monto > 0) {
                        pagosToSend.push({ id_metpago: 3, monto: this.pagos.transferencia.monto, referencia: this.pagos.transferencia.referencia });
                    }

                    let reqBody = {
                        _token: '{{ csrf_token() }}', 
                        tipo_servicio: this.servicio, mesa: this.mesa, 
                        comentarios: this.comentariosGenerales, 
                        total: this.getTotal(), 
                        carrito: cartPayload, 
                        pagos: pagosToSend
                    };

                    if(this.servicio === 3) {
                        if(this.clienteFormVisible) reqBody.nuevo_cliente = this.nuevoClienteData;
                        else reqBody.id_clie = this.clienteSeleccionado.id_cliente || this.clienteSeleccionado.id_clie;

                        if(this.dirFormVisible) reqBody.nueva_direccion = this.nuevaDirData;
                        else reqBody.id_dir = this.dirSeleccionada;
                    }

                    fetch("{{ route('ventas.pos.store') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(reqBody)
                    }).then(r => r.json()).then(res => {
                        if(res.success) { 
                            this.cart = []; this.actualizarCarrito(); this.mesa = ''; this.comentariosGenerales = '';
                            this.modalPago = false;
                            window.open('/venta/pos/ticket/' + res.id_venta, 'Ticket', 'width=400,height=600'); 
                        } else alert("Error al guardar: " + res.message);
                    });
                }
            }));
        });
    </script>
@endif
@endsection