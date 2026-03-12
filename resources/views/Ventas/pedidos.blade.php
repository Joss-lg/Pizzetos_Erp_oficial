@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <span class="font-medium text-[15px]">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-6 right-6 z-50 bg-red-600 text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <span class="font-medium text-[15px]">{{ session('error') }}</span>
</div>
@endif

<div class="w-full h-auto min-h-[80vh]" x-data="monitorEnvios()">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-black text-[#1e293b] tracking-tight">Monitor Operativo</h2>
            <p class="text-sm text-gray-500 mt-1">Control de pedidos locales y envíos a domicilio</p>
        </div>
        <div class="text-right">
            <span class="block text-2xl font-black text-[#fd7e14]" x-text="currentTime"></span>
            <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">Hora Local</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <div class="bg-gray-100 rounded-2xl p-5 border border-gray-200 shadow-inner">
            <h3 class="font-black text-[18px] text-gray-700 mb-4 flex items-center gap-2 border-b border-gray-200 pb-3">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                En Proceso / Por Enviar (<span x-text="pedidosPorEnviar.length"></span>)
            </h3>
            
            <div class="space-y-4">
                <template x-if="pedidosPorEnviar.length === 0">
                    <div class="text-center py-10 text-gray-400 font-medium">No hay pedidos pendientes.</div>
                </template>

                <template x-for="p in pedidosPorEnviar" :key="p.id_venta">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow relative overflow-hidden"
                         :class="p.tipo_servicio == 3 ? 'border-l-4 border-l-[#fd7e14]' : (p.tipo_servicio == 1 ? 'border-l-4 border-l-blue-500' : 'border-l-4 border-l-purple-500')">
                        
                        <div class="absolute top-0 right-0 bg-gray-100 px-3 py-1 rounded-bl-lg font-black text-[13px]" :class="tiempoTranscurrido(p.fecha_hora) > 30 ? 'text-red-600 bg-red-50' : 'text-gray-700'">
                            Espera: <span x-text="tiempoTranscurrido(p.fecha_hora)"></span> min
                        </div>
                        
                        <h4 class="font-black text-[18px] text-gray-900 mb-1 mt-1">Ticket #<span x-text="p.id_venta"></span></h4>
                        <p class="font-bold text-[15px] mb-3 leading-none" 
                           :class="p.tipo_servicio == 3 ? 'text-[#fd7e14]' : (p.tipo_servicio == 1 ? 'text-blue-600' : 'text-purple-600')" 
                           x-text="formatearNombre(p)">
                        </p>

                        <div class="text-[13px] text-gray-600 space-y-1 mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="font-medium" x-html="formatearDetalle(p)"></span>
                            </p>
                            <template x-if="p.tipo_servicio == 3">
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span class="font-bold text-gray-700" x-text="p.telefono"></span>
                                </p>
                            </template>
                            <p class="flex items-center gap-2 mt-1 border-t border-gray-200 pt-1">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-black text-green-600 text-[14px]">Total: $<span x-text="parseFloat(p.total).toFixed(2)"></span></span>
                            </p>
                        </div>

                        <template x-if="p.tipo_servicio == 3">
                            <form :action="`/venta/pedidos/${p.id_venta}/status`" method="POST" class="flex gap-2">
                                @csrf @method('PUT')
                                <input type="hidden" name="accion" value="en_camino">
                                <input type="text" name="repartidor" required placeholder="Nombre del repartidor..." class="flex-1 border border-gray-300 rounded text-sm font-bold px-3 py-2.5 focus:ring-[#fd7e14] focus:border-[#fd7e14] outline-none bg-white shadow-inner">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded text-[13px] shadow transition-colors flex items-center gap-1 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg> Enviar
                                </button>
                            </form>
                        </template>

                        <template x-if="p.tipo_servicio != 3">
                            <form :action="`/venta/pedidos/${p.id_venta}/status`" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="accion" value="entregado">
                                <button type="submit" class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-black py-2.5 rounded-lg text-[14px] shadow transition-colors flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span x-text="p.tipo_servicio == 1 ? 'Marcar Servido en Mesa' : 'Entregar (Para Llevar)'"></span>
                                </button>
                            </form>
                        </template>

                    </div>
                </template>
            </div>
        </div>

        <div class="bg-orange-50 rounded-2xl p-5 border border-orange-200 shadow-inner">
            <h3 class="font-black text-[18px] text-orange-800 mb-4 flex items-center gap-2 border-b border-orange-200 pb-3">
                <svg class="w-6 h-6 text-[#fd7e14]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                En Camino (<span x-text="pedidosEnCamino.length"></span>)
            </h3>
            
            <div class="space-y-4">
                <template x-if="pedidosEnCamino.length === 0">
                    <div class="text-center py-10 text-orange-400 font-medium">Ningún pedido está en ruta actualmente.</div>
                </template>

                <template x-for="p in pedidosEnCamino" :key="p.id_venta">
                    <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-4 border-l-4 border-l-[#fd7e14] hover:shadow-md transition-shadow relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-orange-100 text-orange-800 px-3 py-1 rounded-bl-lg font-black text-[13px] flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="tiempoTranscurrido(p.fecha_hora)"></span> min totales
                        </div>
                        
                        <h4 class="font-black text-[18px] text-gray-900 mb-1 mt-1">Ticket #<span x-text="p.id_venta"></span></h4>
                        <p class="font-bold text-gray-700 text-[15px] mb-3 leading-none" x-text="formatearNombre(p)"></p>

                        <div class="bg-orange-50 border border-orange-100 rounded-lg p-3 mb-4 text-sm text-gray-700">
                            <p class="font-black text-[#fd7e14] text-[15px] flex items-center gap-2 mb-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span x-text="'Repartidor: ' + p.repartidorAsignado"></span>
                            </p>
                            <p class="font-medium text-gray-500 text-xs mb-2 italic" x-text="'Se fue a las ' + p.horaSalida"></p>
                            
                            <p class="flex items-start gap-1.5 text-xs text-gray-600 border-t border-orange-100 pt-2">
                                <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                <span x-html="formatearDetalle(p)"></span>
                            </p>
                        </div>

                        <form :action="`/venta/pedidos/${p.id_venta}/status`" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="accion" value="entregado">
                            <button type="submit" class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-black py-2.5 rounded-lg text-[14px] shadow transition-colors flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Marcar como Entregado
                            </button>
                        </form>

                    </div>
                </template>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('monitorEnvios', () => ({
            rawPedidos: {!! json_encode($pedidos ?? []) !!},
            pedidosPorEnviar: [],
            pedidosEnCamino: [],
            currentTime: '',
            now: new Date(),

            init() {
                this.clasificarPedidos();
                this.updateTime();
                
                // Actualiza la hora local y los cronómetros cada 10 segundos
                setInterval(() => {
                    this.now = new Date();
                    this.updateTime();
                }, 10000); 
            },

            updateTime() {
                this.currentTime = this.now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', hour12: true });
            },

            clasificarPedidos() {
                this.pedidosPorEnviar = [];
                this.pedidosEnCamino = [];

                this.rawPedidos.forEach(p => {
                    let com = p.comentarios || '';
                    if (p.tipo_servicio == 3 && com.includes('EN CAMINO')) {
                        // Extraemos repartidor y hora
                        let matchRep = com.match(/Repartidor:\s*([^|]+)/i);
                        p.repartidorAsignado = matchRep ? matchRep[1].trim() : 'Desconocido';

                        let matchHora = com.match(/EN CAMINO \(([^)]+)\)/i);
                        p.horaSalida = matchHora ? matchHora[1].trim() : '--:--';

                        this.pedidosEnCamino.push(p);
                    } else {
                        this.pedidosPorEnviar.push(p);
                    }
                });
            },

            // CÁLCULO DE TIEMPO BLINDADO CONTRA ERRORES DE ZONA HORARIA
            tiempoTranscurrido(fecha_hora) {
                if(!fecha_hora) return 0;
                // Separa "2024-05-10 14:30:00" en arreglos para forzar la hora local de Oaxaca/México
                let parts = fecha_hora.split(/[- :]/); 
                let past = new Date(parts[0], parts[1]-1, parts[2], parts[3], parts[4], parts[5]);
                let diffMs = this.now - past;
                let mins = Math.floor(diffMs / 60000);
                return mins > 0 ? mins : 0;
            },

            formatearNombre(p) {
                if(p.tipo_servicio == 1) return p.nombreClie ? p.nombreClie : 'Cliente Mesa ' + p.mesa;
                if(p.tipo_servicio == 2) return p.nombreClie ? p.nombreClie : 'Cliente Mostrador';
                return (p.cnombre || 'Sin Nombre') + ' ' + (p.capellido || '');
            },

            formatearDetalle(p) {
                if(p.tipo_servicio == 1) return `Mesa ${p.mesa || '?'}`;
                if(p.tipo_servicio == 2) return `Para Llevar (Pasa a recoger)`;
                
                let d = `<span class="font-bold text-gray-800">${p.calle || 'Sin calle'}</span>`;
                d += `<br>Mz: ${p.manzana || '-'} | Lt: ${p.lote || '-'}`;
                if(p.colonia) d += `<br>Col: ${p.colonia}`;
                if(p.referencia) d += `<br><span class="italic text-gray-500 text-xs">Ref: ${p.referencia}</span>`;
                return d;
            }
        }));
    });
</script>
@endsection