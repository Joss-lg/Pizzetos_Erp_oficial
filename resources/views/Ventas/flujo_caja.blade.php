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
        
        // Lanzamos el PDF en un popup controlado automáticamente
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
                    <p class="text-[9px] font-black text-slate-300 uppercase mb-4 tracking-widest italic">¿Buscas cierres anteriores?</p>
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
                <a href="{{ route('flujo.caja.historial') }}" class="pizzetos-card" style="padding: 1rem 1.5rem !important; text-align: center; border: 2px solid #f1f5f9 !important;">
                    <span class="pizzetos-label">Registros</span>
                    <div class="text-xs font-black uppercase italic text-slate-400">Historial</div>
                </a>
                <div class="pizzetos-card" style="min-width: 200px; text-align: center; border-bottom: 8px solid var(--pizzetos-amber) !important; padding: 1.5rem !important;">
                    <span class="pizzetos-label">Fondo de Inicio</span>
                    <div class="text-4xl pizzetos-title text-slate-800 italic">${{ number_format($cajaAbierta->monto_inicial, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- MÉTRICAS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="pizzetos-card">
                <span class="pizzetos-label">Venta Bruta Total</span>
                <div class="text-3xl pizzetos-title text-slate-900 italic">${{ number_format($stats['venta_total_bruta'], 2) }}</div>
            </div>
            <div class="pizzetos-card">
                <span class="pizzetos-label" style="color: var(--pizzetos-amber) !important;">Tickets</span>
                <div class="text-4xl pizzetos-title italic" style="color: var(--pizzetos-amber) !important;">{{ $stats['num_ventas'] }}</div>
            </div>
            <div class="pizzetos-card">
                <span class="pizzetos-label text-red-400">Egresos (Gastos)</span>
                <div class="text-3xl pizzetos-title text-red-500 italic">-${{ number_format($stats['total_gastos'], 2) }}</div>
            </div>
            <div class="pizzetos-card" style="border-bottom: 8px solid #10b981 !important;">
                <span class="pizzetos-label text-green-500">Efectivo Real</span>
                <div class="text-4xl pizzetos-title text-green-600 italic">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="pizzetos-card" style="padding: 0 !important; overflow: hidden;">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="text-2xl pizzetos-title text-slate-800 italic uppercase">Pedidos en Turno</h3>
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
                            <tbody class="divide-y divide-slate-50">
                                @foreach($ventas_detalle as $venta)
                                    <tr class="{{ $venta->status == 3 ? 'opacity-30' : '' }}">
                                        <td class="px-8 py-6 font-black text-slate-900 text-lg">#{{ $venta->folio_virtual }}</td>
                                        <td class="px-8 py-6 leading-tight">
                                            <div class="flex flex-col">
                                                <span class="text-slate-800 font-black uppercase text-sm tracking-tighter">{{ $venta->nombre_cliente_formateado }}</span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('h:i a') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            @foreach(explode(' + ', $venta->montos_detalle ?? '') as $detalle)
                                                @php
                                                    $partes = explode(': ', $detalle);
                                                    $metodo = $partes[0] ?? '';
                                                    $monto = $partes[1] ?? '';
                                                    $clase = str_contains($metodo, 'Efectivo') ? 'bg-efectivo' : (str_contains($metodo, 'Tarjeta') ? 'bg-tarjeta' : 'bg-transf');
                                                @endphp
                                                <div class="pay-badge-split w-fit"><span class="pay-method {{ $clase }}">{{ $metodo }}</span><span class="pay-amount">{{ $monto }}</span></div>
                                            @endforeach
                                        </td>
                                        <td class="px-8 py-6 text-right font-black text-2xl text-slate-900 tracking-tighter italic">
                                            ${{ number_format($venta->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
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
                            <label class="pizzetos-label block mb-3 italic font-black">Efectivo Físico Actual</label>
                            <input type="number" step="0.01" name="monto_final" x-model="contado" required class="w-full bg-white border-2 border-f1f5f9 rounded-[30px] py-6 text-center font-black italic text-5xl outline-none">
                        </div>

                        <div style="background: #fff; padding: 1.5rem; border-radius: 20px; border: 1px solid #f1f5f9;">
                            <div class="flex justify-between pizzetos-label mb-3">
                                <span>Esperado:</span>
                                <span class="text-slate-700">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</span>
                            </div>
                            <div class="flex justify-between pizzetos-title border-t border-slate-100 pt-4 items-center">
                                <span class="text-amber-500 text-[10px]">DIFERENCIA:</span>
                                <span style="font-size: 1.4rem;" :class="(contado - esperado) > 0 ? 'text-green-500' : ((contado - esperado) < 0 ? 'text-red-500' : 'text-slate-700')" x-text="contado === '' ? '$0.00' : '$' + (contado - esperado).toFixed(2)"></span>
                            </div>
                        </div>

                        <button type="button" @click="if(contado !== '') { modal = true } else { alert('Ingresa el monto.') }" class="pizzetos-btn w-full shadow-xl shadow-amber-200 py-6 text-xl">
                            Cerrar Caja y Turno
                        </button>
                        
                        {{-- MODAL DE CONFIRMACIÓN --}}
                        <div x-show="modal" x-cloak style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
                            <div class="pizzetos-card" style="max-width: 450px; text-align: center;">
                                <h3 class="text-3xl pizzetos-title text-slate-900 mb-4 italic uppercase">¿Confirmar Cierre?</h3>
                                <p class="text-slate-400 font-bold italic text-[11px] mb-10 uppercase tracking-widest leading-relaxed">Se generará el reporte final y se lanzará el PDF automáticamente.</p>
                                <div class="flex gap-4">
                                    <button @click="modal = false" type="button" class="flex-1 py-5 font-black uppercase italic rounded-2xl bg-slate-100 text-slate-500 text-xs">Cancelar</button>
                                    <button type="button" @click="document.getElementById('formCerrar').submit()" class="flex-1 py-5 font-black uppercase italic rounded-2xl bg-red-600 text-white text-xs shadow-lg shadow-red-200">Finalizar Todo</button>
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