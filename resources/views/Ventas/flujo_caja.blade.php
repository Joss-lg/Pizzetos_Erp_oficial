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
        {{-- ========================================== --}}
        {{--        PANTALLA DE APERTURA DE CAJA         --}}
        {{-- ========================================== --}}
        <div class="flex flex-col items-center justify-center mt-12 px-4 text-center">
            <div class="bg-amber-100 w-20 h-20 rounded-full flex items-center justify-center mb-6 shadow-inner">
                <svg class="w-10 h-10 text-amber-600" fill="currentColor" viewBox="0 0 512 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H448c17.7 0 32-14.3 32-32s-14.3-32-32-32H64z"/></svg>
            </div>
            <h2 class="text-3xl font-black text-[#1e293b] tracking-tight mb-2">Punto de Venta Inactivo</h2>
            <p class="text-gray-500 mb-10 max-w-sm">Para comenzar a registrar ventas, es necesario abrir el turno de caja con un fondo inicial.</p>
            
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full max-w-md">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-[#1e293b] italic uppercase">Apertura de Caja</h3>
                    <a href="{{ route('flujo.caja.historial') }}" class="text-amber-600 hover:text-amber-700 text-xs font-black uppercase underline flex items-center gap-1">
                        Historial
                    </a>
                </div>
                <form action="{{ route('flujo.caja.abrir') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="text-left">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Fondo Inicial (Físico en caja) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400 font-black text-lg">$</span>
                            <input type="number" step="0.01" name="monto_inicial" required placeholder="0.00" value="3000.00"
                                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl pl-10 pr-4 py-4 outline-none focus:border-amber-400 focus:bg-white text-xl font-black transition-all">
                        </div>
                    </div>
                    <div class="text-left">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones</label>
                        <input type="text" name="observaciones" placeholder="Ej. Inicio de turno matutino" 
                               class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-amber-400 focus:bg-white text-sm font-medium transition-all">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-black text-white font-black py-4 rounded-xl shadow-lg hover:bg-gray-800 transition-all uppercase tracking-widest italic">
                            Abrir Turno ahora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- ========================================== --}}
        {{--        PANTALLA DE CAJA ACTIVA / RESUMEN    --}}
        {{-- ========================================== --}}
        @php
            $totalMontoPagos = $stats['efectivo'] + $stats['tarjeta'] + $stats['transferencia'];
            $pctEfectivo = $totalMontoPagos > 0 ? round(($stats['efectivo'] / $totalMontoPagos) * 100, 1) : 0;
            $pctTarjeta = $totalMontoPagos > 0 ? round(($stats['tarjeta'] / $totalMontoPagos) * 100, 1) : 0;
            $pctTransferencia = $totalMontoPagos > 0 ? round(($stats['transferencia'] / $totalMontoPagos) * 100, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                <div class="flex justify-between items-end">
                    <div>
                        <div class="bg-green-100 text-green-700 border border-green-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm w-max mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Turno en curso
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tighter italic">CAJA #{{ $cajaAbierta->id_caja }}</h2>
                        <p class="text-sm text-gray-400 font-medium mt-1">Cajero: <span class="text-gray-900 font-black">{{ $cajaAbierta->cajero_nombre ?? 'Administrador' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Iniciado el</p>
                        <p class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->format('d/m/Y - h:i a') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                        <div class="bg-gray-100 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Fondo de Apertura</p>
                            <p class="text-3xl font-black text-gray-800">${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                        <div class="bg-red-50 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-inner border border-red-100">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Gastos del Turno</p>
                            <p class="text-3xl font-black text-red-600">-${{ number_format($stats['total_gastos'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="font-black text-gray-800 uppercase italic tracking-tighter mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 512 512"><path d="M472 168H40c-22.1 0-40 17.9-40 40v192c0 22.1 17.9 40 40 40h432c22.1 0 40-17.9 40-40V208c0-22.1-17.9-40-40-40zM256 368c-44.2 0-80-35.8-80-80s35.8-80 80-80 80 35.8 80 80-35.8 80-80 80z"/></svg>
                        Ventas por Método de Pago
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-green-50 border border-green-100 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-3 text-green-700">
                                <span class="font-black text-[11px] uppercase tracking-wider">Efectivo</span>
                                <span class="font-bold text-[10px] bg-white px-2 py-0.5 rounded-full shadow-sm">{{ $pctEfectivo }}%</span>
                            </div>
                            <p class="text-2xl font-black text-green-900 tracking-tight">${{ number_format($stats['efectivo'], 2) }}</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-3 text-blue-700">
                                <span class="font-black text-[11px] uppercase tracking-wider">Tarjeta</span>
                                <span class="font-bold text-[10px] bg-white px-2 py-0.5 rounded-full shadow-sm">{{ $pctTarjeta }}%</span>
                            </div>
                            <p class="text-2xl font-black text-blue-900 tracking-tight">${{ number_format($stats['tarjeta'], 2) }}</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-100 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-3 text-purple-700">
                                <span class="font-black text-[11px] uppercase tracking-wider">Transferencia</span>
                                <span class="font-bold text-[10px] bg-white px-2 py-0.5 rounded-full shadow-sm">{{ $pctTransferencia }}%</span>
                            </div>
                            <p class="text-2xl font-black text-purple-900 tracking-tight">${{ number_format($stats['transferencia'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-6 hover:bg-gray-50 transition-colors">
                            <h3 class="font-black text-gray-800 uppercase italic tracking-tighter text-sm">Registro de Ventas ({{ $stats['num_ventas'] }})</h3>
                            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse x-cloak class="p-6 border-t border-gray-50 bg-gray-50/30">
                            @if($ventas_detalle->isEmpty())
                                <p class="text-sm text-gray-400 font-bold text-center py-4 uppercase">Sin movimientos registrados</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($ventas_detalle as $v)
                                        <div class="flex justify-between items-center bg-white border border-gray-100 p-3 rounded-xl shadow-sm">
                                            <span class="text-xs font-bold text-gray-500">Venta #{{ $v->id_venta }} <span class="mx-2 text-gray-200">|</span> {{ \Carbon\Carbon::parse($v->fecha_hora)->format('h:i a') }}</span>
                                            <span class="font-black text-green-600">${{ number_format($v->total, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-6 hover:bg-gray-50 transition-colors">
                            <h3 class="font-black text-gray-800 uppercase italic tracking-tighter text-sm">Registro de Gastos</h3>
                            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse x-cloak class="p-6 border-t border-gray-50 bg-gray-50/30">
                            @if($gastos_detalle->isEmpty())
                                <p class="text-sm text-gray-400 font-bold text-center py-4 uppercase">Sin gastos registrados</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($gastos_detalle as $g)
                                        <div class="flex justify-between items-center bg-white border border-gray-100 p-3 rounded-xl shadow-sm">
                                            <span class="text-xs font-bold text-gray-500">{{ $g->descripcion }}</span>
                                            <span class="font-black text-red-500">-${{ number_format($g->precio, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1" 
                 x-data="{ 
                    modalCerrar: false,
                    montoContado: '',
                    montoInicial: {{ $cajaAbierta->monto_inicial }},
                    ventasEfectivo: {{ $stats['efectivo'] }},
                    gastos: {{ $stats['total_gastos'] }},
                    get balanceEsperado() {
                        return (this.montoInicial + this.ventasEfectivo - this.gastos);
                    },
                    get diferencia() {
                        let contado = parseFloat(this.montoContado) || 0;
                        return contado - this.balanceEsperado;
                    }
                 }">
                 
                <div class="bg-white rounded-3xl shadow-2xl border-2 border-amber-400 p-8 sticky top-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">Cierre de Caja</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-2">Recuento final de efectivo</p>
                    </div>

                    <form id="formCerrarCaja" action="{{ route('flujo.caja.cerrar', $cajaAbierta->id_caja) }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Efectivo en Cajón (Contado) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 font-black text-lg">$</span>
                                <input type="number" step="0.01" name="monto_final" x-model="montoContado" required placeholder="0.00"
                                       class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-10 pr-4 py-4 outline-none focus:border-amber-400 focus:bg-white text-2xl font-black transition-all">
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 space-y-3">
                            <div class="flex justify-between items-center text-xs font-bold uppercase tracking-wider">
                                <span class="text-gray-400">Debería haber:</span>
                                <span class="text-gray-900 font-black" x-text="`$${balanceEsperado.toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between items-center font-black pt-3 border-t border-gray-200">
                                <span class="text-[10px] uppercase tracking-widest text-gray-400">Diferencia:</span>
                                <span class="text-lg" :class="{
                                    'text-green-600': diferencia > 0, 
                                    'text-red-600': diferencia < 0, 
                                    'text-gray-900': diferencia === 0
                                }" x-text="montoContado === '' ? '$0.00' : (diferencia > 0 ? `+$${diferencia.toFixed(2)}` : (diferencia < 0 ? `-$${Math.abs(diferencia).toFixed(2)}` : `$0.00`))"></span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Comentarios de Cierre</label>
                            <textarea name="observaciones_cierre" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-4 outline-none focus:border-amber-400 focus:bg-white text-sm font-medium transition-all resize-none" placeholder="Opcional..."></textarea>
                        </div>

                        <button type="button" @click="if(montoContado !== '') { modalCerrar = true } else { alert('Ingresa el monto contado primero.') }" 
                                class="w-full bg-black text-white font-black py-4 rounded-2xl shadow-xl hover:bg-gray-800 transition-all uppercase tracking-widest italic flex items-center justify-center gap-3">
                            Finalizar Turno
                        </button>

                        <div class="text-center">
                            <a href="{{ route('flujo.caja.pdf', $cajaAbierta->id_caja) }}" target="_blank" class="text-amber-600 hover:text-amber-700 text-xs font-black uppercase underline italic">Vista previa del reporte</a>
                        </div>

                        {{-- MODAL DE CONFIRMACIÓN --}}
                        <div x-show="modalCerrar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center" @click.away="modalCerrar = false">
                                <div class="w-16 h-16 rounded-full bg-red-100 mx-auto flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <h3 class="text-2xl font-black text-gray-900 mb-2 italic uppercase tracking-tighter">¿Cerrar Caja?</h3>
                                <p class="text-gray-500 text-sm font-medium leading-relaxed mb-8">Esta acción finalizará tu turno. Asegúrate de que el recuento sea correcto.</p>
                                
                                <div class="flex gap-4">
                                    <button @click="modalCerrar = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black py-4 rounded-xl transition-all text-xs uppercase tracking-widest">Atrás</button>
                                    <button type="button" @click="document.getElementById('formCerrarCaja').submit()" class="flex-1 bg-[#ef4444] hover:bg-red-700 text-white font-black py-4 rounded-xl transition-all text-xs uppercase tracking-widest shadow-lg shadow-red-200">Confirmar</button>
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