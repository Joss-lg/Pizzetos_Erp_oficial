@extends('layouts.app')

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <div class="bg-white rounded-full p-0.5"><svg class="w-4 h-4 text-[#00b300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></div>
    <span class="font-medium text-[15px]">{{ session('success') }}</span>
</div>
@endif

@if(session('download_pdf'))
<script>
    window.addEventListener('DOMContentLoaded', function() {
        window.open("{{ route('flujo.caja.pdf', session('download_pdf')) }}", "_blank");
    });
</script>
@endif

<div class="w-full min-h-[70vh]">

    @if(!$cajaAbierta)
        <div class="flex flex-col items-center justify-center mt-12">
            <h2 class="text-[28px] font-bold text-[#1e293b] tracking-tight mb-8">Gestión de Caja</h2>
            <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 p-8 w-full max-w-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-[#1e293b]">Apertura de Caja</h3>
                    <a href="{{ route('flujo.caja.historial') }}" class="text-amber-600 hover:text-amber-700 text-xs font-bold underline flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ver Historial
                    </a>
                </div>
                <form action="{{ route('flujo.caja.abrir') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm text-[#475569] mb-1.5">Monto Inicial <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="monto_inicial" required placeholder="0.00" class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm text-[#475569] mb-1.5">Observaciones</label>
                        <input type="text" name="observaciones" placeholder="Opcional" class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400 transition-colors">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#eab308] hover:bg-[#ca8a04] text-white font-semibold py-3 rounded-md transition-colors text-[15px]">Abrir Caja</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        @php
            $totalMontoPagos = $stats['efectivo'] + $stats['tarjeta'] + $stats['transferencia'];
            $pctEfectivo = $totalMontoPagos > 0 ? round(($stats['efectivo'] / $totalMontoPagos) * 100, 1) : 0;
            $pctTarjeta = $totalMontoPagos > 0 ? round(($stats['tarjeta'] / $totalMontoPagos) * 100, 1) : 0;
            $pctTransferencia = $totalMontoPagos > 0 ? round(($stats['transferencia'] / $totalMontoPagos) * 100, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Caja #{{ $cajaAbierta->id_caja }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Apertura: {{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->locale('es')->translatedFormat('d \d\e F \d\e Y, h:i a') }}</p>
                        <p class="text-sm text-gray-500">Cajero: <span class="font-medium text-gray-700">{{ $cajaAbierta->cajero_nombre ?? 'Administrador' }}</span></p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="bg-green-100 text-green-700 border border-green-200 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Activa
                        </div>
                        <a href="{{ route('flujo.caja.historial') }}" class="text-amber-600 hover:text-amber-700 text-xs font-bold underline italic">Consultar historial de cajas</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                        <div class="bg-gray-100 w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Fondo Inicial</p>
                            <p class="text-2xl font-black text-gray-800">${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                        <div class="bg-[#f3e8ff] w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-[#9333ea]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Núm. Ventas</p>
                            <p class="text-2xl font-black text-gray-800">{{ $stats['num_ventas'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                        <div class="bg-red-50 w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Gastos</p>
                            <p class="text-2xl font-black text-gray-800">${{ number_format($stats['total_gastos'], 2) }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                        <div class="bg-blue-50 w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Venta Total</p>
                            <p class="text-2xl font-black text-gray-800">${{ number_format($stats['venta_total'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Desglose de Ventas por Método de Pago</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-[#ecfdf5] border border-[#d1fae5] rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2 text-green-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 576 512"><path d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm64 320H64V336c35.3 0 64 28.7 64 64zM64 192V128h64c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64v64H448zm64-192c-35.3 0-64-28.7-64-64h64v64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>
                                <span class="font-bold text-sm">Efectivo</span>
                            </div>
                            <p class="text-2xl font-black text-green-900">${{ number_format($stats['efectivo'], 2) }}</p>
                            <p class="text-[10px] text-green-600 mt-1 font-semibold">{{ $pctEfectivo }}% del total</p>
                        </div>
                        <div class="bg-[#eff6ff] border border-[#dbeafe] rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2 text-blue-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96v32H576V96c0-35.3-28.7-64-64-64H64zM576 224H0V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V224zM112 352h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm112 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H240c-8.8 0-16-7.2-16-16z"/></svg>
                                <span class="font-bold text-sm">Tarjeta</span>
                            </div>
                            <p class="text-2xl font-black text-blue-900">${{ number_format($stats['tarjeta'], 2) }}</p>
                            <p class="text-[10px] text-blue-600 mt-1 font-semibold">{{ $pctTarjeta }}% del total</p>
                        </div>
                        <div class="bg-[#faf5ff] border border-[#f3e8ff] rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2 text-purple-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><path d="M0 224c0 17.7 14.3 32 32 32s32-14.3 32-32c0-53 43-96 96-96h160v32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9S320 19.1 320 32V64H160C71.6 64 0 135.6 0 224zm512 64c0-17.7-14.3-32-32-32s-32 14.3-32 32c0 53-43 96-96 96H192v-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6V448h160c88.4 0 160-71.6 160-160z"/></svg>
                                <span class="font-bold text-sm">Transferencia</span>
                            </div>
                            <p class="text-2xl font-black text-purple-900">${{ number_format($stats['transferencia'], 2) }}</p>
                            <p class="text-[10px] text-purple-600 mt-1 font-semibold">{{ $pctTransferencia }}% del total</p>
                        </div>
                    </div>
                </div>

                <div x-data="{ open: true }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors">
                        <h3 class="font-bold text-gray-800 text-[15px]">Detalle de Ventas por Caja</h3>
                        <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="p-5 border-t border-gray-100 bg-gray-50/50 min-h-[80px] flex items-center justify-center">
                            @if($ventas_detalle->isEmpty())
                                <p class="text-sm text-gray-500 font-medium text-center">No hay ventas registradas.</p>
                            @else
                                <div class="w-full text-sm">
                                    @foreach($ventas_detalle as $v)
                                        <div class="flex justify-between border-b border-gray-200 py-2 last:border-0">
                                            <span class="text-gray-600">Venta #{{ $v->id_venta }} - {{ \Carbon\Carbon::parse($v->fecha_hora)->format('H:i') }}</span>
                                            <span class="font-bold text-green-600">${{ number_format($v->total, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors">
                        <h3 class="font-bold text-gray-800 text-[15px]">Detalle de Gastos</h3>
                        <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="p-5 border-t border-gray-100 bg-gray-50/50 min-h-[80px] flex items-center justify-center">
                            @if($gastos_detalle->isEmpty())
                                <p class="text-sm text-gray-500 font-medium text-center">No hay gastos registrados.</p>
                            @else
                                <div class="w-full text-sm">
                                    @foreach($gastos_detalle as $g)
                                        <div class="flex justify-between border-b border-gray-200 py-2 last:border-0">
                                            <span class="text-gray-600">{{ $g->descripcion }}</span>
                                            <span class="font-bold text-red-500">-${{ number_format($g->precio, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 relative" 
                 x-data="{ 
                    modalCerrar: false,
                    montoContado: '',
                    montoInicial: {{ $cajaAbierta->monto_inicial }},
                    ventasEfectivo: {{ $stats['efectivo'] }},
                    gastos: {{ $stats['total_gastos'] }},
                    get balanceEsperado() {
                        return this.montoInicial + this.ventasEfectivo - this.gastos;
                    },
                    get diferencia() {
                        let contado = parseFloat(this.montoContado) || 0;
                        return contado - this.balanceEsperado;
                    }
                 }">
                 
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-6">Cerrar Caja</h3>

                    <div class="bg-[#fffbeb] border border-[#fef08a] rounded-lg p-4 mb-6">
                        <p class="text-sm text-amber-800 font-medium leading-relaxed">
                            <span class="font-bold flex items-center gap-1.5 mb-1 text-amber-600">⚠️ Importante:</span>
                            Al cerrar la caja, se guardará el balance final y no podrá registrar más ventas en esta sesión.
                        </p>
                    </div>

                    <form id="formCerrarCaja" action="{{ route('flujo.caja.cerrar', $cajaAbierta->id_caja) }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Monto Final en Caja (Recuento Físico) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">$</span>
                                <input type="number" step="0.01" name="monto_final" x-model="montoContado" required class="w-full border border-gray-300 rounded-md pl-7 pr-3 py-2.5 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400 text-sm">
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm">
                            <div class="flex justify-between text-gray-500 mb-1">
                                <span>Balance esperado:</span>
                                <span x-text="`$${balanceEsperado.toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between text-gray-500 mb-3">
                                <span>Monto contado:</span>
                                <span x-text="montoContado ? `$${parseFloat(montoContado).toFixed(2)}` : '$0.00'"></span>
                            </div>
                            <div class="flex justify-between font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-800">Diferencia:</span>
                                <span :class="{
                                    'text-green-600': diferencia > 0, 
                                    'text-red-600': diferencia < 0, 
                                    'text-gray-800': diferencia === 0
                                }" x-text="montoContado === '' ? '$0.00' : (diferencia > 0 ? `+$${diferencia.toFixed(2)} (Sobrante)` : (diferencia < 0 ? `-$${Math.abs(diferencia).toFixed(2)} (Faltante)` : `$0.00 (Exacto)`))"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Observaciones de Cierre</label>
                            <textarea name="observaciones_cierre" placeholder="Opcional: Notas sobre el cierre, incidencias, etc." rows="3" class="w-full border border-gray-300 rounded-md p-3 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400 text-sm resize-none"></textarea>
                        </div>

                        <div class="space-y-3 pt-2">
                            <button type="button" @click="if(montoContado !== '') { modalCerrar = true } else { alert('Por favor, ingresa el Monto Final en Caja.') }" class="w-full bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold py-3 rounded-md transition-colors text-[14px]">
                                Confirmar Cierre de Caja
                            </button>
                            
                            <a href="{{ route('flujo.caja.pdf', $cajaAbierta->id_caja) }}" target="_blank" class="w-full bg-[#eab308] hover:bg-[#ca8a04] text-white font-bold py-3 rounded-md transition-colors text-[14px] flex items-center justify-center gap-2 text-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Descargar Reporte PDF
                            </a>
                        </div>

                        <div x-show="modalCerrar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 overflow-hidden text-center" @click.away="modalCerrar = false">
                                <div class="w-12 h-12 rounded-full bg-red-100 mx-auto flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">Cerrar Caja</h3>
                                <p class="text-gray-500 text-sm mb-6 mt-2">¿Estás seguro que deseas cerrar la caja? Al cerrar la caja, se guardará el balance final y no podrá registrar más ventas en esta sesión.</p>
                                <div class="flex gap-3 mt-6">
                                    <button @click="modalCerrar = false" type="button" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2.5 rounded-lg transition-colors text-sm">Cancelar</button>
                                    <button type="button" @click="document.getElementById('formCerrarCaja').submit()" class="flex-1 bg-[#ef4444] hover:bg-red-600 text-white font-bold py-2.5 rounded-lg transition-colors text-sm shadow-sm">Sí, cerrar caja</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection