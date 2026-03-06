@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
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
        const dbDirectos = {!! json_encode($directos) !!};
        const dbPaquetes = {!! json_encode($paquetes) !!};
        const dbIngredientes = {!! json_encode($ingredientes) !!};
        const dbTamanosBase = {!! json_encode($tamanos_base) !!};
        const dbEspecialidades = {!! json_encode($especialidades_lista) !!};
    </script>

    <div class="w-full min-h-[90vh] bg-[#f8f9fa] p-4 lg:p-6 font-sans text-[#212529]" x-data="posApp()">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 max-w-[1600px] mx-auto">
            
            <div class="lg:col-span-8 flex flex-col gap-5">
                
                {{-- BOTONES SUPERIORES --}}
                <div class="flex flex-wrap gap-2">
                    <button @click="abrirPaquete(1)" class="bg-[#ffc107] text-[#212529] px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-all">Paquete 1</button>
                    <button @click="abrirPaquete(2)" class="bg-[#ffc107] text-[#212529] px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-all">Paquete 2</button>
                    <button @click="abrirPaquete(3)" class="bg-[#ffc107] text-[#212529] px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-all">Paquete 3</button>
                    <button @click="modalIngredientes = true" class="bg-[#fd7e14] text-white px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-all">Por Ingrediente</button>
                    <button @click="modalMitades = true; mitSel = []; mitTam = null;" class="bg-[#dc3545] text-white px-5 py-2 rounded-md text-[14px] font-bold shadow-sm hover:brightness-95 transition-all">Mitad y Mitad</button>
                </div>

                {{-- BARRA DE CATEGORÍAS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 flex flex-col xl:flex-row justify-between items-center gap-4">
                    <div class="flex flex-wrap gap-1.5 items-center w-full xl:w-auto">
                        <button @click="cat = 12; view = 'pizzas'" :class="cat === 12 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Pizzas</button>
                        <button @click="cat = 2; view = 'pizzas'" :class="cat === 2 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Mariscos</button>
                        <button @click="cat = 11; view = 'otros'" :class="cat === 11 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Rectangular</button>
                        <button @click="cat = 10; view = 'otros'" :class="cat === 10 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors">Barra</button>
                        
                        <div class="relative" x-data="{ openExtras: false }">
                            <button @click="openExtras = !openExtras" :class="[1,5,6,7,8,9].includes(cat) ? 'bg-[#adb5bd] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-5 py-2 rounded-md text-[13px] font-bold transition-colors flex items-center gap-1">
                                Extras <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="openExtras" @click.away="openExtras = false" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1">
                                @foreach($categorias_extras as $catEx)
                                    <button @click="cat = {{ $catEx->id_cat }}; view = 'otros'; openExtras = false" class="w-full text-left px-4 py-2.5 text-[13px] font-bold text-[#495057] hover:bg-gray-50">{{ $catEx->descripcion }}</button>
                                @endforeach
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
                    <div x-show="view === 'pizzas'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        <template x-for="p in getListaTamanos()" :key="p.nombre">
                            <button @click="abrirOpciones(p)" class="bg-white rounded-[10px] shadow-sm border border-gray-100 border-l-[4px] border-l-[#ffc107] p-5 flex flex-col justify-between items-start text-left h-[105px] hover:shadow-md transition">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight" x-text="p.nombre"></span>
                                <span class="text-[#fd7e14] text-[13px] font-bold">Ver opciones &rarr;</span>
                            </button>
                        </template>
                    </div>
                    <div x-show="view === 'otros'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" x-cloak>
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
                <div class="p-6 pb-4 border-b border-gray-100">
                    <h2 class="text-[24px] font-black text-[#212529]">Pedido Actual</h2>
                    <p x-show="cartGroups.length === 0" class="text-[#6c757d] text-[14px] mt-1">Sin productos en el carrito</p>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 scrollbar-hide bg-[#f8f9fa]">
                    
                    <template x-for="(group, gIdx) in cartGroups" :key="group.id_grupo">
                        <div>
                            
                            {{-- TARJETAS PARA PIZZAS AGRUPADAS (MAXIMO 2 POR CAJA - LIMPIO) --}}
                            <template x-if="group.type === 'pizza_pair'">
                                <div class="bg-white border border-gray-200 rounded-[8px] shadow-sm mb-4">
                                    
                                    {{-- Cabecera Estándar (Sin letreros de Promo) --}}
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
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                    </button>
                                                </div>

                                                {{-- Controles de Cantidad y Precio Base para la Pizza --}}
                                                <div class="flex items-center justify-between mt-2 mb-2">
                                                    <div class="flex items-center bg-[#e9ecef] rounded border border-gray-200">
                                                        <button @click="eliminarItemByUid(p.item.uid)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">-</button>
                                                        <span class="w-8 h-7 flex justify-center items-center font-bold text-[#212529] bg-white border-x border-gray-200 text-[13px]">1</span>
                                                        <button @click="clonePizza(p.item)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">+</button>
                                                    </div>
                                                    <span class="text-[12px] text-[#6c757d] font-medium" x-text="'Base: $' + parseFloat(p.price).toFixed(2)"></span>
                                                </div>

                                                {{-- Opciones Extra (Queso) y Precio Final de esta Pizza --}}
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

                                    <div class="px-4 py-3 bg-gray-50 flex justify-between items-center border-t border-gray-200 rounded-b-[8px]">
                                        <span class="text-gray-500 text-[12px] font-bold uppercase tracking-wider">Subtotal Caja</span>
                                        <span class="font-black text-[#212529] text-[18px]" x-text="'$' + group.subtotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </template>

                            {{-- TARJETAS PARA OTROS PRODUCTOS (Hamburguesas, Refrescos, Paquetes) --}}
                            <template x-if="group.type === 'normal'">
                                <div class="border border-gray-200 rounded-[8px] p-4 bg-white shadow-sm mb-4 relative">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-[#212529] text-[14px] pr-6 leading-tight" x-text="group.item.nombre_base"></h4>
                                        <button @click="eliminarItemByUid(group.item.uid)" class="text-[#dc3545] hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded absolute right-4 top-4 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="flex items-center bg-[#e9ecef] rounded border border-gray-200">
                                            <button @click="group.item.qty > 1 ? updateNormalQty(group.item, -1) : null" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">-</button>
                                            <span class="w-8 h-7 flex justify-center items-center font-bold text-[#212529] bg-white border-x border-gray-200 text-[13px]" x-text="group.item.qty"></span>
                                            <button @click="updateNormalQty(group.item, 1)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300">+</button>
                                        </div>
                                        <span class="text-[12px] text-[#6c757d] font-medium" x-text="'| c/u: $' + parseFloat(group.item.precioBase).toFixed(2)"></span>
                                    </div>
                                    
                                    <div x-show="group.item.variante" class="bg-[#f8f9fa] border border-gray-200 rounded-[6px] p-2 mt-2">
                                        <span class="text-[12px] text-[#495057] block font-medium" x-text="group.item.variante"></span>
                                    </div>

                                    <div class="text-right font-black text-[#212529] text-[16px] mt-3 border-t border-gray-100 pt-2">
                                        <span x-text="'$' + group.subtotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>

                {{-- ZONA COBRO FINAL --}}
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
                                <button @click="servicio = 3; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#fd7e14] hover:bg-gray-50 border-b border-gray-100">🚚 A Domicilio</button>
                                <button @click="servicio = 1; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#495057] hover:bg-gray-50 border-b border-gray-100">🍽️ Comer Aqui</button>
                                <button @click="servicio = 2; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#495057] hover:bg-gray-50 border-b border-gray-100">🛍️ Para Llevar</button>
                                <button @click="servicio = 4; open = false" class="w-full text-left px-4 py-3 text-[14px] font-bold text-[#495057] hover:bg-gray-50">📝 P. Especial</button>
                            </div>
                        </div>

                        <button @click="send()" :disabled="cart.length === 0" :class="cart.length === 0 ? 'bg-[#e9ecef] text-[#adb5bd] cursor-not-allowed' : 'bg-[#e9ecef] hover:bg-[#dee2e6] text-[#212529]'" class="flex-1 font-black text-[15px] transition-colors">
                            Enviar a Cocina
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODALES ESPECÍFICOS                                                       --}}
        {{-- ========================================================================= --}}

        {{-- MODAL OPCIONES NORMAL (Catálogo Pizzas) --}}
        <div x-show="modalOpc" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[350px] flex flex-col overflow-hidden" @click.away="modalOpc = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-[18px] font-bold text-[#212529]" x-text="opcItem?.nombre"></h2>
                    <button @click="modalOpc = false" class="text-gray-400 hover:text-black font-bold text-xl">&times;</button>
                </div>
                <div class="p-5 bg-[#f8f9fa] space-y-3 max-h-[50vh] overflow-y-auto scrollbar-hide">
                    <p class="text-[13px] text-gray-500 mb-1 font-bold">Selecciona el tamaño:</p>
                    <template x-for="t in opcItem?.tamanos" :key="t.id">
                        <button @click="addOpc(t)" class="w-full flex justify-between items-center bg-white border border-gray-200 rounded-[8px] p-4 hover:border-[#fd7e14] hover:shadow-sm transition-all">
                            <span class="font-bold text-[#212529] text-[14px]" x-text="cleanSize(t.tamano)"></span>
                            <span class="font-black text-[#28a745] text-[15px]" x-text="'$' + parseFloat(t.precio).toFixed(2)"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- PAQUETE 1 --}}
        <div x-show="modalPaq1" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-[400px] flex flex-col overflow-hidden" @click.away="modalPaq1 = false">
                <div class="p-6 pb-4 relative border-b border-gray-100 bg-[#ffc107]">
                    <button @click="modalPaq1 = false" class="absolute top-4 right-4 text-black/60 hover:text-black font-bold text-2xl">&times;</button>
                    <h2 class="text-2xl font-black text-black mb-1">Paquete 1</h2>
                </div>
                <div class="p-6 bg-[#f8f9fa]">
                    <ul class="list-disc pl-5 text-[14px] font-medium text-gray-600 mb-5">
                        <li>2 Pizzas Grandes</li>
                        <li>1 Refresco de 2L Jarrito</li>
                    </ul>
                    <p class="text-[14px] font-bold text-black mb-2">Selecciona tus pizzas:</p>
                    <div class="space-y-3 mb-2">
                        <button @click="paq1Opt = 'Combinado (1 Hawaiana y 1 Pepperoni)'" class="w-full text-left block border rounded-[8px] p-4 transition-all" :class="paq1Opt === 'Combinado (1 Hawaiana y 1 Pepperoni)' ? 'bg-[#fff9c4] border-[#ffc107] shadow-sm' : 'bg-white border-gray-200'">
                            <span class="block text-[14px] font-bold text-black">Combinado</span>
                            <span class="block text-[12px] text-gray-500">1 Hawaiana y 1 Pepperoni</span>
                        </button>
                        <button @click="paq1Opt = '2 Hawaianas'" class="w-full text-left block border rounded-[8px] p-4 transition-all" :class="paq1Opt === '2 Hawaianas' ? 'bg-[#fff9c4] border-[#ffc107] shadow-sm' : 'bg-white border-gray-200'">
                            <span class="text-[14px] font-bold text-black">2 Hawaianas</span>
                        </button>
                        <button @click="paq1Opt = '2 Pepperoni'" class="w-full text-left block border rounded-[8px] p-4 transition-all" :class="paq1Opt === '2 Pepperoni' ? 'bg-[#fff9c4] border-[#ffc107] shadow-sm' : 'bg-white border-gray-200'">
                            <span class="text-[14px] font-bold text-black">2 Pepperoni</span>
                        </button>
                    </div>
                </div>
                <div class="p-5 flex gap-3 border-t border-gray-100 bg-white items-center justify-between">
                    <span class="font-black text-[#28a745] text-[20px]">Precio: $<span x-text="paqObj ? parseFloat(paqObj.precio).toFixed(2) : '0.00'"></span></span>
                    <button @click="addPaq1()" :disabled="!paq1Opt" :class="!paq1Opt ? 'opacity-50' : ''" class="bg-[#ffc107] hover:bg-[#e0a800] text-[#212529] font-bold py-3 px-6 rounded-lg text-[14px]">Agregar</button>
                </div>
            </div>
        </div>

        {{-- PAQUETE 2 --}}
        <div x-show="modalPaq2" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-[450px] flex flex-col max-h-[90vh] overflow-hidden" @click.away="modalPaq2 = false">
                <div class="p-6 relative border-b border-gray-100 bg-[#ffc107]">
                    <button @click="modalPaq2 = false" class="absolute top-4 right-4 text-black/60 hover:text-black font-bold text-2xl">&times;</button>
                    <h2 class="text-2xl font-black text-black mb-1">Paquete 2</h2>
                </div>
                <div class="p-6 overflow-y-auto flex-1 space-y-5 bg-[#f8f9fa] scrollbar-hide">
                    <div>
                        <p class="text-[13px] font-bold text-gray-800 mb-2">Selecciona el tipo de producto:</p>
                        <div class="flex rounded-md overflow-hidden border border-gray-300 bg-white">
                            <button @click="paq2Tipo = 'hamb'; paq2Extra = ''" :class="paq2Tipo === 'hamb' ? 'bg-black text-white font-bold' : 'text-gray-600'" class="flex-1 py-2.5 text-[13px] transition-colors">Hamburguesa</button>
                            <button @click="paq2Tipo = 'alitas'; paq2Extra = ''" :class="paq2Tipo === 'alitas' ? 'bg-black text-white font-bold' : 'text-gray-600'" class="flex-1 py-2.5 text-[13px] transition-colors">Alitas</button>
                        </div>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-black mb-2" x-text="paq2Tipo === 'hamb' ? 'Selecciona tu hamburguesa:' : 'Selecciona tus alitas:'"></p>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-if="paq2Tipo === 'hamb'">
                                <button @click="paq2Extra = 'Sencilla de Pollo'" :class="paq2Extra === 'Sencilla de Pollo' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] text-left font-bold text-black shadow-sm">Sencilla de Pollo</button>
                            </template>
                            <template x-if="paq2Tipo === 'hamb'">
                                <button @click="paq2Extra = 'Sencilla de Res'" :class="paq2Extra === 'Sencilla de Res' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] text-left font-bold text-black shadow-sm">Sencilla de Res</button>
                            </template>
                            <template x-if="paq2Tipo === 'alitas'">
                                <button @click="paq2Extra = 'Alitas BBQ'" :class="paq2Extra === 'Alitas BBQ' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] text-left font-bold text-black shadow-sm">Alitas BBQ</button>
                            </template>
                            <template x-if="paq2Tipo === 'alitas'">
                                <button @click="paq2Extra = 'Alitas Adobadas'" :class="paq2Extra === 'Alitas Adobadas' ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-3 text-[13px] text-left font-bold text-black shadow-sm">Alitas Adobadas</button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-black mb-2">Selecciona tu Pizza Grande:</p>
                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1 scrollbar-hide pb-2">
                            <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                                <button @click="paq2Pizza = esp.nombre" :class="paq2Pizza === esp.nombre ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200'" class="border rounded-[8px] p-2.5 text-[12px] text-left text-black font-bold shadow-sm transition-colors" x-text="esp.nombre"></button>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="p-5 flex gap-3 border-t border-gray-100 bg-white justify-between items-center">
                    <span class="font-black text-[#28a745] text-[20px]">Precio: $<span x-text="paqObj ? parseFloat(paqObj.precio).toFixed(2) : '0.00'"></span></span>
                    <button @click="addPaq2()" :disabled="!paq2Extra || !paq2Pizza" :class="(!paq2Extra || !paq2Pizza) ? 'opacity-50' : ''" class="bg-[#ffc107] hover:bg-[#e0a800] text-[#212529] font-bold py-3 px-6 rounded-lg text-[14px]">Agregar</button>
                </div>
            </div>
        </div>

        {{-- PAQUETE 3 --}}
        <div x-show="modalPaq3" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-[450px] flex flex-col max-h-[90vh] overflow-hidden" @click.away="modalPaq3 = false">
                <div class="p-6 relative border-b border-gray-100 bg-[#ffc107]">
                    <button @click="modalPaq3 = false" class="absolute top-4 right-4 text-black/60 hover:text-black font-bold text-2xl">&times;</button>
                    <h2 class="text-2xl font-black text-black mb-1">Paquete 3</h2>
                </div>
                <div class="p-6 overflow-y-auto flex-1 bg-[#f8f9fa] scrollbar-hide">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-[13px] font-bold text-black">Selecciona 3 Pizzas Grandes:</p>
                        <span class="text-[12px] font-black bg-white px-2 py-1 rounded shadow-sm border border-gray-200" :class="paq3Pizzas.length === 3 ? 'text-green-600' : 'text-gray-500'" x-text="paq3Pizzas.length + '/3'"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border border-gray-100 rounded-lg p-2">
                        <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                            <button @click="togglePaq3(esp.nombre)" 
                                    :class="paq3Pizzas.includes(esp.nombre) ? 'border-[#ffc107] bg-[#fff9c4]' : 'bg-white border-gray-200 hover:bg-gray-50'" 
                                    class="border rounded-[8px] p-3 text-[12px] font-medium text-left text-black shadow-sm transition-colors" x-text="esp.nombre"></button>
                        </template>
                    </div>
                </div>
                <div class="p-5 flex gap-3 border-t border-gray-100 bg-white justify-between items-center">
                    <span class="font-black text-[#28a745] text-[20px]">Precio: $<span x-text="paqObj ? parseFloat(paqObj.precio).toFixed(2) : '0.00'"></span></span>
                    <button @click="addPaq3()" :disabled="paq3Pizzas.length !== 3" :class="paq3Pizzas.length !== 3 ? 'opacity-50' : ''" class="bg-[#ffc107] hover:bg-[#e0a800] text-[#212529] font-bold py-3 px-6 rounded-lg text-[14px]">Agregar</button>
                </div>
            </div>
        </div>

        {{-- MITAD Y MITAD (Rojo) --}}
        <div x-show="modalMitades" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-[750px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalMitades = false">
                <div class="bg-[#dc3545] p-5 flex justify-between items-center text-white">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v4h-2zm0 6h2v2h-2z"/></svg> Mitades
                    </h2>
                    <button @click="modalMitades = false" class="hover:text-gray-200 font-bold text-2xl leading-none">&times;</button>
                </div>
                
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    <div class="w-full md:w-[65%] p-6 overflow-y-auto border-r border-gray-100 space-y-6 bg-[#f8f9fa] scrollbar-hide">
                        <div>
                            <p class="font-bold text-[15px] text-black mb-3">1. Selecciona el tamaño:</p>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                <template x-for="tam in dbTamanosBase" :key="tam.id_tamañop">
                                    <button @click="mitTam = tam; mitSel = []" :class="mitTam?.id_tamañop === tam.id_tamañop ? 'border-red-500 bg-red-50 shadow' : 'border-gray-200 bg-white'" class="border rounded-[8px] py-4 text-center transition-all">
                                        <span class="block font-bold text-black text-[14px]" x-text="cleanSize(tam.tamano)"></span>
                                        <span class="block font-black text-[#dc3545] text-[15px] mt-1" x-text="'$' + parseFloat(tam.precio).toFixed(2)"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="mitTam">
                            <div class="flex justify-between items-center mb-3">
                                <p class="font-bold text-[15px] text-black">2. Selecciona 2 especialidades:</p>
                                <span class="text-[12px] font-black bg-white border border-gray-200 px-2 py-1 rounded shadow-sm" :class="mitSel.length === 2 ? 'text-[#dc3545]' : 'text-gray-500'" x-text="mitSel.length + '/2'"></span>
                            </div>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                <template x-for="esp in dbEspecialidades" :key="esp.id_esp">
                                    <button @click="toggleMitad(esp.nombre)" :class="mitSel.includes(esp.nombre) ? 'border-red-500 bg-red-50 text-red-700' : 'bg-white border-gray-200 text-gray-700 hover:border-red-300'" class="border rounded-[8px] p-3 text-[12px] font-bold text-left transition-colors shadow-sm">
                                        <span x-text="esp.nombre"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-[35%] bg-white p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-[18px] font-black text-black border-b border-gray-200 pb-3 mb-5">Resumen</h3>
                            <p class="text-[12px] font-bold text-gray-400 uppercase tracking-wide mb-1">Tamaño</p>
                            <p class="text-[16px] font-black text-[#dc3545] mb-5" x-text="mitTam ? cleanSize(mitTam.tamano) : '---'"></p>
                            <p class="text-[12px] font-bold text-gray-400 uppercase tracking-wide mb-2">Especialidades</p>
                            <div class="space-y-2">
                                <div class="border rounded-[8px] p-3 text-[13px]" :class="!mitSel[0] ? 'text-gray-400 border-dashed border-gray-300 bg-gray-50' : 'text-black font-bold border-gray-200 bg-white shadow-sm'" x-text="mitSel[0] ? '1/2 ' + mitSel[0] : 'Selecciona primera mitad'"></div>
                                <div class="border rounded-[8px] p-3 text-[13px]" :class="!mitSel[1] ? 'text-gray-400 border-dashed border-gray-300 bg-gray-50' : 'text-black font-bold border-gray-200 bg-white shadow-sm'" x-text="mitSel[1] ? '2/2 ' + mitSel[1] : 'Selecciona segunda mitad'"></div>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-gray-500 text-[14px] font-bold">Base</span>
                                <span class="font-black text-[#28a745] text-[26px] leading-none" x-text="'$' + (mitTam ? parseFloat(mitTam.precio).toFixed(2) : '0.00')"></span>
                            </div>
                            <button @click="addMitad()" :disabled="mitSel.length !== 2 || !mitTam" :class="(mitSel.length !== 2 || !mitTam) ? 'bg-[#ced4da] text-gray-500 cursor-not-allowed' : 'bg-[#dc3545] text-white hover:bg-red-700 shadow-md'" class="w-full font-bold py-3.5 rounded-[8px] text-[14px] transition-all mb-2">Añadir al Carrito</button>
                            <button @click="modalMitades = false" class="w-full text-gray-500 hover:text-black font-bold text-[13px] py-2">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- POR INGREDIENTES (Naranja) --}}
        <div x-show="modalIngredientes" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-[600px] flex flex-col h-[85vh] overflow-hidden" @click.away="modalIngredientes = false">
                <div class="bg-[#fd7e14] p-5 flex justify-between items-center text-white">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.43 2 5.23 3.54 3.01 6L12 22l8.99-16C18.77 3.54 15.57 2 12 2zM7 7c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm5 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg> Por Ingrediente
                    </h2>
                    <button @click="modalIngredientes = false" class="hover:text-orange-200 font-bold text-2xl leading-none">&times;</button>
                </div>
                <div class="flex-1 overflow-y-auto p-6 bg-[#f8f9fa] space-y-6 scrollbar-hide">
                    <div>
                        <p class="font-bold text-[14px] text-black mb-3">1. Selecciona el tamaño base:</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <template x-for="tam in dbTamanosBase" :key="tam.id_tamañop">
                                <button @click="ingTam = tam" :class="ingTam?.id_tamañop === tam.id_tamañop ? 'border-orange-500 bg-orange-50 shadow' : 'border-gray-200 bg-white hover:border-orange-300'" class="border rounded-[8px] py-4 text-center transition-all">
                                    <span class="block font-bold text-black text-[14px]" x-text="cleanSize(tam.tamano)"></span>
                                    <span class="block font-black text-[#28a745] text-[15px] mt-1" x-text="'Base: $' + parseFloat(tam.precio).toFixed(2)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div x-show="ingTam">
                        <div class="flex justify-between items-center mb-3">
                            <p class="font-bold text-[14px] text-black">2. Elige tus ingredientes:</p>
                            <span class="text-[11px] font-black text-white bg-[#fd7e14] px-2 py-1 rounded shadow-sm">+$15.00 c/u</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white border border-gray-200 rounded-[8px] p-4 shadow-sm">
                            <template x-for="ing in dbIngredientes" :key="ing.id_ingrediente">
                                <label class="flex items-center gap-2 cursor-pointer text-[13px] text-[#495057] font-medium p-1.5 hover:bg-orange-50 rounded transition-colors">
                                    <input type="checkbox" :value="ing.ingrediente" x-model="ingSel" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-4 h-4">
                                    <span x-text="ing.ingrediente"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="p-5 flex gap-4 bg-white border-t border-gray-200 items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[12px] text-gray-400 font-bold uppercase tracking-wider">Total Pizza</span>
                        <span class="font-black text-[#28a745] text-[26px] leading-none" x-text="'$' + precioPizzaIngredientes().toFixed(2)"></span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="modalIngredientes = false" class="bg-[#e9ecef] hover:bg-[#dee2e6] text-[#495057] font-bold px-6 py-3.5 rounded-[8px] text-[14px] transition-colors">Cancelar</button>
                        <button @click="addIng()" :disabled="!ingTam || ingSel.length === 0" :class="(!ingTam || ingSel.length === 0) ? 'bg-[#ced4da] text-white cursor-not-allowed' : 'bg-[#fd7e14] hover:bg-[#e36b0c] text-white shadow-md'" class="font-bold px-6 py-3.5 rounded-[8px] text-[14px] transition-all">Añadir al Carrito</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL COMENTARIOS GENERALES --}}
        <div x-show="modalComentarios" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[450px] max-w-full overflow-hidden" @click.away="modalComentarios = false">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-[#212529] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#ffc107]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                        Nota General del Pedido
                    </h2>
                </div>
                <div class="p-6">
                    <textarea x-model="comentariosGeneralesTemp" rows="4" maxlength="255" placeholder="Escribe instrucciones generales para cocina..." class="w-full bg-[#f8f9fa] border border-gray-200 rounded-[8px] p-3 text-[13px] text-[#212529] focus:outline-none focus:border-[#fd7e14] focus:bg-white resize-none"></textarea>
                </div>
                <div class="p-5 flex gap-3 bg-white border-t border-gray-100">
                    <button @click="modalComentarios = false" class="flex-1 bg-[#e9ecef] hover:bg-[#dee2e6] text-[#212529] font-bold py-2.5 rounded-[6px] text-[13px]">Cancelar</button>
                    <button @click="comentariosGenerales = comentariosGeneralesTemp; modalComentarios = false" class="flex-1 bg-[#fd7e14] hover:bg-[#e36b0c] text-white font-bold py-2.5 rounded-[6px] text-[13px] shadow-sm">Guardar Nota</button>
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

                // Modales Variables
                modalPaq1: false, paq1Opt: 'Combinado (1 Hawaiana y 1 Pepperoni)', paqObj: null,
                modalPaq2: false, paq2Tipo: 'hamb', paq2Extra: '', paq2Pizza: '',
                modalPaq3: false, paq3Pizzas: [],
                modalIngredientes: false, ingTam: null, ingSel: [],
                modalMitades: false, mitTam: null, mitSel: [],

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
                abrirOpciones(item) { this.opcItem = item; this.modalOpc = true; },
                generateUID() { return Math.random().toString(36).substr(2, 9); },

                // LIMPIADOR DE TAMAÑOS (Borra "Especial", "Mar", etc)
                cleanSize(str) {
                    if (!str) return '';
                    let s = str.toLowerCase();
                    if(s.includes('chica')) return 'Chica';
                    if(s.includes('mediana') || s.includes('media')) return 'Mediana';
                    if(s.includes('grande')) return 'Grande';
                    if(s.includes('familiar')) return 'Familiar';
                    return str; 
                },

                // LÓGICA DE PRECIOS ORILLA
                getPrecioOrilla(nombreBase) {
                    let n = nombreBase.toLowerCase();
                    if(n.includes('chica')) return 35;
                    if(n.includes('mediana') || n.includes('media')) return 40;
                    if(n.includes('grande')) return 45;
                    if(n.includes('familiar')) return 50;
                    return 35; 
                },

                // MOTOR INTELIGENTE: Agrupa y calcula 2x1 y 40% OFF matemáticamente
                actualizarCarrito() {
                    let pizzasFlat = [];
                    let normals = [];

                    // 1. Separar Pizzas (individuales) de Otros Productos (normales)
                    this.cart.forEach((cItem, index) => {
                        if (cItem.es_pizza) {
                            let baseSize = this.cleanSize(cItem.nombre_base).toUpperCase();
                            
                            cItem.subtotalBase = cItem.precioBase;
                            cItem.subtotal = cItem.precioBase;
                            cItem.descuentoPromo = 0;
                            cItem.precioFinal = cItem.precioBase;

                            if (baseSize !== '') {
                                // Clonamos las pizzas para tratarlas individualmente en el agrupador
                                for (let i = 0; i < cItem.qty; i++) {
                                    pizzasFlat.push({ cartIndex: index, size: baseSize, price: cItem.precioBase, item: cItem });
                                }
                            }
                        } else {
                            cItem.subtotalBase = cItem.precioBase * cItem.qty;
                            cItem.subtotal = cItem.subtotalBase;
                            cItem.descuentoPromo = 0;
                            cItem.precioFinal = cItem.precioBase;
                            normals.push({ cartIndex: index, item: cItem });
                        }
                    });

                    // 2. Agrupar pizzas por tamaño para hacer las parejas
                    let grouped = pizzasFlat.reduce((acc, p) => {
                        acc[p.size] = acc[p.size] || [];
                        acc[p.size].push(p);
                        return acc;
                    }, {});

                    this.cartGroups = [];

                    // 3. Crear Cajas Visuales Emparejadas (Sin letreros explícitos de Promo)
                    for (let size in grouped) {
                        let pArr = grouped[size];
                        pArr.sort((a, b) => b.price - a.price); // Ordenar para cobrar la de mayor precio siempre

                        for (let i = 0; i < pArr.length; i += 2) {
                            let p1 = pArr[i];
                            let p2 = pArr[i + 1];

                            let groupItems = [p1];
                            let subGroup = p1.price + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);
                            
                            p1.item.precioCobrado = p1.price;
                            p1.item.precioFinal = p1.item.precioCobrado + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);

                            if (p2) {
                                // ES PAR: Promo 2x1 (La segunda es gratis, solo se cobra la orilla si tiene)
                                p2.item.descuentoPromo += p2.price; 
                                p2.item.subtotal -= p2.price;
                                p2.item.precioCobrado = 0;
                                p2.item.precioFinal = 0 + (p2.item.orilla_queso ? p2.item.precio_orilla : 0);

                                subGroup += (p2.item.orilla_queso ? p2.item.precio_orilla : 0);
                                groupItems.push(p2);

                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', is2x1: true, size: this.cleanSize(size), items: groupItems, subtotal: subGroup });
                            } else {
                                // ES IMPAR: Descuento 40% a la pizza que sobró
                                let desc = p1.price * 0.40;
                                p1.item.descuentoPromo += desc;
                                p1.item.subtotal -= desc;
                                p1.item.precioCobrado = p1.price - desc;
                                p1.item.precioFinal = p1.item.precioCobrado + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);

                                subGroup -= desc;
                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', is2x1: false, size: this.cleanSize(size), items: groupItems, subtotal: subGroup });
                            }
                        }
                    }

                    // 4. Agregar tarjetas de productos normales al carrito visual
                    normals.forEach(n => {
                        this.cartGroups.push({
                            id_grupo: this.generateUID(),
                            type: 'normal', cIdx: n.cartIndex, item: n.item, subtotal: n.item.subtotal
                        });
                    });
                },

                // METODOS DE AGREGADO AL CARRITO PRINCIPAL
                addPizzaToMainCart(obj) {
                    // Cada pizza que entra al carrito lo hace de 1 en 1 para que el motor las pueda emparejar matematicamente
                    this.cart.push({ ...obj, qty: 1, uid: this.generateUID() });
                    this.actualizarCarrito();
                },

                addOpc(t) {
                    let cTam = this.cleanSize(t.tamano);
                    let nomFull = (this.cat === 12 ? 'Pizza ' : 'Mariscos ') + cTam;
                    this.addPizzaToMainCart({
                        db_id: t.id, col: (this.cat === 12 ? 'id_pizza' : 'id_maris'), tipo: 'pizza_normal', es_pizza: true,
                        nombre_base: nomFull, variante: this.opcItem.nombre, precioBase: parseFloat(t.precio),
                        orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), comentario: ''
                    });
                    this.modalOpc = false;
                },

                addDirecto(p) {
                    let idx = this.cart.findIndex(i => i.db_id === p.id && !i.es_pizza);
                    if(idx > -1) { 
                        this.cart[idx].qty++; 
                    } else { 
                        this.cart.push({ 
                            db_id: p.id, col: p.col, tipo: 'directo', nombre_base: p.nombre, variante: '', 
                            precioBase: parseFloat(p.precio), qty: 1, es_pizza: false, uid: this.generateUID(), comentario: '' 
                        }); 
                    }
                    this.actualizarCarrito();
                },

                // PAQUETES
                abrirPaquete(id) {
                    this.paqObj = dbPaquetes.find(p => p.id_paquete === id);
                    if(id === 1) { this.paq1Opt = 'Combinado (1 Hawaiana y 1 Pepperoni)'; this.modalPaq1 = true; }
                    if(id === 2) { this.paq2Tipo = 'hamb'; this.paq2Extra = ''; this.paq2Pizza = ''; this.modalPaq2 = true; }
                    if(id === 3) { this.paq3Pizzas = []; this.modalPaq3 = true; }
                },
                addPaq(id, variante) {
                    let pb = parseFloat(this.paqObj.precio);
                    let idx = this.cart.findIndex(i => i.db_id === id && i.tipo === 'paq' && i.variante === variante);
                    if(idx > -1) { this.cart[idx].qty++; }
                    else { this.cart.push({ db_id: id, col: 'id_paquete', tipo: 'paq', nombre_base: 'Paquete '+id, variante: variante, precioBase: pb, qty: 1, es_pizza: false, uid: this.generateUID(), comentario: ''}); }
                    this.actualizarCarrito();
                },
                addPaq1() { this.addPaq(1, this.paq1Opt); this.modalPaq1 = false; },
                addPaq2() { this.addPaq(2, this.paq2Extra + ' + Pizza ' + this.paq2Pizza); this.modalPaq2 = false; },
                togglePaq3(nom) { let idx = this.paq3Pizzas.indexOf(nom); if(idx > -1) this.paq3Pizzas.splice(idx, 1); else if(this.paq3Pizzas.length < 3) this.paq3Pizzas.push(nom); },
                addPaq3() { this.addPaq(3, this.paq3Pizzas.join(' / ')); this.modalPaq3 = false; },

                // MITADES E INGREDIENTES
                toggleMitad(nom) { let idx = this.mitSel.indexOf(nom); if(idx > -1) this.mitSel.splice(idx, 1); else if(this.mitSel.length < 2) this.mitSel.push(nom); },
                addMitad() {
                    let cTam = this.cleanSize(this.mitTam.tamano);
                    let nomFull = 'Mitad y Mitad ' + cTam;
                    this.addPizzaToMainCart({ db_id: null, col: 'id_pizza', tipo: 'piz_mitad', nombre_base: nomFull, variante: this.mitSel[0] + ' / ' + this.mitSel[1], precioBase: parseFloat(this.mitTam.precio), es_pizza: true, orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), comentario: '', mitad1: this.mitSel[0], mitad2: this.mitSel[1], tamano: this.mitTam.tamano });
                    this.modalMitades = false; this.mitTam = null; this.mitSel = [];
                },
                precioPizzaIngredientes() { return !this.ingTam ? 0 : parseFloat(this.ingTam.precio) + (this.ingSel.length * 15); },
                addIng() {
                    let cTam = this.cleanSize(this.ingTam.tamano);
                    let nomFull = 'Personalizada ' + cTam;
                    this.addPizzaToMainCart({ db_id: this.ingTam.id_tamañop, col: 'id_pizza', tipo: 'piz_ing', nombre_base: nomFull, variante: 'Ings: ' + this.ingSel.join(', '), precioBase: this.precioPizzaIngredientes(), es_pizza: true, orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), comentario: '', ingredientes_extra: this.ingSel });
                    this.modalIngredientes = false; this.ingTam = null; this.ingSel = [];
                },

                // EVENTOS INTERNOS (Clonar, Eliminar y Recalcular)
                recalc() { this.actualizarCarrito(); },
                toggleOrilla(uid, checked) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1) this.cart[idx].orilla_queso = checked;
                    this.actualizarCarrito();
                },
                clonePizza(item) {
                    // Boton [+] en Pizzas: Crea una clon exacto para que el sistema lo pueda emparejar si corresponde
                    let clone = JSON.parse(JSON.stringify(item));
                    clone.uid = this.generateUID();
                    clone.orilla_queso = false; // Sin orilla por defecto al clonar
                    this.cart.push(clone);
                    this.actualizarCarrito();
                },
                eliminarItemByUid(uid) {
                    // Funciona para el Basurero y el boton [-] de Pizzas
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

                getTotal() { return this.cartGroups.reduce((s, g) => s + g.subtotal, 0); },
                nomServicio() { const s = {1: 'Comer Aqui', 2: 'Para Llevar', 3: 'A Domicilio', 4: 'P. Especiales'}; return s[this.servicio]; },

                send() {
                    if(this.servicio === 1 && !this.mesa) return alert("Ingrese el número de mesa.");
                    
                    // Al enviar la orden mandamos los precios ya finales calculados
                    let cartPayload = [];
                    this.cartGroups.forEach(g => {
                        if(g.type === 'pizza_pair') {
                            g.items.forEach(p => {
                                cartPayload.push({ ...p.item, precioFinal: p.item.precioFinal, qty: 1 });
                            });
                        } else {
                            cartPayload.push({ ...g.item, precioFinal: g.item.precioBase });
                        }
                    });

                    fetch("{{ route('ventas.pos.store') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            _token: '{{ csrf_token() }}', tipo_servicio: this.servicio, mesa: this.mesa, 
                            comentarios: this.comentariosGenerales, total: this.getTotal(), carrito: cartPayload, 
                            pagos: [{id_metpago: 2, monto: this.getTotal()}] 
                        })
                    }).then(r => r.json()).then(res => {
                        if(res.success) { 
                            this.cart = []; this.actualizarCarrito(); this.mesa = ''; this.comentariosGenerales = '';
                            window.open('/venta/pos/ticket/' + res.id_venta, 'Ticket', 'width=400,height=600'); 
                        } else alert("Error al guardar: " + res.message);
                    });
                }
            }));
        });
    </script>
@endif
@endsection