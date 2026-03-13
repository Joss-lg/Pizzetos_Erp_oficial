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

<div class="w-full min-h-[70vh] font-sans">

    @if(!$cajaAbierta)
        <div class="flex flex-col items-center justify-center mt-12 px-4 text-center">
            <div class="bg-amber-100 w-20 h-20 rounded-full flex items-center justify-center mb-6 shadow-inner">
                <svg class="w-10 h-10 text-amber-600" fill="currentColor" viewBox="0 0 512 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H448c17.7 0 32-14.3 32-32s-14.3-32-32-32H64z"/></svg>
            </div>
            <h2 class="text-3xl font-black text-[#1e293b] tracking-tight mb-2 uppercase italic">Turno de Caja Inactivo</h2>
            <p class="text-gray-500 mb-10 max-w-sm">Para registrar transacciones es obligatorio realizar la apertura del turno con el fondo inicial correspondiente.</p>
            
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full max-w-md transition-all hover:shadow-2xl">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-[#1e293b] italic uppercase text-xs tracking-[0.2em]">Apertura de Caja</h3>
                    <a href="{{ route('flujo.caja.historial') }}" class="text-amber-600 hover:text-amber-700 text-xs font-black uppercase underline">Ver Historial</a>
                </div>
                <form action="{{ route('flujo.caja.abrir') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="text-left">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fondo de Inicio (Contado) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400 font-black text-lg">$</span>
                            <input type="number" step="0.01" name="monto_inicial" required value="3000.00" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-10 pr-4 py-4 focus:border-amber-400 text-xl font-black transition-all outline-none">
                        </div>
                    </div>
                    <div class="text-left">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones</label>
                        <input type="text" name="observaciones" placeholder="Ej. Apertura turno matutino" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-amber-400 text-sm font-medium transition-all outline-none">
                    </div>
                    <button type="submit" class="w-full bg-black text-white font-black py-4 rounded-xl shadow-lg hover:bg-gray-800 transition-all uppercase tracking-widest italic">Establecer Fondo y Abrir</button>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                {{-- Info del Turno --}}
                <div class="flex justify-between items-end">
                    <div>
                        <div class="bg-green-100 text-green-700 border border-green-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm w-max mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Sistema Activo
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tighter italic uppercase">Caja #{{ $cajaAbierta->id_caja }}</h2>
                        <p class="text-sm text-gray-400 font-medium mt-1 uppercase italic tracking-tighter">Cajero Actual: <span class="text-gray-900 font-black">{{ $cajaAbierta->cajero_nombre ?? 'Administración' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Apertura</p>
                        <p class="text-sm font-black text-gray-700 uppercase tracking-tighter italic">{{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->format('d.m.y / h:i a') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="bg-gray-100 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Saldo de Apertura</p>
                            <p class="text-3xl font-black text-gray-800 tracking-tighter italic">${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="bg-red-50 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-red-400">Salidas / Gastos</p>
                            <p class="text-3xl font-black text-red-600 tracking-tighter italic">-${{ number_format($stats['total_gastos'], 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Dashboard de Pagos --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 transition-all hover:shadow-md">
                    <h3 class="font-black text-gray-800 uppercase italic tracking-tighter mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 512 512"><path d="M472 168H40c-22.1 0-40 17.9-40 40v192c0 22.1 17.9 40 40 40h432c22.1 0 40-17.9 40-40V208c0-22.1-17.9-40-40-40zM256 368c-44.2 0-80-35.8-80-80s35.8-80 80-80 80 35.8 80 80-35.8 80-80 80z"/></svg>
                        Conciliación por Métodos
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-green-50 border border-green-100 rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-3 text-green-700">
                                <span class="font-black text-[11px] uppercase tracking-wider italic">Efectivo</span>
                                <span class="font-bold text-[10px] bg-white px-2 py-0.5 rounded-full shadow-sm">{{ $pctEfectivo }}%</span>
                            </div>
                            <p class="text-2xl font-black text-green-900 tracking-tighter italic">${{ number_format($stats['efectivo'], 2) }}</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-3 text-blue-700">
                                <span class="font-black text-[11px] uppercase tracking-wider italic">Tarjetas</span>
                                <span class="font-bold text-[10px] bg-white px-2 py-0.5 rounded-full shadow-sm">{{ $pctTarjeta }}%</span>
                            </div>
                            <p class="text-2xl font-black text-blue-900 tracking-tighter italic">${{ number_format($stats['tarjeta'], 2) }}</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-100 rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-3 text-purple-700">
                                <span class="font-black text-[11px] uppercase tracking-wider italic">Transferencias</span>
                                <span class="font-bold text-[10px] bg-white px-2 py-0.5 rounded-full shadow-sm">{{ $pctTransferencia }}%</span>
                            </div>
                            <p class="text-2xl font-black text-purple-900 tracking-tighter italic">${{ number_format($stats['transferencia'], 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- TABLA DETALLE DE VENTAS --}}
                <div x-data="{ open: true }" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-12">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-6 hover:bg-gray-50 transition-colors">
                        <h3 class="font-black text-gray-800 uppercase italic tracking-tighter text-sm italic">Detalle de Operaciones ({{ $stats['num_ventas'] }})</h3>
                        <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="p-0 border-t border-gray-50">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                        <th class="px-6 py-4">Orden</th>
                                        <th class="px-6 py-4">Hora</th>
                                        <th class="px-6 py-4">Servicio / Cliente</th>
                                        <th class="px-6 py-4">Métodos de Pago Utilizados</th>
                                        <th class="px-6 py-4 text-right">Monto Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-sm">
                                    @foreach($ventas_detalle as $venta)
                                        <tr class="hover:bg-gray-50 transition-colors {{ $venta->status == 3 ? 'bg-red-50/50' : '' }}">
                                            <td class="px-6 py-5 font-black {{ $venta->status == 3 ? 'text-red-700' : 'text-gray-900' }} italic">#{{ $venta->id_venta }}</td>
                                            <td class="px-6 py-5 text-gray-400 font-bold text-xs uppercase">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('h:i a') }}</td>
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col">
                                                    <span class="text-gray-700 font-black uppercase italic tracking-tighter text-[13px]">
                                                        @if($venta->tipo_servicio == 1) MESA {{ $venta->mesa }} - {{ $venta->nombreClie }}
                                                        @elseif($venta->tipo_servicio == 2) MOSTRADOR
                                                        @else {{ $venta->nombreClie ?? 'DOMICILIO' }}
                                                        @endif
                                                    </span>
                                                    @if($venta->status == 3)
                                                        <span class="text-[9px] text-red-600 font-black uppercase tracking-widest mt-1 italic">Operación Anulada</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- DESGLOSE DE MÚLTIPLES MÉTODOS Y MONTOS --}}
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col gap-1">
                                                    @if($venta->metodos_pago)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="p-1 bg-amber-100 text-amber-600 rounded">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            </span>
                                                            <span class="text-[11px] font-black uppercase italic text-gray-700 tracking-tighter leading-none">
                                                                {{ $venta->metodos_pago }}
                                                            </span>
                                                        </div>
                                                        {{-- Detalle de los montos sumados --}}
                                                        <span class="text-[10px] font-bold text-gray-400 italic ml-6 leading-none">
                                                            ({{ $venta->montos_detalle }})
                                                        </span>
                                                    @else
                                                        <span class="text-[11px] font-black uppercase italic text-gray-300 tracking-widest ml-6 italic">Sin cobro registrado</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-6 py-5 text-right font-black {{ $venta->status == 3 ? 'text-red-400 line-through' : 'text-gray-900 tracking-tighter italic text-base' }}">
                                                ${{ number_format($venta->total, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- DETALLE DE GASTOS --}}
                <div x-data="{ open: false }" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-12">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-6 hover:bg-gray-50 transition-colors">
                        <h3 class="font-black text-gray-800 uppercase italic tracking-tighter text-sm italic">Registros de Egresos</h3>
                        <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="p-6 border-t border-gray-50 bg-gray-50/30">
                        @if($gastos_detalle->isEmpty())
                            <p class="text-xs text-gray-400 font-bold text-center py-4 uppercase tracking-widest italic">No se han registrado salidas de efectivo</p>
                        @else
                            <div class="space-y-2">
                                @foreach($gastos_detalle as $g)
                                    <div class="flex justify-between items-center bg-white border border-gray-100 p-3 rounded-xl shadow-sm hover:shadow-md transition-all">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-tight italic">{{ $g->descripcion }}</span>
                                        <span class="font-black text-red-500 italic">-${{ number_format($g->precio, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- PANEL DE CIERRE --}}
            <div class="lg:col-span-1" x-data="{ modalCerrar: false, montoContado: '', montoInicial: {{ $cajaAbierta->monto_inicial }}, ventasEfectivo: {{ $stats['efectivo'] }}, gastos: {{ $stats['total_gastos'] }}, get balanceEsperado() { return (this.montoInicial + this.ventasEfectivo - this.gastos); }, get diferencia() { let contado = parseFloat(this.montoContado) || 0; return contado - this.balanceEsperado; } }">
                <div class="bg-white rounded-[3rem] shadow-2xl border-2 border-amber-400 p-8 sticky top-8 transition-all hover:shadow-amber-200/20">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">Cerrar Turno</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-2 italic">Contabilidad y Arqueo</p>
                    </div>

                    <form id="formCerrarCaja" action="{{ route('flujo.caja.cerrar', $cajaAbierta->id_caja) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Monto Físico en Caja (CONTADO)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 font-black text-lg">$</span>
                                <input type="number" step="0.01" name="monto_final" x-model="montoContado" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-3xl pl-10 pr-4 py-5 focus:border-amber-400 text-2xl font-black transition-all outline-none italic tracking-tighter">
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 space-y-3">
                            <div class="flex justify-between items-center text-xs font-bold uppercase tracking-widest italic">
                                <span class="text-gray-400">Balance del Sistema:</span>
                                <span class="text-gray-900 font-black" x-text="`$${balanceEsperado.toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between items-center font-black pt-4 border-t border-gray-200 uppercase italic">
                                <span class="text-[10px] tracking-widest text-gray-400">Diferencia Final:</span>
                                <span class="text-xl tracking-tighter" :class="diferencia > 0 ? 'text-green-600' : (diferencia < 0 ? 'text-red-600' : 'text-gray-900')" x-text="montoContado === '' ? '$0.00' : (diferencia > 0 ? `+$${diferencia.toFixed(2)}` : (diferencia < 0 ? `-$${Math.abs(diferencia).toFixed(2)}` : `$0.00`))"></span>
                            </div>
                        </div>

                        <textarea name="observaciones_cierre" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 focus:border-amber-400 text-sm font-bold uppercase italic tracking-tighter transition-all resize-none outline-none" placeholder="Agregar nota al reporte final..."></textarea>

                        <button type="button" @click="if(montoContado !== '') { modalCerrar = true } else { alert('Ingresa el monto contado primero.') }" class="w-full bg-black text-white font-black py-5 rounded-3xl shadow-xl hover:bg-gray-800 transition-all uppercase tracking-widest italic tracking-tighter">Finalizar Operaciones</button>
                        
                        <div class="text-center pt-2">
                            <a href="{{ route('flujo.caja.pdf', $cajaAbierta->id_caja) }}" target="_blank" class="text-amber-600 hover:text-amber-700 text-[10px] font-black uppercase underline italic tracking-widest">Previsualizar Reporte PDF</a>
                        </div>

                        {{-- MODAL DE SEGURIDAD --}}
                        <div x-show="modalCerrar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
                            <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-sm p-10 text-center transition-all">
                                <div class="w-20 h-20 rounded-3xl bg-red-100 mx-auto flex items-center justify-center mb-6 shadow-inner">
                                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <h3 class="text-2xl font-black text-gray-900 mb-2 italic uppercase tracking-tighter leading-none">Confirmar Cierre</h3>
                                <p class="text-gray-400 text-xs font-bold uppercase italic tracking-tighter mb-8 leading-relaxed">Esta acción bloqueará las ventas nuevas y generará el acta definitiva del turno. ¿Continuar?</p>
                                <div class="flex gap-4">
                                    <button @click="modalCerrar = false" type="button" class="flex-1 bg-gray-100 text-gray-500 font-black py-4 rounded-2xl text-[10px] uppercase tracking-widest italic hover:bg-gray-200 transition-colors">Cancelar</button>
                                    <button type="button" @click="document.getElementById('formCerrarCaja').submit()" class="flex-1 bg-red-600 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-widest italic shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Sí, Cerrar</button>
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