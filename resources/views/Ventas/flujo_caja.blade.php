@extends('layouts.app')

@section('content')
<style>
    /* VARIABLES DE DISEÑO PIZZETOS ACTUALIZADAS */
    :root {
        --pizzetos-amber: #fbbf24;
        --pizzetos-radius: 45px;
        --pizzetos-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
    }

    .pizzetos-card {
        background: #ffffff !important;
        border-radius: var(--pizzetos-radius) !important;
        border: 1px solid #f1f5f9 !important;
        box-shadow: var(--pizzetos-shadow) !important;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .pizzetos-title { font-weight: 900 !important; font-style: italic !important; text-transform: uppercase !important; letter-spacing: -1px !important; }
    .pizzetos-label { font-size: 9px !important; font-weight: 900 !important; text-transform: uppercase !important; color: #94a3b8 !important; letter-spacing: 1px; font-style: italic; margin-bottom: 8px; }

    .pay-badge-split { display: flex; align-items: center; border-radius: 10px; overflow: hidden; font-size: 9px; font-weight: 900; text-transform: uppercase; border: 1px solid #f1f5f9; margin-bottom: 4px; }
    .pay-method { padding: 3px 8px; }
    .pay-amount { padding: 3px 8px; background: #fff; color: #1e293b; border-left: 1px solid #f1f5f9; }

    .bg-efectivo { background: #f0fdf4; color: #16a34a; }
    .bg-tarjeta { background: #eff6ff; color: #2563eb; }
    .bg-transf { background: #faf5ff; color: #9333ea; }

    .pizzetos-btn {
        background: var(--pizzetos-amber) !important;
        border-radius: 20px !important;
        padding: 1.2rem;
        font-weight: 900 !important;
        font-style: italic !important;
        text-transform: uppercase !important;
        color: #000 !important;
        border: none !important;
        transition: all 0.3s ease;
        cursor: pointer;
        text-align: center;
        display: block;
        width: 100%;
    }
    .pizzetos-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.3); }
</style>

{{-- LÓGICA DE AUTO-LANZAMIENTO DE PDF AL CERRAR --}}
@if(session('download_pdf'))
<script>
    window.addEventListener('DOMContentLoaded', function() {
        const width = 850;
        const height = 900;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);
        
        window.open(
            "{{ url('/venta/flujo-caja/pdf') }}/{{ session('download_pdf') }}", 
            'CierreCaja', 
            `width=${width},height=${height},left=${left},top=${top},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`
        );
    });
</script>
@endif

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
     class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 italic font-black uppercase tracking-tighter">
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="w-full space-y-8 pb-12 px-2">

    @if(!$cajaAbierta)
        {{-- VISTA DE APERTURA --}}
        <div class="flex flex-col items-center justify-center min-h-[70vh]">
            <div class="pizzetos-card w-full max-w-md text-center border-t-[10px] border-amber-400">
                <div class="bg-amber-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-4xl pizzetos-title text-slate-900 mb-2 italic">Abrir Turno</h2>
                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mb-10">Ingresa el fondo inicial para comenzar</p>
                
                <form action="{{ route('flujo.caja.abrir') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="relative">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 font-black text-2xl text-amber-500">$</span>
                        <input type="number" name="monto_inicial" step="0.01" required placeholder="0.00"
                               class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl py-6 pl-12 pr-6 text-4xl font-black focus:border-amber-400 focus:outline-none transition-all text-center italic tracking-tighter">
                    </div>
                    <button type="submit" class="pizzetos-btn shadow-lg shadow-amber-100 py-5 text-xl">
                        Iniciar Operaciones
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('flujo.caja.historial') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-black font-black uppercase italic text-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ver Historial de Cortes
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- MONITOR DE CAJA ABIERTA --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-[10px] pizzetos-title border border-green-200">Terminal Online</span>
                <h1 class="text-6xl pizzetos-title text-slate-900 mt-4 leading-none uppercase italic">Caja {{ $cajaAbierta->folio_virtual }}</h1>
                <p class="text-sm font-bold text-slate-400 uppercase italic">Responsable: <span class="text-amber-500">{{ $cajaAbierta->cajero_nombre }}</span></p>
            </div>
            
            <div class="flex gap-4">
                <div class="pizzetos-card" style="min-width: 250px; text-align: center; border-bottom: 8px solid var(--pizzetos-amber) !important; padding: 1.5rem !important;">
                    <span class="pizzetos-label">Fondo de Inicio</span>
                    <div class="text-4xl pizzetos-title text-slate-800 italic">${{ number_format($cajaAbierta->monto_inicial, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- DESGLOSE POR MÉTODO DE PAGO --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="pizzetos-card" style="border-left: 8px solid #16a34a !important; padding: 1.5rem !important;">
                <span class="pizzetos-label">Efectivo (Ventas)</span>
                <div class="text-3xl pizzetos-title text-green-600 italic">${{ number_format($stats['efectivo_ventas'], 2) }}</div>
            </div>
            <div class="pizzetos-card" style="border-left: 8px solid #2563eb !important; padding: 1.5rem !important;">
                <span class="pizzetos-label">Tarjeta</span>
                <div class="text-3xl pizzetos-title text-blue-600 italic">${{ number_format($stats['tarjeta'], 2) }}</div>
            </div>
            <div class="pizzetos-card" style="border-left: 8px solid #9333ea !important; padding: 1.5rem !important;">
                <span class="pizzetos-label">Transferencia</span>
                <div class="text-3xl pizzetos-title text-purple-600 italic">${{ number_format($stats['transferencia'], 2) }}</div>
            </div>
        </div>

        {{-- MÉTRICAS RESUMEN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="pizzetos-card">
                <span class="pizzetos-label">Venta Bruta Total</span>
                <div class="text-3xl pizzetos-title text-slate-900 italic">${{ number_format($stats['venta_total_bruta'], 2) }}</div>
            </div>
            <div class="pizzetos-card">
                <span class="pizzetos-label" style="color: var(--pizzetos-amber) !important;">Tickets Válidos</span>
                <div class="text-4xl pizzetos-title italic" style="color: var(--pizzetos-amber) !important;">{{ $stats['num_ventas'] }}</div>
            </div>
            <div class="pizzetos-card">
                <span class="pizzetos-label text-red-400">Gastos</span>
                <div class="text-3xl pizzetos-title text-red-500 italic">-${{ number_format($stats['total_gastos'], 2) }}</div>
            </div>
            <div class="pizzetos-card" style="border-bottom: 8px solid #10b981 !important;">
                <span class="pizzetos-label text-green-500">Efectivo Real</span>
                <div class="text-4xl pizzetos-title text-green-600 italic">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                {{-- TABLA PEDIDOS DEL TURNO --}}
                <div class="pizzetos-card" style="padding: 0 !important; overflow: hidden;">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="text-2xl pizzetos-title text-slate-800 italic uppercase">Pedidos del Turno</h3>
                        <div style="width: 80px; height: 6px; background: var(--pizzetos-amber); border-radius: 10px;"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left italic">
                            <thead class="bg-slate-50">
                                <tr class="pizzetos-label">
                                    <th class="px-8 py-5">Folio Virtual</th>
                                    <th class="px-8 py-5">Cliente</th>
                                    <th class="px-8 py-5">Métodos</th>
                                    <th class="px-8 py-5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                @foreach($ventas_detalle as $venta)
                                    <tr class="{{ $venta->status == 3 ? 'bg-red-50/50 hover:bg-red-50' : 'hover:bg-slate-50 transition-colors' }}">
                                        <td class="px-8 py-6 font-black {{ $venta->status == 3 ? 'text-red-400 line-through' : 'text-slate-900' }} text-lg">
                                            #{{ $venta->folio_virtual }}
                                        </td>
                                        <td class="px-8 py-6 leading-tight">
                                            <div class="flex flex-col">
                                                <span class="{{ $venta->status == 3 ? 'text-red-600 line-through' : 'text-slate-800' }} font-black uppercase text-sm tracking-tighter">
                                                    {{ $venta->nombre_cliente_formateado }}
                                                </span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[9px] font-bold {{ $venta->status == 3 ? 'text-red-400' : 'text-slate-400' }} uppercase tracking-widest">
                                                        {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('h:i a') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            {{-- LÓGICA BLINDADA PARA LOS PAGOS Y CANCELADOS --}}
                                            @if($venta->status == 3)
                                                <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-red-200">CANCELADO</span>
                                            @elseif(empty($venta->montos_detalle))
                                                <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-slate-200">PENDIENTE</span>
                                            @else
                                                @foreach(explode(' + ', $venta->montos_detalle) as $detalle)
                                                    @php
                                                        $partes = explode(': ', $detalle);
                                                        if(count($partes) < 2) continue; // Si viene roto de la BD, lo ignoramos
                                                        $metodo = $partes[0];
                                                        $monto = $partes[1];
                                                        $clase = str_contains($metodo, 'Efectivo') ? 'bg-efectivo' : (str_contains($metodo, 'Tarjeta') ? 'bg-tarjeta' : 'bg-transf');
                                                    @endphp
                                                    <div class="pay-badge-split w-fit mb-1">
                                                        <span class="pay-method {{ $clase }}">{{ $metodo }}</span>
                                                        <span class="pay-amount">{{ $monto }}</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 text-right font-black text-2xl tracking-tighter italic {{ $venta->status == 3 ? 'text-red-400 line-through' : 'text-slate-900' }}">
                                            ${{ number_format($venta->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TABLA DETALLES DE GASTOS CON RESPONSABLE --}}
                <div class="pizzetos-card" style="padding: 0 !important; overflow: hidden; border-top: 5px solid #ef4444 !important;">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="text-2xl pizzetos-title text-red-600 italic uppercase">Detalles de Gastos</h3>
                        <div style="width: 80px; height: 6px; background: #fca5a5; border-radius: 10px;"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left italic">
                            <thead class="bg-red-50">
                                <tr class="pizzetos-label">
                                    <th class="px-8 py-5">Motivo / Concepto</th>
                                    <th class="px-8 py-5">Responsable</th>
                                    <th class="px-8 py-5 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                @forelse($gastos_detalle as $g)
                                    <tr>
                                        <td class="px-8 py-6 leading-tight">
                                            <div class="flex flex-col">
                                                <span class="text-slate-800 font-black uppercase text-sm tracking-tighter">{{ $g->descripcion }}</span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($g->fecha)->format('h:i a') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-[10px] font-black uppercase italic border border-slate-200">
                                                {{ $g->responsable }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right font-black text-2xl text-red-500 italic">-${{ number_format($g->precio, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-8 py-10 text-center text-slate-400 font-bold uppercase text-[10px] tracking-widest">Sin gastos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR ARQUEO --}}
            <div x-data="{ modal: false, contado: '', esperado: {{ $stats['efectivo_real_en_sobre'] }} }">
                <div class="pizzetos-card" style="border-top: 10px solid var(--pizzetos-amber) !important; background: #fafafa !important;">
                    <h3 class="text-3xl pizzetos-title text-center mb-8 italic">Arqueo Final</h3>
                    <form id="formCerrar" action="{{ route('flujo.caja.cerrar', $cajaAbierta->id_caja) }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="text-center">
                            <label class="pizzetos-label block mb-3 italic font-black">Efectivo Físico Real</label>
                            <input type="number" step="0.01" name="monto_final" x-model="contado" required class="w-full bg-white border-2 border-slate-100 rounded-[30px] py-6 text-center font-black italic text-5xl outline-none tracking-tighter">
                        </div>

                        <div style="background: #fff; padding: 1.5rem; border-radius: 20px; border: 1px solid #f1f5f9;">
                            <div class="flex justify-between pizzetos-label mb-3">
                                <span>Esperado en Efectivo:</span>
                                <span class="text-slate-700">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</span>
                            </div>
                            <div class="flex justify-between pizzetos-title border-t border-slate-100 pt-4 items-center">
                                <span class="text-amber-500 text-[10px]">DIFERENCIA:</span>
                                <span style="font-size: 1.4rem;" :class="(contado - esperado) > 0 ? 'text-green-500' : ((contado - esperado) < 0 ? 'text-red-500' : 'text-slate-700')" x-text="contado === '' ? '$0.00' : '$' + (contado - esperado).toFixed(2)"></span>
                            </div>
                        </div>

                        <button type="button" @click="if(contado !== '') { modal = true } else { alert('Ingresa el monto contado.') }" class="pizzetos-btn w-full shadow-xl shadow-amber-200 py-6 text-xl">
                            Cerrar Caja y Turno
                        </button>
                        
                        <div x-show="modal" x-cloak style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
                            <div class="pizzetos-card" style="max-width: 450px; text-align: center;">
                                <h3 class="text-3xl pizzetos-title text-slate-900 mb-4 italic uppercase">¿Confirmar Cierre?</h3>
                                <p class="text-slate-400 font-bold italic text-[11px] mb-10 uppercase tracking-widest leading-relaxed">Se generará el reporte final y se lanzará el PDF automáticamente.</p>
                                <div class="flex gap-4">
                                    <button @click="modal = false" type="button" class="flex-1 py-5 font-black uppercase italic rounded-2xl bg-slate-100 text-slate-500 text-xs tracking-tighter">Cancelar</button>
                                    <button type="button" @click="document.getElementById('formCerrar').submit()" class="flex-1 py-5 font-black uppercase italic rounded-2xl bg-red-600 text-white text-xs tracking-tighter shadow-lg shadow-red-200">Finalizar Todo</button>
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