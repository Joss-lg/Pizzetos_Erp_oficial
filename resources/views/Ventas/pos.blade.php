@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
</style>

@if(!$cajaAbierta)
    {{-- Reemplaza TODO lo que esté aquí adentro por esto: --}}
    <div class="w-full flex flex-col items-center justify-center min-h-[70vh]" x-data="{ modalAbrir: true }">
        <div class="bg-white rounded-[45px] shadow-2xl border border-gray-100 p-12 text-center max-w-lg">
            <div class="bg-amber-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-4xl font-black text-gray-900 mb-2 uppercase italic tracking-tighter">Turno Cerrado</h3>
            <p class="text-gray-500 mb-8 font-bold uppercase text-xs tracking-widest">Debes iniciar un nuevo turno para vender</p>
            
            <form action="{{ route('flujo.caja.abrir') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 italic text-left ml-4">Monto Inicial en Caja ($)</label>
                    <input type="number" name="monto_inicial" step="0.01" placeholder="0.00" required 
                           class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl py-4 px-6 text-2xl font-black text-center focus:border-amber-400 focus:ring-0 transition-all">
                </div>
                <button type="submit" class="w-full bg-amber-400 hover:bg-amber-500 text-black font-black py-5 rounded-2xl shadow-lg shadow-amber-100 uppercase italic tracking-tighter transition-all active:scale-95">
                    Abrir Caja y Comenzar
                </button>
            </form>
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

    <div class="w-full h-[calc(100vh-95px)] bg-[#f8f9fa] font-sans text-[#212529] flex flex-col overflow-hidden" x-data="posApp()">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 h-full min-h-0">
            
            {{-- IZQUIERDA: BOTONES Y PRODUCTOS FIJOS --}}
            <div class="lg:col-span-8 flex flex-col gap-2 h-full min-h-0">
                
                {{-- BOTONES SUPERIORES --}}
                <div class="flex flex-wrap gap-1.5 shrink-0">
                    <button @click="abrirPaquete(1)" class="bg-[#ffc107] text-[#212529] px-3 py-1.5 rounded-md text-[12px] font-bold shadow-sm hover:brightness-95 transition-colors">Paquete 1</button>
                    <button @click="abrirPaquete(2)" class="bg-[#ffc107] text-[#212529] px-3 py-1.5 rounded-md text-[12px] font-bold shadow-sm hover:brightness-95 transition-colors">Paquete 2</button>
                    <button @click="abrirPaquete(3)" class="bg-[#ffc107] text-[#212529] px-3 py-1.5 rounded-md text-[12px] font-bold shadow-sm hover:brightness-95 transition-colors">Paquete 3</button>
                    <button @click="abrirModalIngredientes()" class="bg-[#fd7e14] text-white px-3 py-1.5 rounded-md text-[12px] font-bold shadow-sm hover:brightness-95 transition-colors">Por Ingrediente</button>
                    <button @click="modalMitades = true; mitSel = []; mitTam = null;" class="bg-[#dc3545] text-white px-3 py-1.5 rounded-md text-[12px] font-bold shadow-sm hover:brightness-95 transition-colors">Mitad y Mitad</button>
                </div>

                {{-- BARRA DE CATEGORÍAS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1.5 flex flex-col xl:flex-row justify-between items-center gap-2 shrink-0">
                    <div class="flex flex-wrap gap-1 items-center w-full xl:w-auto">
                        <button @click="cat = 12; view = 'pizzas'" :class="cat === 12 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors">Pizzas</button>
                        <button @click="cat = 2; view = 'pizzas'" :class="cat === 2 ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors">Mariscos</button>
                        
                        <button @click="abrirRectangularGeneral()" :class="modalRectangular ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors">Rectangular</button>
                        <button @click="abrirBarraGeneral()" :class="modalBarra ? 'bg-[#fd7e14] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors">Barra</button>
                        
                        {{-- MENÚ EXTRAS --}}
                        <div class="relative">
                            <button @click="openExtras = !openExtras" :class="dbCategoriasExtras.map(c=>c.id_cat).includes(cat) || cat === 1 ? 'bg-[#adb5bd] text-white shadow-sm' : 'bg-[#e9ecef] text-[#495057] hover:bg-[#dee2e6]'" class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors flex items-center gap-1">
                                Extras 
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="openExtras" @click.away="openExtras = false" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1 max-h-72 overflow-y-auto">
                                
                                <template x-for="catEx in dbCategoriasExtras" :key="catEx.id_cat">
                                    <button @click="
                                        cat = parseInt(catEx.id_cat); 
                                        view = 'otros'; 
                                        openExtras = false;
                                    " class="w-full text-left px-3 py-2 text-[11px] font-bold text-[#495057] hover:bg-gray-50" x-text="catEx.descripcion"></button>
                                </template>

                                <button @click="cat = 1; view = 'bebidas'; openExtras = false;" class="w-full text-left px-3 py-2 text-[11px] font-bold text-[#495057] hover:bg-gray-50 border-t border-gray-100">Refrescos</button>
                                <button @click="abrirMagnoGeneral(); openExtras = false" class="w-full text-left px-3 py-2 text-[11px] font-bold text-[#495057] hover:bg-gray-50 border-t border-gray-100">Magno</button>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full xl:w-[180px]">
                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" x-model="search" placeholder="Buscar..." class="w-full pl-7 pr-2 py-1 border border-gray-200 rounded-md text-[11px] focus:outline-none focus:border-[#fd7e14]">
                    </div>
                </div>

                {{-- GRID PRODUCTOS --}}
                <div class="flex-1 overflow-y-auto scrollbar-hide pb-2 pt-1">
                    
                    {{-- Pizzas / Mariscos --}}
                    <div x-show="view === 'pizzas'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 content-start pr-2">
                        <template x-for="p in getListaTamanos()" :key="p.nombre">
                            <button @click="abrirOpciones(p)" class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-[5px] border-l-[#ffc107] p-4 flex flex-col justify-between items-start text-left min-h-[95px] hover:shadow-md hover:border-[#ffc107] transition-all group">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight w-full" x-text="p.nombre"></span>
                                <span class="text-[#fd7e14] text-[12px] font-black flex items-center gap-1 mt-3 group-hover:translate-x-1 transition-transform">
                                    Opciones <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                            </button>
                        </template>
                    </div>

                    {{-- Bebidas --}}
                    <div x-show="view === 'bebidas'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 content-start pr-2" x-cloak>
                        <template x-for="b in getListaBebidas()" :key="'beb_'+b.nombre">
                            <button @click="abrirBebida(b)" class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-[5px] border-l-[#17a2b8] p-4 flex flex-col justify-between items-start text-left min-h-[95px] hover:shadow-md hover:border-[#17a2b8] transition-all group">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight w-full" x-text="b.nombre"></span>
                                <span class="text-[#17a2b8] text-[12px] font-black flex items-center gap-1 mt-3 group-hover:translate-x-1 transition-transform">
                                    Elegir tamaño <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                            </button>
                        </template>
                    </div>

                    {{-- Otros Productos (Hamburguesas, etc) --}}
                    <div x-show="view === 'otros'" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 content-start mt-1 pr-2" x-cloak>
                        <template x-for="p in getListaDirectos()" :key="p.id">
                            <button @click="addDirecto(p)" class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-[5px] border-l-blue-400 p-4 flex flex-col justify-between items-start text-left min-h-[95px] hover:shadow-md hover:border-blue-400 transition-all group">
                                <span class="font-bold text-[#212529] text-[15px] leading-tight w-full" x-text="p.nombre"></span>
                                <div class="flex items-center gap-1 mt-3">
                                    <span class="text-blue-600 text-[14px] font-black group-hover:scale-110 transition-transform origin-left" x-text="'$' + parseFloat(p.precio).toFixed(2)"></span>
                                </div>
                            </button>
                        </template>
                    </div>

                </div>
            </div>

            {{-- DERECHA: CARRITO --}}
            <div class="lg:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full min-h-0">
                <div class="p-5 pb-4 border-b border-gray-100 flex justify-between items-end shrink-0">
                    <div>
                        <h2 class="text-[20px] font-black text-[#212529] leading-none" x-text="id_venta_edit ? 'Editando #' + id_venta_edit : 'Pedido Actual'"></h2>
                        <p x-show="cartGroups.length === 0" class="text-[#6c757d] text-[13px] mt-1.5">Sin productos en el carrito</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 scrollbar-hide bg-[#f8f9fa]">
                    <template x-for="(group, gIdx) in cartGroups" :key="group.id_grupo">
                        <div>
                            
                            {{-- NUEVO DISEÑO PARA PIZZAS AGRUPADAS --}}
                            <template x-if="group.type === 'pizza_pair'">
                                <div class="bg-white border-2 border-amber-400 rounded-xl shadow-sm mb-4 overflow-hidden">
                                    
                                    {{-- HEADER DEL CUADRO --}}
                                    <div class="bg-amber-400 px-4 py-2.5 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            <h3 class="font-black text-black text-[14px]" x-text="'Pizzas ' + group.size"></h3>
                                        </div>
                                        <button @click="eliminarGrupo(group)" class="text-black/60 hover:text-red-700 bg-amber-300/50 hover:bg-amber-300 p-1.5 rounded-md transition-colors" title="Borrar grupo completo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    
                                    {{-- PIZZAS INDEPENDIENTES ADENTRO --}}
                                    <div class="p-3 space-y-3">
                                        <template x-for="(p, idx) in group.items" :key="p.item.uid">
                                            <div class="relative border border-gray-200 bg-white rounded-lg p-3 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                                                
                                                <div class="flex justify-between items-start mb-3">
                                                    <div class="pr-8 flex items-center gap-2">
                                                        <h4 class="font-black text-[#212529] text-[15px] leading-tight" x-text="p.item.variante || p.item.nombre_base"></h4>
                                                        <span x-show="p.item.is_old" class="text-[10px] bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Enviado</span>
                                                    </div>

                                                    <button @click="eliminarItemByUid(p.item.uid)" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-1.5 rounded transition-colors absolute right-3 top-3">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>

                                                {{-- CHECKBOX ORILLA INDEPENDIENTE --}}
                                                <div class="flex justify-between items-center pt-2.5 border-t border-gray-100">
                                                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-600 cursor-pointer hover:text-[#fd7e14] transition-colors">
                                                        <input type="checkbox" :checked="p.item.orilla_queso" @change="toggleOrilla(p.item.uid, $event.target.checked)" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-4 h-4">
                                                        Orilla Queso <span class="text-[#fd7e14]" x-text="'+$' + p.item.precio_orilla"></span>
                                                    </label>
                                                    <span class="text-[16px] font-black text-[#212529]" x-text="'$' + p.item.precioFinal.toFixed(2)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- TOTAL DEL GRUPO --}}
                                    <div class="px-4 py-3 bg-gray-50 text-right border-t border-gray-200">
                                        <span class="text-gray-500 text-[12px] font-bold uppercase mr-2 tracking-wider">Subtotal:</span>
                                        <span class="font-black text-[#212529] text-[20px]" x-text="'$' + group.subtotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </template>

                            {{-- TARJETAS PARA OTROS PRODUCTOS NORMALES --}}
                            <template x-if="group.type === 'normal'">
                                <div class="border border-gray-200 rounded-[8px] p-4 bg-white shadow-sm mb-4 relative" :class="group.item.is_old ? 'bg-gray-50' : ''">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-[#212529] text-[14px] pr-6 leading-tight" x-text="group.item.nombre_base"></h4>
                                        <button @click="eliminarItemByUid(group.item.uid)" class="text-[#dc3545] hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded absolute right-4 top-4 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 10-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="flex items-center bg-[#e9ecef] rounded border border-gray-200">
                                            <button @click="group.item.qty > 1 ? updateNormalQty(group.item, -1) : null" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300 flex items-center justify-center">-</button>
                                            <span class="w-8 h-7 flex justify-center items-center font-bold text-[#212529] bg-white border-x border-gray-200 text-[13px]" x-text="group.item.qty"></span>
                                            <button @click="updateNormalQty(group.item, 1)" class="w-7 h-7 font-bold text-[#495057] hover:bg-gray-300 flex items-center justify-center">+</button>
                                        </div>
                                        <span class="text-[12px] text-[#6c757d] font-medium" x-text="'| Base: $' + parseFloat(group.item.precioBase).toFixed(2)"></span>
                                        <span x-show="group.item.is_old" class="text-[10px] bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Enviado</span>
                                    </div>
                                    
                                    <div x-show="group.item.variante" class="bg-[#f8f9fa] border border-gray-200 rounded-[6px] p-2 mt-2">
                                        <span class="text-[12px] text-[#495057] block font-bold whitespace-pre-wrap" x-text="group.item.variante"></span>
                                    </div>

                                    <template x-if="group.item.is_magno">
                                        <label class="flex items-center gap-2 text-[12px] text-[#495057] cursor-pointer mt-2 w-max bg-white px-2 py-1 rounded border border-gray-200 shadow-sm hover:bg-gray-50">
                                            <input type="checkbox" x-model="group.item.orilla_queso" @change="recalc()" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-3.5 h-3.5">
                                            Orilla Queso <span class="font-bold text-[#fd7e14]" x-text="'+$' + group.item.precio_orilla"></span>
                                        </label>
                                    </template>

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

                {{-- ZONA COBRO FINAL Y SELECTOR DE SERVICIO DINAMICO --}}
                <div class="p-4 border-t border-gray-200 bg-white rounded-b-xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] shrink-0">
                    <div class="flex justify-between items-center font-black text-[#212529] mb-3">
                        <span class="text-[16px]">Total:</span>
                        <span x-text="'$' + getTotal().toFixed(2)" class="text-[26px]"></span>
                    </div>

                    <button @click="modalComentarios = true" class="w-full bg-[#f8f9fa] border border-gray-200 hover:bg-[#e9ecef] text-[#212529] py-2.5 rounded-[6px] font-bold text-[14px] flex justify-center items-center gap-2 mb-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Agregar comentarios
                    </button>

                    <div class="mb-3 h-10 flex gap-2" x-show="servicio === 1" x-cloak>
                        <input type="number" x-model="mesa" placeholder="Mesa #" class="w-1/3 h-full bg-white border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] font-bold focus:outline-none focus:border-[#fd7e14] shadow-sm">
                        <input type="text" x-model="nombreClienteMesa" placeholder="Nombre cliente *" class="w-2/3 h-full bg-white border border-gray-300 rounded-[6px] py-2 px-3 text-[14px] font-bold focus:outline-none focus:border-[#fd7e14] shadow-sm">
                    </div>

                    <div class="flex h-[45px] relative">
                        
                        <button @click="openServicio = !openServicio" class="w-[45%] h-full bg-[#fd7e14] hover:bg-[#e36b0c] text-white font-bold text-[14px] flex justify-between items-center px-4 rounded-l-[6px] border-r border-[#e36b0c] transition-colors shadow-sm">
                            <div class="flex items-center gap-2">
                                <svg x-show="servicio === 3" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                <svg x-show="servicio === 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <svg x-show="servicio === 2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <span x-text="nomServicio()"></span>
                            </div>
                            <svg class="w-3.5 h-3.5 transition-transform" :class="openServicio ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="openServicio" @click.away="openServicio = false" x-cloak class="absolute bottom-full left-0 w-[240px] mb-2 bg-white border border-gray-200 rounded-lg shadow-2xl z-50 py-1">
                            <button @click="servicio = 3; openServicio = false" class="w-full text-left px-5 py-3 text-[14px] flex items-center gap-3 transition-colors border-b border-gray-100" :class="servicio === 3 ? 'text-[#fd7e14] font-black bg-orange-50' : 'text-[#495057] font-bold hover:bg-gray-50'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                A Domicilio
                            </button>
                            <button @click="servicio = 1; openServicio = false" class="w-full text-left px-5 py-3 text-[14px] flex items-center gap-3 transition-colors border-b border-gray-100" :class="servicio === 1 ? 'text-[#fd7e14] font-black bg-orange-50' : 'text-[#495057] font-bold hover:bg-gray-50'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Comer Aqui
                            </button>
                            <button @click="servicio = 2; openServicio = false" class="w-full text-left px-5 py-3 text-[14px] flex items-center gap-3 transition-colors" :class="servicio === 2 ? 'text-[#fd7e14] font-black bg-orange-50' : 'text-[#495057] font-bold hover:bg-gray-50'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Para Llevar
                            </button>
                        </div>

                        <button @click="procesarOrden()" :disabled="cart.length === 0" :class="cart.length === 0 ? 'bg-[#fd7e14]/60 text-white cursor-not-allowed' : 'bg-[#fd7e14] hover:bg-[#e36b0c] text-white'" class="flex-1 font-black text-[15px] rounded-r-[6px] transition-colors shadow-sm">
                            <span x-text="id_venta_edit ? 'Guardar Cambios' : 'Enviar Orden'"></span>
                        </button>

                    </div>
                </div>
            </div>
        </div>

        {{-- MODALES DE PRODUCTOS Y PAGOS OCULTOS --}}
        
        <div x-show="modalComentarios" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-[400px] flex flex-col overflow-hidden" @click.away="modalComentarios = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-[18px] font-bold text-[#212529]">Comentarios del Pedido</h2>
                    <button @click="modalComentarios = false" class="text-gray-400 hover:text-black font-bold text-xl">&times;</button>
                </div>
                <div class="p-5 bg-white">
                    <textarea x-model="comentariosGeneralesTemp" rows="4" placeholder="Ej. Sin cebolla, extra servilletas..." class="w-full border border-gray-300 rounded-[8px] p-3 text-[14px] focus:outline-none focus:border-[#fd7e14]"></textarea>
                </div>
                <div class="p-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button @click="modalComentarios = false" class="px-4 py-2 bg-white border border-gray-300 rounded text-gray-600 font-bold">Cancelar</button>
                    <button @click="guardarComentarios()" class="px-4 py-2 bg-[#fd7e14] text-white rounded font-bold shadow-sm">Guardar</button>
                </div>
            </div>
        </div>

        {{-- MODAL OPCIONES NORMAL --}}
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
                            <div class="mt-3 bg-gray-50 border border-gray-200 px-3 py-2 rounded font-bold text-[12px] text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Incluye 1 Refresco de 2L
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

        {{-- POR INGREDIENTES CON BUSCADOR --}}
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
                    
                    <div x-show="ingTam" class="mt-4">
                        <div class="mb-3 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" x-model="searchIng" placeholder="Buscar ingrediente..." class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-[8px] text-[13px] font-medium focus:outline-none focus:border-[#fd7e14]">
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white border border-gray-200 rounded-[8px] p-4 shadow-sm max-h-[250px] overflow-y-auto scrollbar-hide">
                            <template x-for="ing in getIngredientesFiltrados()" :key="ing.id_ingrediente">
                                <label class="flex items-center gap-2 cursor-pointer text-[13px] text-[#495057] font-medium p-1.5 hover:bg-orange-50 rounded transition-colors">
                                    <input type="checkbox" :value="ing.ingrediente" x-model="ingSel" class="rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14] w-4 h-4">
                                    <span x-text="ing.ingrediente"></span>
                                </label>
                            </template>
                            <template x-if="getIngredientesFiltrados().length === 0">
                                <div class="col-span-2 sm:col-span-3 text-center py-4 text-gray-400 text-xs font-bold">No se encontraron ingredientes.</div>
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
            <div class="bg-white rounded-xl shadow-2xl w-[900px] max-w-[95vw] flex flex-col max-h-[95vh] overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                    <h2 class="text-3xl font-black text-gray-800 flex items-center gap-3">
                        <svg class="w-8 h-8 text-[#fd7e14]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Dirección de Entrega
                    </h2>
                    <button @click="modalCliente = false" class="text-gray-400 hover:text-black font-bold text-4xl leading-none">&times;</button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-8 lg:p-10 bg-white space-y-8">
                    
                    <div class="relative">
                        
                        <div class="flex justify-between items-end mb-3">
                            <label class="block text-[18px] font-bold text-gray-700">Seleccionar Cliente *</label>
                            <button x-show="!clienteFormVisible && !clienteSeleccionado" @click="toggleFormNuevoCliente()" class="bg-[#ffc107] hover:bg-[#e0a800] text-black font-black px-6 py-2.5 rounded-[8px] text-[16px] flex items-center gap-2 shadow-sm whitespace-nowrap transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path></svg>
                                Nuevo Cliente
                            </button>
                        </div>

                        {{-- VISTA DE BÚSQUEDA Y LISTA --}}
                        <div x-show="!clienteSeleccionado && !clienteFormVisible" class="space-y-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" 
                                       x-model="searchClienteText" 
                                       placeholder="Escribe el nombre o teléfono del cliente..." 
                                       autocomplete="off"
                                       class="w-full border-2 border-gray-300 rounded-[8px] py-4 pl-12 pr-5 text-[20px] font-medium focus:border-[#fd7e14] focus:ring-4 focus:ring-orange-100 focus:outline-none transition-all">
                            </div>
                            
                            {{-- LISTA DE CLIENTES PERMANENTE --}}
                            <div class="bg-white border border-gray-200 rounded-[8px] shadow-inner max-h-[300px] overflow-y-auto">
                                <template x-if="getClientesFiltrados().length === 0">
                                    <div class="px-5 py-8 text-[16px] text-gray-500 italic text-center bg-gray-50">
                                        No se encontraron clientes en la base de datos.<br><span class="font-bold">Haz clic en "Nuevo Cliente" para registrarlo.</span>
                                    </div>
                                </template>

                                <template x-for="cl in getClientesFiltrados()" :key="cl.id_cliente || cl.id_clie || Math.random()">
                                    <div @click="seleccionarCliente(cl)" class="px-6 py-4 hover:bg-orange-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center transition-colors">
                                        <span class="font-bold text-[18px] text-gray-800" x-text="getClienteNombre(cl)"></span>
                                        <span class="text-[15px] text-gray-600 font-bold bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-md shadow-sm" x-text="getClienteTelefono(cl)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- ESTADO CLIENTE SELECCIONADO --}}
                        <div x-show="clienteSeleccionado && !clienteFormVisible" class="bg-green-50 border border-green-200 p-6 rounded-[8px] flex justify-between items-center transition-all">
                            <div>
                                <span class="text-[13px] font-bold text-green-600 uppercase tracking-wider mb-1 block">Cliente Seleccionado</span>
                                <span class="font-black text-[24px] text-green-800 block leading-none" x-text="getClienteNombre(clienteSeleccionado)"></span>
                                <span class="text-[18px] text-green-700 font-bold block mt-2" x-text="'Tel: ' + getClienteTelefono(clienteSeleccionado)"></span>
                            </div>
                            <button @click="clienteSeleccionado = null; direccionesCliente = []; dirSeleccionada = null; searchClienteText='';" class="text-red-600 bg-red-100 px-6 py-3 rounded-lg font-bold hover:bg-red-200 transition-colors text-[16px]">Cambiar Cliente</button>
                        </div>

                        {{-- FORMULARIO NUEVO CLIENTE --}}
                        <div x-show="clienteFormVisible" class="bg-gray-50 p-6 rounded-[8px] border border-gray-200 space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                <h4 class="font-black text-[18px] text-gray-800">Registrar Nuevo Cliente</h4>
                                <button @click="toggleFormNuevoCliente()" class="text-gray-500 hover:text-red-500 font-bold text-[15px] bg-white border border-gray-300 px-3 py-1.5 rounded">Cancelar</button>
                            </div>
                            <div>
                                <label class="block text-[15px] font-bold text-gray-600 mb-2">Nombre *</label>
                                <input type="text" x-model="nuevoClienteData.nombre" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[15px] font-bold text-gray-600 mb-2">Teléfono</label>
                                <input type="text" x-model="nuevoClienteData.telefono" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none">
                            </div>
                        </div>

                    </div>

                    <div x-show="clienteSeleccionado || clienteFormVisible" class="pt-6 border-t border-gray-200 transition-all">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-[16px] font-bold text-gray-700">Dirección de Entrega *</label>
                            <button @click="dirFormVisible = !dirFormVisible; dirSeleccionada = null" class="bg-[#ffc107] hover:bg-[#e0a800] text-black font-bold px-5 py-2.5 rounded-[6px] text-[15px] flex items-center gap-1 shadow-sm transition-colors">
                                + Nueva Dirección
                            </button>
                        </div>

                        <div x-show="!dirFormVisible && direccionesCliente.length > 0" class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            <template x-for="dir in direccionesCliente" :key="dir.id_direccion || dir.id_dir || Math.random()">
                                <label class="block cursor-pointer">
                                    <div class="border rounded-[8px] p-5 transition-colors" :class="dirSeleccionada === (dir.id_direccion || dir.id_dir) ? 'border-2 border-[#fd7e14] bg-orange-50 shadow-md' : 'border-gray-200 hover:bg-gray-50'">
                                        <div class="flex items-start gap-4">
                                            <input type="radio" :value="dir.id_direccion || dir.id_dir" x-model="dirSeleccionada" class="mt-1 text-[#fd7e14] focus:ring-[#fd7e14] w-6 h-6">
                                            <div class="text-[15px] text-gray-700 leading-snug">
                                                <span class="font-black block text-[18px] text-black mb-1.5" x-text="dir.calle || dir.Calle || 'Sin calle'"></span>
                                                <span class="block text-gray-600 mb-1" x-text="'Manzana: ' + (dir.manzana||dir.Manzana||'-') + ' | Lote: ' + (dir.lote||dir.Lote||'-')"></span>
                                                <span class="block text-gray-600 font-medium" x-text="'Colonia: ' + (dir.colonia||dir.Colonia||'-')"></span>
                                                <span class="block text-gray-500 italic mt-2 bg-white px-3 py-1.5 border border-gray-200 rounded" x-show="dir.referencia || dir.Referencia" x-text="'Referencia: ' + (dir.referencia || dir.Referencia)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                        <div x-show="!dirFormVisible && direccionesCliente.length === 0 && !clienteFormVisible" class="text-center p-8 border-2 border-dashed border-gray-300 rounded-[8px] text-[16px] text-gray-500 font-medium bg-gray-50">
                            Este cliente no tiene direcciones guardadas.<br>Haz clic en "+ Nueva Dirección".
                        </div>

                        <div x-show="dirFormVisible" class="bg-gray-50 p-6 rounded-[8px] border border-gray-200 space-y-4 mt-3">
                            <h4 class="font-black text-[18px] text-gray-800 border-b border-gray-200 pb-3">Registrar Nueva Dirección</h4>
                            <div>
                                <label class="block text-[15px] font-bold text-gray-600 mb-2">Calle *</label>
                                <input type="text" x-model="nuevaDirData.calle" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none">
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-[15px] font-bold text-gray-600 mb-2">Manzana</label>
                                    <input type="text" x-model="nuevaDirData.manzana" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[15px] font-bold text-gray-600 mb-2">Lote</label>
                                    <input type="text" x-model="nuevaDirData.lote" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[15px] font-bold text-gray-600 mb-2">Colonia</label>
                                <input type="text" x-model="nuevaDirData.colonia" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[15px] font-bold text-gray-600 mb-2">Referencia</label>
                                <textarea x-model="nuevaDirData.referencia" rows="2" class="w-full border border-gray-300 rounded-[8px] py-3 px-4 text-[16px] focus:border-[#fd7e14] focus:outline-none resize-none"></textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="p-6 flex gap-4 bg-gray-50 border-t border-gray-200 shrink-0">
                    <button @click="modalCliente = false" class="flex-1 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-4 rounded-[8px] text-[18px] transition-colors">Cancelar</button>
                    <button @click="confirmarDomicilio()" :disabled="!esDomicilioValido()" :class="!esDomicilioValido() ? 'bg-[#ced4da] text-white cursor-not-allowed' : 'bg-[#fd7e14] hover:bg-[#e36b0c] text-white shadow-md'" class="flex-1 font-black py-4 rounded-[8px] text-[18px] transition-colors">Confirmar Dirección</button>
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
                    
                    <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.transferencia.activo ? 'border-[#fd7e14]' : 'border-gray-200'">
                        <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                            <input type="checkbox" x-model="pagos.transferencia.activo" @change="autoFillPago('transferencia')" class="w-5 h-5 rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14]">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            <span class="font-bold text-[15px] text-[#212529]">Transferencia</span>
                        </label>
                        <div x-show="pagos.transferencia.activo" class="px-4 pb-4 pt-1 space-y-3">
                            <div>
                                <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Transferencia</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                    <input type="number" step="0.01" min="0" x-model.number="pagos.transferencia.monto" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded text-[14px] font-bold focus:outline-none focus:border-[#fd7e14]">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[12px] text-blue-600 font-bold mb-1">Número de Referencia *</label>
                                <input type="text" x-model="pagos.transferencia.referencia" placeholder="Ref de transferencia" class="w-full px-3 py-2 border border-blue-200 bg-blue-50 rounded text-[14px] focus:outline-none focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.tarjeta.activo ? 'border-[#fd7e14]' : 'border-gray-200'">
                        <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                            <input type="checkbox" x-model="pagos.tarjeta.activo" @change="autoFillPago('tarjeta')" class="w-5 h-5 rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14]">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span class="font-bold text-[15px] text-[#212529]">Tarjeta</span>
                        </label>
                        <div x-show="pagos.tarjeta.activo" class="px-4 pb-4 pt-1">
                            <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Tarjeta</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                <input type="number" step="0.01" min="0" x-model.number="pagos.tarjeta.monto" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded text-[14px] font-bold focus:outline-none focus:border-[#fd7e14]">
                            </div>
                            <p class="text-[11px] text-gray-400 italic mt-2">Se enviará terminal.</p>
                        </div>
                    </div>

                    <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.efectivo.activo ? 'border-[#fd7e14]' : 'border-gray-200'">
                        <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                            <input type="checkbox" x-model="pagos.efectivo.activo" @change="autoFillPago('efectivo')" class="w-5 h-5 rounded border-gray-300 text-[#fd7e14] focus:ring-[#fd7e14]">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-bold text-[15px] text-[#212529]">Efectivo</span>
                        </label>
                        <div x-show="pagos.efectivo.activo" class="px-4 pb-4 pt-1 space-y-3">
                            <div>
                                <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Efectivo</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                    <input type="number" step="0.01" min="0" x-model.number="pagos.efectivo.monto" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded text-[14px] font-bold focus:outline-none focus:border-[#fd7e14]">
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
                    <button @click="procesarOrdenFinal(false)" :disabled="!pagosValidos() || isProcessing" :class="(!pagosValidos() || isProcessing) ? 'bg-[#1a202c]/50 text-white cursor-not-allowed' : 'bg-[#1a202c] hover:bg-black text-white shadow-md'" class="w-full font-bold py-3.5 rounded-[8px] text-[15px] transition-colors flex justify-center items-center gap-2">
                        <span x-show="!isProcessing">Confirmar Pagos</span>
                        <span x-show="isProcessing">Procesando...</span>
                        <span x-show="!isProcessing && getTotalPagarInputs() > 0" class="bg-white/20 px-2 py-0.5 rounded text-[13px]" x-text="'$' + getTotalPagarInputs().toFixed(2)"></span>
                    </button>
                    <p x-show="!pagosValidos()" class="text-[11px] text-red-500 text-center font-medium">Asegúrate de que la suma coincida con el total y rellenar referencias.</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                openServicio: false,
                openExtras: false,
                cat: 12, view: 'pizzas', search: '', cart: {!! json_encode($cart_preloaded ?? []) !!}, cartGroups: [], 
                servicio: {{ $venta_edit->tipo_servicio ?? 3 }}, 
                mesa: '{{ $venta_edit->mesa ?? '' }}', 
                nombreClienteMesa: '{{ $venta_edit->nombreClie ?? '' }}',
                id_venta_edit: {{ $venta_edit->id_venta ?? 'null' }},
                comentariosGenerales: '{{ $venta_edit->comentarios ?? '' }}', comentariosGeneralesTemp: '', modalComentarios: false,
                modalOpc: false, opcItem: null,

                modalPaq1: false, paq1Opt: 'Combinado (1 Hawaiana y 1 Pepperoni)', paqObj: null,
                modalPaq2: false, paq2Tipo: 'hamb', paq2Extra: '', paq2Pizza: '',
                modalPaq3: false, paq3Pizzas: [],
                modalIngredientes: false, ingTam: null, ingSel: [], searchIng: '',
                modalMitades: false, mitTam: null, mitSel: [],
                modalRectangular: false, rectItem: null, rectSel: [],
                modalBarra: false, barraItem: null, barraSel: [],
                modalBebida: false, bebidaItem: null,
                modalMagno: false, magnoItem: null, magnoSel: [],

                modalCliente: false, 
                modalPago: false,
                isProcessing: false,
                searchClienteText: '', showClientesList: false,
                clienteSeleccionado: null, direccionesCliente: [], dirSeleccionada: null,
                clienteFormVisible: false, dirFormVisible: false,
                nuevoClienteData: { nombre: '', telefono: '' },
                nuevaDirData: { calle: '', manzana: '', lote: '', colonia: '', referencia: '' },

                pagos: {
                    efectivo: { activo: false, monto: null, entregado: null },
                    tarjeta: { activo: false, monto: null },
                    transferencia: { activo: false, monto: null, referencia: '' }
                },

                init() {
                    if(this.cart && this.cart.length > 0) {
                        this.actualizarCarrito();
                    }
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
                
                getClientesFiltrados() {
                    let listaSegura = Array.isArray(dbClientes) ? dbClientes : [];
                    if(!this.searchClienteText || this.searchClienteText.trim() === '') return listaSegura; 
                    
                    let txt = this.searchClienteText.toLowerCase().trim();
                    return listaSegura.filter(c => {
                        let nom = (c.nombre || '').toLowerCase();
                        let ape = (c.apellido || '').toLowerCase();
                        let tel = (c.telefono || '').toLowerCase();
                        let full = nom + ' ' + ape;
                        return full.includes(txt) || tel.includes(txt);
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
                        orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam)
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
                    let precioMagno = dbMagnoPrice && dbMagnoPrice > 150 ? parseFloat(dbMagnoPrice) : 230.00; 
                    this.magnoItem = { id: null, col: 'id_pizza', nombre: 'Magno', precio: precioMagno };
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
                    let varianteFinal = this.formatearMagnoPreview();
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
                            orillas_qty: 0, max_orillas: maxPizzas, precio_orilla: dbPreciosOrilla.grande 
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
                
                getIngredientesFiltrados() {
                    if(!this.searchIng || this.searchIng.trim() === '') return dbIngredientes;
                    let s = this.searchIng.toLowerCase().trim();
                    return dbIngredientes.filter(i => (i.ingrediente || '').toLowerCase().includes(s));
                },
                abrirModalIngredientes() {
                    this.ingTam = null;
                    this.ingSel = [];
                    this.searchIng = '';
                    this.modalIngredientes = true;
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
                
                eliminarItemByUid(uid) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1) this.cart.splice(idx, 1);
                    this.actualizarCarrito();
                },

                eliminarGrupo(group) {
                    let uidsToRemove = group.items.map(p => p.item.uid);
                    this.cart = this.cart.filter(cItem => !uidsToRemove.includes(cItem.uid));
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
                
                nomServicio() { 
                    if(this.servicio === 1) return 'Comer Aqui';
                    if(this.servicio === 2) return 'Para Llevar';
                    if(this.servicio === 3) return 'A Domicilio';
                    return 'Seleccionar'; 
                },

                abrirModalCliente() {
                    this.clienteSeleccionado = null;
                    this.searchClienteText = '';
                    this.direccionesCliente = [];
                    this.dirSeleccionada = null;
                    this.clienteFormVisible = false;
                    this.dirFormVisible = false;
                    this.nuevoClienteData = { nombre: '', telefono: '' };
                    this.nuevaDirData = { calle: '', manzana: '', lote: '', colonia: '', referencia: '' };
                    this.modalCliente = true;
                },

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

                abrirModalPago() {
                    this.pagos = {
                        efectivo: { activo: false, monto: null, entregado: null },
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
                    if(!this.pagos.efectivo.activo && !this.pagos.tarjeta.activo && !this.pagos.transferencia.activo) return false;
                    if(this.pagos.transferencia.activo && (!this.pagos.transferencia.referencia || this.pagos.transferencia.referencia.trim() === '')) return false;
                    return true;
                },

                procesarOrden() {
                    if(this.servicio === 1) {
                        if(!this.mesa || !this.nombreClienteMesa.trim()) return alert("El número de mesa y el nombre del cliente son obligatorios.");
                        this.procesarOrdenFinal(true); 
                    } else if(this.servicio === 2) {
                        this.abrirModalPago(); 
                    } else if(this.servicio === 3) {
                        this.abrirModalCliente(); 
                    }
                },

                procesarOrdenFinal(esAbierta = false) {
                if(!esAbierta && !this.pagosValidos()) return;
                this.isProcessing = true;

                let cartPayload = [];
                this.cartGroups.forEach(g => {
                    if(g.type === 'pizza_pair') {
                        g.items.forEach(p => { cartPayload.push({ ...p.item, precioFinal: p.item.precioFinal, qty: 1 }); });
                    } else {
                        cartPayload.push({ ...g.item, precioFinal: g.item.precioFinal });
                    }
                });

                let pagosToSend = [];
                if(!esAbierta) {
                    if(this.pagos.efectivo.activo && this.pagos.efectivo.monto > 0) {
                        pagosToSend.push({ id_metpago: 2, monto: this.pagos.efectivo.monto, entregado: this.pagos.efectivo.entregado || this.pagos.efectivo.monto });
                    }
                    if(this.pagos.tarjeta.activo && this.pagos.tarjeta.monto > 0) {
                        pagosToSend.push({ id_metpago: 1, monto: this.pagos.tarjeta.monto }); 
                    }
                    if(this.pagos.transferencia.activo && this.pagos.transferencia.monto > 0) {
                        pagosToSend.push({ id_metpago: 3, monto: this.pagos.transferencia.monto, referencia: this.pagos.transferencia.referencia });
                    }
                }

                let reqBody = {
                    _token: '{{ csrf_token() }}', 
                    tipo_servicio: this.servicio, 
                    mesa: this.mesa, 
                    nombre_cliente: this.nombreClienteMesa,
                    comentarios: this.comentariosGenerales, 
                    total: this.getTotal(), 
                    carrito: cartPayload, 
                    pagos: pagosToSend,
                    id_venta_edit: this.id_venta_edit
                };

                if(this.servicio === 3) {
                    if(this.clienteFormVisible) reqBody.nuevo_cliente = this.nuevoClienteData;
                    else reqBody.id_clie = this.clienteSeleccionado.id_cliente || this.clienteSeleccionado.id_clie;

                    if(this.dirFormVisible) reqBody.nueva_direccion = this.nuevaDirData;
                    else reqBody.id_dir = this.dirSeleccionada;
                }

                fetch("{{ route('ventas.pos.store') }}", {
                    method: 'POST', 
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify(reqBody)
                }).then(async r => {
                    if(!r.ok) { throw new Error("Error del servidor: " + r.status); }
                    return r.json();
                }).then(res => {
                    if(res.success) { 
                        // 1. Limpiar variables locales
                        this.cart = []; 
                        this.actualizarCarrito(); 
                        this.mesa = ''; 
                        this.nombreClienteMesa = ''; 
                        this.comentariosGenerales = '';
                        this.modalPago = false;
                        
                        this.clienteSeleccionado = null;
                        this.searchClienteText = '';
                        this.direccionesCliente = [];
                        this.dirSeleccionada = null;
                        this.clienteFormVisible = false;
                        this.dirFormVisible = false;

                        // 2. CONFIGURACIÓN DEL POPUP PARA EL TICKET
                        let urlTicket = '/venta/pos/ticket/' + res.id_venta;
                        if (this.id_venta_edit) { urlTicket += '?solo_nuevos=1'; }

                        const width = 420;
                        const height = 700;
                        const left = (window.screen.width / 2) - (width / 2);
                        const top = (window.screen.height / 2) - (height / 2);

                        // ABRIR VENTANA PEQUEÑA
                        window.open(urlTicket, 'TicketPizzetos', `width=${width},height=${height},left=${left},top=${top},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`); 
                        
                        // 3. Redirección o reset de estado
                        if(this.id_venta_edit) {
                            setTimeout(() => { window.location.href = "{{ route('ventas.resume') }}"; }, 1500);
                        } else {
                            this.isProcessing = false;
                        }
                    } else {
                        alert("Error al guardar: " + res.message);
                        this.isProcessing = false;
                    }
                }).catch(e => {
                    alert("Ocurrió un error. Intenta de nuevo.\n" + e.message);
                    this.isProcessing = false;
                });
            }
            }));
        });
    </script>
@endif
@endsection