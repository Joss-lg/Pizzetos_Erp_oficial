@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="p-4 sm:p-6 h-[calc(100vh-95px)] overflow-y-auto bg-[#f8f9fa]" x-data="especialesApp()">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-800 italic uppercase tracking-tighter leading-none">Pedidos Especiales</h1>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Por entregar</p>
            </div>
            <a href="{{ route('ventas.pos') }}" class="w-full sm:w-auto justify-center bg-gray-800 hover:bg-black text-white px-5 py-2.5 rounded-lg font-bold shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver al POS
            </a>
        </div>

        @if(count($especiales) === 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="bg-cyan-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 mb-1">Todo al día</h3>
                <p class="text-gray-500 font-medium">No hay pedidos especiales pendientes por entregar.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="w-full overflow-x-auto min-w-full inline-block align-middle">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-[11px] uppercase tracking-widest">
                                <th class="p-4 font-black">Entrega / Hora</th>
                                <th class="p-4 font-black">Folio / Cliente</th>
                                <th class="p-4 font-black text-right">Total</th>
                                <th class="p-4 font-black text-right">Abonado</th>
                                <th class="p-4 font-black text-right">Resta</th>
                                <th class="p-4 font-black text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-gray-700">
                            @foreach($especiales as $esp)
                                <tr class="border-b border-gray-100 hover:bg-cyan-50/30 transition-colors">
                                    <td class="p-4">
                                        <span class="block font-black text-black text-[15px]">{{ \Carbon\Carbon::parse($esp->fecha_entrega)->format('d/m/Y') }}</span>
                                        <span class="text-[#17a2b8] font-bold">{{ \Carbon\Carbon::parse($esp->fecha_entrega)->format('h:i A') }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="block font-black text-gray-800">#{{ str_pad($esp->id_venta, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="text-gray-600 block">{{ $esp->cliente_display }}</span>
                                        <span class="text-xs text-gray-400 font-bold bg-gray-100 px-2 py-0.5 rounded">{{ $esp->telefono_display }}</span>
                                    </td>
                                    <td class="p-4 text-right font-black text-gray-800">${{ number_format($esp->total, 2) }}</td>
                                    <td class="p-4 text-right font-bold text-green-600">${{ number_format($esp->pagado, 2) }}</td>
                                    <td class="p-4 text-right font-black text-red-500 text-lg">${{ number_format($esp->restante, 2) }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="imprimirTicket({{ $esp->id_venta }})" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors" title="Imprimir Ticket">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </button>

                                            <a href="/venta/pos?id_venta={{ $esp->id_venta }}" class="p-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors" title="Editar Pedido">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>

                                            <button @click="abrirAbono({{ json_encode($esp) }})" class="p-2.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors" title="Agregar Abono">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>

                                            <button @click="abrirCobro({{ json_encode($esp) }})" class="bg-[#17a2b8] hover:bg-[#138496] text-white px-3 py-2.5 rounded-lg font-bold shadow-sm transition-all text-[11px] uppercase tracking-wider flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Entregar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div x-show="modalCobrar" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-[400px] flex flex-col overflow-hidden" @click.away="modalCobrar = false">
            <div class="bg-[#17a2b8] p-5 flex justify-between items-center text-white">
                <h2 class="text-lg font-black uppercase italic tracking-wider">Liquidar Pedido #<span x-text="pedidoActual?.id_venta"></span></h2>
                <button @click="modalCobrar = false" class="hover:text-gray-200 font-black text-2xl leading-none">&times;</button>
            </div>
            
            <div class="p-6 bg-white space-y-5">
                <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Resta por Pagar</p>
                    <p class="text-4xl font-black text-red-500" x-text="'$' + (pedidoActual?.restante || 0).toFixed(2)"></p>
                </div>

                <div x-show="pedidoActual?.restante > 0">
                    <label class="block text-[11px] font-black text-gray-600 uppercase mb-2">Método de pago del restante</label>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <button @click="metodoPago = 2" :class="metodoPago === 2 ? 'bg-black text-white' : 'bg-gray-100 text-gray-600'" class="py-2.5 rounded-lg font-bold text-xs transition-colors">Efectivo</button>
                        <button @click="metodoPago = 1" :class="metodoPago === 1 ? 'bg-black text-white' : 'bg-gray-100 text-gray-600'" class="py-2.5 rounded-lg font-bold text-xs transition-colors">Tarjeta</button>
                        <button @click="metodoPago = 3" :class="metodoPago === 3 ? 'bg-black text-white' : 'bg-gray-100 text-gray-600'" class="py-2.5 rounded-lg font-bold text-xs transition-colors">Transf.</button>
                    </div>

                    <div x-show="metodoPago === 3" x-transition>
                        <input type="text" x-model="referencia" placeholder="Referencia / 4 dígitos" class="w-full border-2 border-gray-200 rounded-lg py-3 px-4 font-bold text-sm focus:outline-none focus:border-black">
                    </div>
                </div>
            </div>

            <div class="p-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button @click="modalCobrar = false" class="flex-1 font-bold text-gray-500 bg-white border border-gray-200 rounded-xl py-3">Cancelar</button>
                <button @click="procesarEntrega()" :disabled="isProcessing || (metodoPago === 3 && pedidoActual?.restante > 0 && !referencia)" class="flex-1 bg-[#28a745] hover:bg-[#218838] text-white font-black rounded-xl py-3 shadow-md uppercase italic disabled:opacity-50">
                    <span x-show="!isProcessing">Entregar</span>
                    <span x-show="isProcessing">Cargando...</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="modalAbono" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-[450px] flex flex-col overflow-hidden" @click.away="modalAbono = false">
            <div class="bg-amber-400 p-5 flex justify-between items-center text-black">
                <h2 class="text-lg font-black uppercase italic tracking-wider">Abonar a Pedido #<span x-text="pedidoActual?.id_venta"></span></h2>
                <button @click="modalAbono = false" class="hover:text-gray-800 font-black text-2xl leading-none">&times;</button>
            </div>
            
            <div class="p-6 bg-white space-y-5">
                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Restante Actual</p>
                        <p class="text-2xl font-black text-gray-800" x-text="'$' + (pedidoActual?.restante || 0).toFixed(2)"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nuevo Restante</p>
                        <p class="text-2xl font-black" :class="getTotalAbono() > pedidoActual?.restante ? 'text-red-500' : 'text-green-600'" x-text="'$' + Math.max(0, (pedidoActual?.restante || 0) - getTotalAbono()).toFixed(2)"></p>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-[11px] font-black text-gray-600 uppercase mb-2">Ingresa los montos del abono</label>
                    
                    <div class="flex items-center gap-3">
                        <label class="w-20 text-[11px] font-black text-slate-500 uppercase">Efectivo $</label>
                        <input type="number" step="0.1" min="0" x-model.number="abonos.efectivo" placeholder="0.00" class="flex-1 bg-white border-2 border-slate-200 rounded-xl py-2 px-3 font-black text-[#28a745] focus:outline-none focus:border-amber-400 transition-colors">
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="w-20 text-[11px] font-black text-slate-500 uppercase">Tarjeta $</label>
                        <input type="number" step="0.1" min="0" x-model.number="abonos.tarjeta" placeholder="0.00" class="flex-1 bg-white border-2 border-slate-200 rounded-xl py-2 px-3 font-black text-[#28a745] focus:outline-none focus:border-amber-400 transition-colors">
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="w-20 text-[11px] font-black text-slate-500 uppercase">Transf. $</label>
                        <div class="flex-1 flex gap-2">
                            <input type="number" step="0.1" min="0" x-model.number="abonos.transferencia" placeholder="0.00" class="w-1/2 bg-white border-2 border-slate-200 rounded-xl py-2 px-3 font-black text-[#28a745] focus:outline-none focus:border-amber-400 transition-colors">
                            <input type="text" x-model="abonos.referencia" placeholder="Ref." class="w-1/2 bg-white border-2 border-slate-200 rounded-xl py-2 px-3 text-sm font-bold text-slate-600 focus:outline-none focus:border-[#17a2b8] transition-colors">
                        </div>
                    </div>
                    <p class="text-[10px] text-red-500 font-bold mt-2 text-center" x-show="getTotalAbono() > pedidoActual?.restante">El abono no puede ser mayor al dinero restante</p>
                </div>
            </div>

            <div class="p-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button @click="modalAbono = false" class="flex-1 font-bold text-gray-500 bg-white border border-gray-200 rounded-xl py-3">Cancelar</button>
                <button @click="procesarAbono()" :disabled="isProcessing || !abonoValido()" class="flex-1 bg-black hover:bg-slate-800 text-white font-black rounded-xl py-3 shadow-md uppercase italic disabled:opacity-50">
                    <span x-show="!isProcessing">Guardar Abono</span>
                    <span x-show="isProcessing">Cargando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('especialesApp', () => ({
            modalCobrar: false,
            modalAbono: false,
            pedidoActual: null,
            metodoPago: 2, 
            referencia: '',
            isProcessing: false,
            
            abonos: {
                efectivo: '',
                tarjeta: '',
                transferencia: '',
                referencia: ''
            },

            abrirCobro(pedido) {
                this.pedidoActual = pedido;
                this.metodoPago = 2;
                this.referencia = '';
                this.modalCobrar = true;
            },

            abrirAbono(pedido) {
                this.pedidoActual = pedido;
                this.abonos = { efectivo: '', tarjeta: '', transferencia: '', referencia: '' };
                this.modalAbono = true;
            },

            getTotalAbono() {
                let e = parseFloat(this.abonos.efectivo) || 0;
                let t = parseFloat(this.abonos.tarjeta) || 0;
                let tr = parseFloat(this.abonos.transferencia) || 0;
                return e + t + tr;
            },

            abonoValido() {
                let total = this.getTotalAbono();
                if (total <= 0) return false;
                if (total > this.pedidoActual.restante) return false;
                if ((parseFloat(this.abonos.transferencia) || 0) > 0 && !this.abonos.referencia.trim()) return false;
                return true;
            },

            imprimirTicket(id_venta) {
                const width = 420; const height = 700;
                const left = (window.screen.width / 2) - (width / 2);
                const top = (window.screen.height / 2) - (height / 2);
                window.open('/venta/pos/ticket/' + id_venta, 'TicketEspecial', `width=${width},height=${height},left=${left},top=${top},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`); 
            },

            async procesarAbono() {
                this.isProcessing = true;
                let pagosToSend = [];
                
                if((parseFloat(this.abonos.efectivo) || 0) > 0) pagosToSend.push({id_metpago: 2, monto: parseFloat(this.abonos.efectivo)});
                if((parseFloat(this.abonos.tarjeta) || 0) > 0) pagosToSend.push({id_metpago: 1, monto: parseFloat(this.abonos.tarjeta)});
                if((parseFloat(this.abonos.transferencia) || 0) > 0) pagosToSend.push({id_metpago: 3, monto: parseFloat(this.abonos.transferencia), referencia: this.abonos.referencia});

                try {
                    let response = await fetch(`/pedidos-especiales/${this.pedidoActual.id_venta}/abono`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}', pagos: pagosToSend })
                    });
                    
                    let res = await response.json();
                    if(res.success) {
                        this.imprimirTicket(this.pedidoActual.id_venta);
                        setTimeout(() => { window.location.reload(); }, 1000);
                    } else {
                        alert("Error: " + res.message);
                        this.isProcessing = false;
                    }
                } catch (e) {
                    alert("Error de red");
                    this.isProcessing = false;
                }
            },

            async procesarEntrega() {
                this.isProcessing = true;
                
                let reqBody = {
                    _token: '{{ csrf_token() }}',
                    id_pespeciales: this.pedidoActual.id_pespeciales,
                    monto_cobrado: this.pedidoActual.restante,
                    id_metpago: this.metodoPago,
                    referencia: this.referencia
                };

                try {
                    let response = await fetch(`/pedidos-especiales/${this.pedidoActual.id_venta}/entregar`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(reqBody)
                    });
                    
                    let res = await response.json();
                    
                    if(res.success) {
                        this.modalCobrar = false;
                        this.imprimirTicket(res.id_venta);
                        setTimeout(() => { window.location.reload(); }, 1500);
                    } else {
                        alert("Error: " + res.message);
                        this.isProcessing = false;
                    }
                } catch (e) {
                    alert("Error de red");
                    this.isProcessing = false;
                }
            }
        }));
    });
</script>
@endsection