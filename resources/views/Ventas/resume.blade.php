@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-8 min-h-[400px]" x-data="historialApp()">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#0f172a] tracking-tight">Historial de Pedidos</h2>
            <p class="text-sm text-gray-500 mt-1">Historial completo de pedidos registrados en el sistema</p>
        </div>
        
        <form action="{{ route('ventas.resume') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="fecha" class="border border-gray-300 text-gray-700 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[160px]">
                <option value="hoy" {{ ($filtroFecha ?? 'hoy') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ ($filtroFecha ?? '') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                <option value="mes" {{ ($filtroFecha ?? '') == 'mes' ? 'selected' : '' }}>Este mes</option>
                <option value="todos" {{ ($filtroFecha ?? '') == 'todos' ? 'selected' : '' }}>Todos los registros</option>
            </select>

            <select name="estado" class="border border-gray-300 text-gray-700 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[160px]">
                <option value="todos" {{ ($filtroEstado ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                <option value="0" {{ ($filtroEstado ?? '') == '0' ? 'selected' : '' }}>Esperando</option>
                <option value="1" {{ ($filtroEstado ?? '') == '1' ? 'selected' : '' }}>Pagado</option>
                <option value="3" {{ ($filtroEstado ?? '') == '3' ? 'selected' : '' }}>Cancelado</option>
            </select>

            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-md text-sm font-bold flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Actualizar
            </button>
        </form>
    </div>

    @if($ventas->isEmpty())
        <div class="flex items-center justify-center py-20">
            <p class="text-gray-500 text-[15px]">No hay pedidos para mostrar</p>
        </div>
    @else
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">ID VENTA</th>
                        <th class="px-6 py-4 font-semibold">FECHA / HORA</th>
                        <th class="px-6 py-4 font-semibold">USUARIO</th>
                        <th class="px-6 py-4 font-semibold">CLIENTE / MESA</th>
                        <th class="px-6 py-4 font-semibold text-center">PRODUCTOS</th>
                        <th class="px-6 py-4 font-semibold">TOTAL</th>
                        <th class="px-6 py-4 font-semibold text-center">ESTADO</th>
                        <th class="px-6 py-4 font-semibold text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @foreach($ventas as $venta)
                        @php
                            // Extraer el nombre del usuario y el motivo de cancelación desde los comentarios
                            $usuarioVenta = 'Sistema';
                            $motivoCancelacion = '';
                            if ($venta->comentarios && str_contains($venta->comentarios, 'Atendió:')) {
                                $partes = explode('|', $venta->comentarios);
                                $usuarioVenta = trim(str_replace('Atendió:', '', $partes[0]));
                                
                                foreach($partes as $p) {
                                    if(str_contains($p, 'CANCELADO - Motivo:')) {
                                        $motivoCancelacion = trim(str_replace('CANCELADO - Motivo:', '', $p));
                                    }
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $venta->status == 3 ? 'bg-red-50/50 opacity-75' : '' }}">
                            <td class="px-6 py-4 font-bold text-gray-900">#{{ $venta->id_venta }}</td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y h:i A') }}</td>
                            
                            <td class="px-6 py-4 font-bold text-blue-600 whitespace-nowrap">{{ $usuarioVenta }}</td>

                            <td class="px-6 py-4 text-gray-700 font-medium">
                                @if($venta->tipo_servicio == 1)
                                    Mesa {{ $venta->mesa ?? '?' }} - {{ $venta->nombreClie ?? 'Sin Nombre' }}
                                @elseif($venta->tipo_servicio == 2)
                                    Mostrador (Para Llevar)
                                @else
                                    {{ $venta->nombreClie ?? ($venta->cnombre ?? 'Domicilio') }}
                                @endif

                                @if($motivoCancelacion)
                                    <div class="text-xs text-red-500 font-bold mt-1">Motivo: {{ $motivoCancelacion }}</div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-50 text-blue-700 border border-blue-200 font-bold px-2.5 py-1 rounded text-xs">
                                    {{ $venta->total_productos ?? \DB::table('DetalleVenta')->where('id_venta', $venta->id_venta)->sum('cantidad') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-black text-green-600">${{ number_format($venta->total, 2) }}</td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($venta->status == 0)
                                    <span class="bg-gray-100 text-gray-600 font-bold px-3 py-1 rounded-full text-xs">Cuenta Abierta</span>
                                @elseif($venta->status == 1)
                                    <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full text-xs">Pagado</span>
                                @elseif($venta->status == 3)
                                    <span class="bg-red-100 text-red-600 font-bold px-3 py-1 rounded-full text-xs">Cancelado</span>
                                @else
                                    <span class="bg-blue-100 text-blue-600 font-bold px-3 py-1 rounded-full text-xs">Preparando</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 flex items-center justify-center gap-3">
                                
                                @if($venta->status != 3)
                                    @if($venta->status == 0)
                                        <a href="/venta/pos?edit={{ $venta->id_venta }}" title="Editar Pedido" class="text-blue-500 hover:text-blue-700 bg-blue-50 p-1.5 rounded transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <button @click="abrirPago({{ $venta->id_venta }}, {{ $venta->total }}, false)" title="Pagar Cuenta" class="text-green-600 hover:text-green-800 bg-green-50 p-1.5 rounded transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </button>
                                    @endif

                                    @if($venta->status == 1)
                                        <button @click="abrirPago({{ $venta->id_venta }}, {{ $venta->total }}, true)" title="Editar Método de Pago" class="text-amber-600 hover:text-amber-800 bg-amber-50 p-1.5 rounded transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        </button>
                                    @endif

                                    <button @click="abrirCancelar({{ $venta->id_venta }})" title="Cancelar Pedido" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif

                                <a href="/venta/pos/ticket/{{ $venta->id_venta }}" target="_blank" title="Reimprimir Ticket" class="text-gray-500 hover:text-gray-800 bg-gray-100 p-1.5 rounded transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ======================================================= --}}
    {{-- MODAL CANCELAR PEDIDO                                   --}}
    {{-- ======================================================= --}}
    <div x-show="modalCancelar" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-[400px] max-w-full flex flex-col overflow-hidden" @click.away="modalCancelar = false">
            <div class="bg-red-600 p-5 flex justify-between items-center text-white">
                <h2 class="text-lg font-black" x-text="'Cancelar Ticket #' + id_venta_cancelar"></h2>
                <button @click="modalCancelar = false" class="hover:text-white/80 font-black text-2xl leading-none">&times;</button>
            </div>
            <div class="p-6 bg-white space-y-4">
                <p class="text-gray-600 text-[14px]">Esta acción anulará el ticket y no sumará al corte de caja. Por favor, ingresa el motivo:</p>
                <textarea x-model="motivo_cancelacion" rows="3" placeholder="Ej: El cliente se fue, pedido duplicado..." class="w-full border border-gray-300 rounded p-3 text-sm focus:outline-none focus:border-red-500"></textarea>
            </div>
            <div class="p-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button @click="modalCancelar = false" class="flex-1 bg-white border border-gray-300 text-gray-700 font-bold py-2.5 rounded-lg">Volver</button>
                <button @click="procesarCancelacion()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-lg shadow-sm">Confirmar Cancelación</button>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL MULTIPAGO RÁPIDO (Y EDICIÓN DE PAGO)              --}}
    {{-- ======================================================= --}}
    <div x-show="modalPago" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-[450px] max-w-full flex flex-col h-auto max-h-[90vh] overflow-hidden" @click.away="modalPago = false">
            <div :class="es_edicion_pago ? 'bg-blue-600' : 'bg-green-600'" class="p-5 flex justify-between items-center text-white relative">
                <h2 class="text-xl font-black" x-text="(es_edicion_pago ? 'Editar Pago #' : 'Cobrar Cuenta #') + id_venta_pago"></h2>
                <button @click="modalPago = false" class="hover:text-white/80 font-black text-2xl leading-none">&times;</button>
            </div>
            
            <div class="p-6 text-center border-b border-gray-100 bg-white">
                <div class="font-black text-[#1a202c] text-[36px] leading-none mb-1" x-text="'$' + total_pago.toFixed(2)"></div>
                <div class="text-[14px] font-bold" :class="faltaPagar() === 0 ? 'text-green-500' : (faltaPagar() < 0 ? 'text-blue-500' : 'text-red-500')" x-text="faltaPagar() === 0 ? 'Monto completo asignado' : (faltaPagar() < 0 ? 'Cambio: $' + Math.abs(faltaPagar()).toFixed(2) : 'Falta asignar: $' + faltaPagar().toFixed(2))"></div>
            </div>

            <div class="flex-1 overflow-y-auto p-5 bg-[#f8f9fa] space-y-4">
                <p class="text-[13px] text-gray-500 mb-2">Selecciona métodos de pago:</p>
                
                {{-- Efectivo (2) --}}
                <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.efectivo.activo ? 'border-amber-500' : 'border-gray-200'">
                    <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                        <input type="checkbox" x-model="pagos.efectivo.activo" @change="autoFillPago('efectivo')" class="w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                        <span class="font-bold text-[15px] text-[#212529]">Efectivo</span>
                    </label>
                    <div x-show="pagos.efectivo.activo" class="px-4 pb-4 pt-1 space-y-3">
                        <div>
                            <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Efectivo</label>
                            <input type="number" step="0.01" min="0" x-model.number="pagos.efectivo.monto" class="w-full border border-gray-300 rounded px-3 py-2 text-[14px] font-bold focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Tarjeta (1) --}}
                <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.tarjeta.activo ? 'border-amber-500' : 'border-gray-200'">
                    <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                        <input type="checkbox" x-model="pagos.tarjeta.activo" @change="autoFillPago('tarjeta')" class="w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                        <span class="font-bold text-[15px] text-[#212529]">Tarjeta</span>
                    </label>
                    <div x-show="pagos.tarjeta.activo" class="px-4 pb-4 pt-1">
                        <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Tarjeta</label>
                        <input type="number" step="0.01" min="0" x-model.number="pagos.tarjeta.monto" class="w-full border border-gray-300 rounded px-3 py-2 text-[14px] font-bold focus:outline-none">
                    </div>
                </div>

                {{-- Transferencia (3) --}}
                <div class="border rounded-[8px] overflow-hidden transition-all bg-white shadow-sm" :class="pagos.transferencia.activo ? 'border-amber-500' : 'border-gray-200'">
                    <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                        <input type="checkbox" x-model="pagos.transferencia.activo" @change="autoFillPago('transferencia')" class="w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                        <span class="font-bold text-[15px] text-[#212529]">Transferencia</span>
                    </label>
                    <div x-show="pagos.transferencia.activo" class="px-4 pb-4 pt-1 space-y-3">
                        <div>
                            <label class="block text-[12px] text-gray-500 mb-1">Monto a cobrar con Transferencia</label>
                            <input type="number" step="0.01" min="0" x-model.number="pagos.transferencia.monto" class="w-full border border-gray-300 rounded px-3 py-2 text-[14px] font-bold focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[12px] text-blue-600 font-bold mb-1">Número de Referencia *</label>
                            <input type="text" x-model="pagos.transferencia.referencia" class="w-full px-3 py-2 border border-blue-200 bg-blue-50 rounded text-[14px] focus:outline-none focus:border-blue-400">
                        </div>
                    </div>
                </div>

            </div>

            <div class="p-5 bg-white border-t border-gray-100 flex flex-col gap-2">
                <button @click="procesarPagoFinal()" :disabled="!pagosValidos()" :class="!pagosValidos() ? 'bg-[#1a202c]/50 text-white cursor-not-allowed' : 'bg-[#1a202c] hover:bg-black text-white shadow-md'" class="w-full font-bold py-3.5 rounded-[8px] text-[15px] transition-colors">
                    <span x-text="es_edicion_pago ? 'Guardar Nuevo Pago' : 'Confirmar Cobro'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('historialApp', () => ({
                modalPago: false,
                modalCancelar: false,
                id_venta_pago: null,
                id_venta_cancelar: null,
                total_pago: 0,
                es_edicion_pago: false,
                motivo_cancelacion: '',
                pagos: {
                    efectivo: { activo: false, monto: 0, entregado: null },
                    tarjeta: { activo: false, monto: null },
                    transferencia: { activo: false, monto: null, referencia: '' }
                },

                abrirCancelar(id) {
                    this.id_venta_cancelar = id;
                    this.motivo_cancelacion = '';
                    this.modalCancelar = true;
                },

                procesarCancelacion() {
                    if(!this.motivo_cancelacion.trim()) return alert("Por favor, ingresa el motivo de la cancelación.");
                    fetch("{{ route('ventas.cancelar') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}', id_venta: this.id_venta_cancelar, motivo: this.motivo_cancelacion })
                    }).then(r => r.json()).then(res => {
                        if(res.success) { window.location.reload(); } else { alert("Error: " + res.message); }
                    });
                },

                abrirPago(id, total, esEdicion) {
                    this.id_venta_pago = id;
                    this.total_pago = parseFloat(total);
                    this.es_edicion_pago = esEdicion;
                    this.pagos = {
                        efectivo: { activo: true, monto: this.total_pago, entregado: null },
                        tarjeta: { activo: false, monto: null },
                        transferencia: { activo: false, monto: null, referencia: '' }
                    };
                    this.modalPago = true;
                },

                autoFillPago(tipo) {
                    if(this.pagos[tipo].activo) {
                        let falta = this.faltaPagar();
                        if(falta > 0) this.pagos[tipo].monto = falta;
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

                faltaPagar() { return parseFloat((this.total_pago - this.getTotalPagarInputs()).toFixed(2)); },
                pagosValidos() { return this.faltaPagar() === 0; },

                procesarPagoFinal() {
                    if(!this.pagosValidos()) return;
                    let pagosToSend = [];
                    if(this.pagos.efectivo.activo && this.pagos.efectivo.monto > 0) pagosToSend.push({ id_metpago: 2, monto: this.pagos.efectivo.monto });
                    if(this.pagos.tarjeta.activo && this.pagos.tarjeta.monto > 0) pagosToSend.push({ id_metpago: 1, monto: this.pagos.tarjeta.monto }); 
                    if(this.pagos.transferencia.activo && this.pagos.transferencia.monto > 0) pagosToSend.push({ id_metpago: 3, monto: this.pagos.transferencia.monto, referencia: this.pagos.transferencia.referencia });

                    let url = this.es_edicion_pago ? "{{ route('ventas.editar_pago') }}" : "{{ route('ventas.pagar') }}";

                    fetch(url, {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}', id_venta: this.id_venta_pago, pagos: pagosToSend })
                    }).then(r => r.json()).then(res => {
                        if(res.success) {
                            this.modalPago = false;
                            if(!this.es_edicion_pago) window.open('/venta/pos/ticket/' + this.id_venta_pago, 'Ticket', 'width=400,height=600');
                            setTimeout(() => { window.location.reload(); }, 1000);
                        } else { alert("Error: " + res.message); }
                    });
                }
            }));
        });
    </script>
</div>
@endsection