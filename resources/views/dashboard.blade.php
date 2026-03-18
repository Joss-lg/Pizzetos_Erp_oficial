@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    @if(!$cajaAbierta)
        {{-- ESTADO: CAJA CERRADA --}}
        <div class="w-full flex flex-col items-center justify-center min-h-[75vh]">
            <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 p-12 text-center max-w-lg relative overflow-hidden">
                {{-- Fondo decorativo --}}
                <div class="absolute top-0 right-0 p-8 opacity-[0.03] rotate-12">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/></svg>
                </div>

                <div class="relative z-10">
                    <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h2 class="text-4xl font-black text-slate-900 italic tracking-tighter uppercase leading-none mb-2">Turno Cerrado</h2>
                    <p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest italic mb-8">Abre la caja para ver las métricas en tiempo real</p>
                    
                    {{-- Pon aquí la ruta real de tu POS --}}
                    <a href="/venta/pos" class="inline-block w-full bg-amber-400 hover:bg-amber-500 text-slate-900 font-black py-4 rounded-2xl shadow-lg shadow-amber-100 uppercase italic tracking-tighter transition-all active:scale-95">
                        Ir al Punto de Venta
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- ESTADO: CAJA ABIERTA (TU DASHBOARD ORIGINAL) --}}
        
        {{-- HEADER COMPACTO --}}
        <div class="flex justify-between items-center bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase leading-none">
                    Resumen de Hoy <span class="text-amber-400">.</span>
                </h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic mt-1">
                    {{ now()->format('d M, Y | h:i A') }}
                </p>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <span class="block text-[9px] font-black text-slate-300 uppercase italic leading-none mb-1">Tickets hoy</span>
                    <span class="text-2xl font-black text-slate-800 italic leading-none">{{ $numVentas }}</span>
                </div>
                <div class="bg-amber-400 p-3 rounded-2xl shadow-lg shadow-amber-100 text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>

        {{-- GRID PRINCIPAL --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            {{-- CARD: BALANCE EFECTIVO --}}
            <div class="md:col-span-8 bg-white rounded-[3.5rem] p-10 border-4 border-amber-400/20 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 p-12 opacity-[0.03] rotate-12">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/></svg>
                </div>
                
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <span class="text-slate-400 font-black text-[10px] uppercase tracking-[0.4em] italic mb-2 block">Balance Efectivo en Caja</span>
                        <h2 class="text-7xl font-black text-slate-900 italic tracking-tighter leading-none">
                            ${{ number_format($efectivoCaja, 2) }}
                        </h2>
                    </div>
                    
                    <div class="mt-12 flex items-center gap-6">
                        <div class="bg-emerald-50 p-5 rounded-[2rem] border border-emerald-100">
                            <span class="block text-[9px] font-black text-emerald-600 uppercase italic mb-1">Entradas</span>
                            <span class="text-2xl font-black text-slate-800 italic">${{ number_format($efectivoVentas, 2) }}</span>
                        </div>
                        <div class="text-slate-300 font-black text-2xl italic">-</div>
                        <div class="bg-red-50 p-5 rounded-[2rem] border border-red-100">
                            <span class="block text-[9px] font-black text-red-500 uppercase italic mb-1">Salidas</span>
                            <span class="text-2xl font-black text-slate-800 italic">-${{ number_format($gastosHoy, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD: VENTA BRUTA --}}
            <div class="md:col-span-4 bg-white rounded-[3.5rem] p-10 border border-slate-100 shadow-sm flex flex-col justify-center text-center">
                <span class="text-[10px] font-black uppercase text-slate-400 italic tracking-[0.2em] mb-4">Venta Total Bruta</span>
                <h2 class="text-5xl font-black text-slate-900 italic tracking-tighter leading-none mb-6">
                    ${{ number_format($ventasHoy, 2) }}
                </h2>
                <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-400" style="width: 100%"></div>
                </div>
            </div>

            {{-- MÉTODOS DE PAGO --}}
            <div class="md:col-span-4 bg-emerald-500 rounded-[2.5rem] p-8 text-white shadow-xl shadow-emerald-100 flex items-center justify-between group transition-all hover:-translate-y-1">
                <div>
                    <span class="text-[9px] font-black uppercase italic opacity-70 tracking-widest block mb-1">Efectivo</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($efectivoVentas, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 1.13-1.12 1.92-2.8 1.92-1.98 0-2.85-.93-3-2.3H4.21c.15 2.11 1.74 3.41 3.79 3.91V21h3v-2.15c2.02-.42 3.51-1.6 3.51-3.66 0-2.35-1.9-3.61-4.71-4.29z"/></svg>
                </div>
            </div>

            <div class="md:col-span-4 bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-100 flex items-center justify-between group transition-all hover:-translate-y-1">
                <div>
                    <span class="text-[9px] font-black uppercase italic opacity-70 tracking-widest block mb-1">Tarjeta</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($tarjetasHoy, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                </div>
            </div>

            <div class="md:col-span-4 bg-purple-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-purple-100 flex items-center justify-between group transition-all hover:-translate-y-1">
                <div>
                    <span class="text-[9px] font-black uppercase italic opacity-70 tracking-widest block mb-1">Transferencia</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($transferenciasHoy, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V9h2v7zm4 0h-2V7h2v9z"/></svg>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection