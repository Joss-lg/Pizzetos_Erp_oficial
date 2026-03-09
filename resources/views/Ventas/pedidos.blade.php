@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .vertical-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        text-orientation: mixed;
    }
    [x-cloak] { display: none !important; }
</style>

<div class="w-full min-h-[85vh] bg-[#fffbf2] p-6 font-sans rounded-xl border border-amber-100" x-data="cocinaApp()">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Monitor de Cocina</h2>
            <p class="text-gray-500 font-medium mt-1">Pedidos en curso y pendientes</p>
        </div>
        <div class="flex gap-4">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                <span class="font-bold text-gray-600 text-sm">Esperando (<span x-text="pedidos.filter(p => p.status == 0).length"></span>)</span>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                <span class="font-bold text-gray-600 text-sm">Preparando (<span x-text="pedidos.filter(p => p.status == 1).length"></span>)</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <template x-for="p in pedidos" :key="p.id">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 flex overflow-hidden min-h-[340px] transition-transform hover:-translate-y-1">
                
                <div class="w-10 flex flex-col items-center py-5 shrink-0"
                     :class="p.status == 0 ? 'bg-gray-100 border-r border-gray-200' : 'bg-[#fff9c4] border-r border-amber-200'">
                    <div class="w-3.5 h-3.5 rounded-full mb-6" :class="p.status == 0 ? 'bg-gray-400' : 'bg-amber-500'"></div>
                    <span class="vertical-text font-black tracking-[0.2em] uppercase text-[12px]" 
                          :class="p.status == 0 ? 'text-gray-500' : 'text-amber-700'"
                          x-text="p.status == 0 ? 'Esperando' : 'Preparando'">
                    </span>
                </div>

                <div class="flex-1 flex flex-col p-5 bg-white relative">
                    
                    <div class="mb-4">
                        <h3 class="font-black text-[18px] text-gray-900 leading-tight mb-2" x-text="'Pedido #' + p.id + ' - ' + p.cliente"></h3>
                        
                        <div class="flex items-center gap-2 font-bold text-[14px] mb-1" 
                             :class="p.status == 0 ? 'text-gray-400' : (p.minutos >= 30 ? 'text-red-600' : (p.minutos >= 15 ? 'text-amber-600' : 'text-green-600'))">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-show="p.status == 0">En espera</span>
                            <span x-show="p.status == 1">Tiempo: <span x-text="p.minutos"></span> min</span>
                        </div>
                        
                        <div class="text-[13px] text-gray-500 font-medium">Sucursal: <span class="text-gray-800">Chalco</span></div>
                        
                        <div class="mt-2">
                            <span class="px-2 py-1 rounded text-[11px] font-bold"
                                  :class="p.status == 0 ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-800'"
                                  x-text="p.status == 0 ? 'Esperando' : 'Preparando'"></span>
                        </div>
                    </div>

                    <hr class="border-gray-100 mb-4">

                    <div class="flex-1">
                        <p class="text-[13px] font-bold text-gray-600 mb-2" x-text="'Productos (' + p.total_items + ' items):'"></p>
                        
                        <div class="space-y-3 max-h-[140px] overflow-y-auto scrollbar-hide">
                            <template x-for="(item, index) in p.items" :key="index">
                                <div x-show="p.expanded || index < 2" class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                    <div class="font-bold text-[13px] text-gray-800 leading-snug">
                                        <span class="text-amber-600 mr-1" x-text="item.cantidad + 'x'"></span> 
                                        <span x-text="item.nombre"></span>
                                    </div>
                                    <template x-if="item.sub.length > 0">
                                        <ul class="mt-1 space-y-0.5">
                                            <template x-for="(sub, sIdx) in item.sub" :key="sIdx">
                                                <li class="text-[11px] text-gray-500 font-medium ml-4 list-disc" x-text="sub"></li>
                                            </template>
                                        </ul>
                                    </template>
                                </div>
                            </template>
                        </div>
                        
                        <template x-if="p.items.length > 2">
                            <button @click="p.expanded = !p.expanded" class="text-blue-600 font-bold text-[12px] mt-3 hover:underline outline-none">
                                <span x-text="p.expanded ? 'Mostrar menos' : 'Mostrar más'"></span>
                            </button>
                        </template>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between gap-2">
                        
                        <button @click="actualizarStatus(p.id, 1)" 
                                :disabled="p.status == 1"
                                :class="p.status == 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-50 text-gray-700'"
                                class="flex flex-col items-center justify-center flex-1 py-2 rounded-lg transition-colors group">
                            <svg class="w-6 h-6 mb-1 text-gray-600 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m-4-3v3m8-3v3M4 10h16v6a4 4 0 01-4 4H8a4 4 0 01-4-4v-6z"></path></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider group-hover:text-amber-600">Preparar</span>
                        </button>
                        
                        <a :href="'/venta/pos/ticket/' + p.id" target="_blank" class="flex flex-col items-center justify-center flex-1 py-2 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors group">
                            <svg class="w-6 h-6 mb-1 text-gray-600 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider group-hover:text-blue-600">Detalle</span>
                        </a>

                        <button @click="actualizarStatus(p.id, 2)" 
                                :disabled="p.status == 0"
                                :class="p.status == 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-green-50 text-gray-700'"
                                class="flex flex-col items-center justify-center flex-1 py-2 rounded-lg transition-colors group">
                            <svg class="w-6 h-6 mb-1 text-gray-600 group-hover:text-green-500 transition-colors" fill="currentColor" viewBox="0 0 512 512"><path d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480V396.4c0-4 1.5-7.8 4.2-10.7L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider group-hover:text-green-600">Completar</span>
                        </button>

                    </div>

                </div>
            </div>
        </template>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cocinaApp', () => ({
                pedidosRaw: {!! json_encode($ventas ?? []) !!},
                pedidos: [],

                init() {
                    // Mapear los datos de PHP al modelo visual
                    this.pedidos = this.pedidosRaw.map(p => ({
                        id: p.id_venta,
                        cliente: p.cliente_display,
                        status: p.status, 
                        minutos: p.status == 1 ? p.minutos : 0, // Si está esperando (0), el cronómetro no arranca aún.
                        total_items: p.total_items,
                        items: p.items,
                        expanded: false 
                    }));

                    // Iniciar el reloj para que sume 1 minuto cada 60 segundos
                    // SOLO a los pedidos que estén en status 1 (Preparando)
                    setInterval(() => {
                        this.pedidos.forEach(p => {
                            if (p.status == 1) {
                                p.minutos++;
                            }
                        });
                    }, 60000); // 60,000 ms = 1 minuto
                },

                actualizarStatus(id, nuevoStatus) {
                    fetch(`/venta/pedidos/${id}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: nuevoStatus })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            if (nuevoStatus == 2) {
                                // Si ya se completó, desaparece de la vista de cocina
                                this.pedidos = this.pedidos.filter(p => p.id !== id);
                            } else if (nuevoStatus == 1) {
                                // Si pasó a preparando, actualizamos UI y arrancamos el cronómetro visual en 0
                                let idx = this.pedidos.findIndex(p => p.id === id);
                                if (idx > -1) {
                                    this.pedidos[idx].status = 1;
                                    this.pedidos[idx].minutos = 0; 
                                }
                            }
                        } else {
                            alert('Error al actualizar el estado: ' + res.message);
                        }
                    })
                    .catch(err => alert('Error de conexión.'));
                }
            }));
        });
    </script>
</div>
@endsection