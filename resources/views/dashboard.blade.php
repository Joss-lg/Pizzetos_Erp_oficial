@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .app-dark .dashboard-board {
        background: #182235 !important;
        border-color: #2f3c53 !important;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.18) !important;
    }

    .app-dark .dashboard-board-soft {
        background: #131c2e !important;
        border-color: #2f3c53 !important;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16) !important;
    }

    .app-dark .dashboard-chip-green {
        background: rgba(34, 197, 94, 0.14) !important;
        border-color: rgba(34, 197, 94, 0.2) !important;
    }

    .app-dark .dashboard-chip-red {
        background: rgba(239, 68, 68, 0.14) !important;
        border-color: rgba(239, 68, 68, 0.2) !important;
    }

    .app-dark .dashboard-pay-efectivo {
        background: linear-gradient(135deg, #14532d, #166534) !important;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.18) !important;
    }

    .app-dark .dashboard-pay-tarjeta {
        background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.18) !important;
    }

    .app-dark .dashboard-pay-transferencia {
        background: linear-gradient(135deg, #6d28d9, #7c3aed) !important;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.18) !important;
    }

    /* ── Top productos ── */
    .dash-rank-card { background:#fff; border-radius:2.5rem; padding:2rem; border:1px solid rgba(226,232,240,.8); box-shadow:0 2px 8px rgba(0,0,0,.04); }
    .app-dark .dash-rank-card { background:#182235!important; border-color:#2f3c53!important; }

    .dash-pizza-spotlight { background:linear-gradient(135deg,#fd7e14,#ffc107); border-radius:1.5rem; padding:1.25rem 1.5rem; color:#fff; }

    .dash-rank-bar-track { height:6px; border-radius:999px; background:rgba(226,232,240,.6); overflow:hidden; }
    .app-dark .dash-rank-bar-track { background:rgba(255,255,255,.08)!important; }
    .dash-rank-bar-fill { height:100%; border-radius:999px; transition:width .4s; }

    .dash-cat-badge { font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; padding:2px 8px; border-radius:999px; }
    .dash-cat-pizza        { background:rgba(253,126,20,.14); color:#ea580c; }
    .dash-cat-hamburguesa  { background:rgba(239,68,68,.12);  color:#dc2626; }
    .dash-cat-alitas       { background:rgba(245,158,11,.14); color:#d97706; }
    .dash-cat-costillas    { background:rgba(239,68,68,.12);  color:#dc2626; }
    .dash-cat-spaguetti    { background:rgba(99,102,241,.12); color:#4f46e5; }
    .dash-cat-papas        { background:rgba(234,179,8,.14);  color:#ca8a04; }
    .app-dark .dash-cat-pizza       { background:rgba(253,126,20,.18)!important; color:#fb923c!important; }
    .app-dark .dash-cat-hamburguesa { background:rgba(239,68,68,.18)!important;  color:#f87171!important; }
    .app-dark .dash-cat-alitas      { background:rgba(245,158,11,.18)!important; color:#fbbf24!important; }
    .app-dark .dash-cat-costillas   { background:rgba(239,68,68,.18)!important;  color:#f87171!important; }
    .app-dark .dash-cat-spaguetti   { background:rgba(99,102,241,.18)!important; color:#818cf8!important; }
    .app-dark .dash-cat-papas       { background:rgba(234,179,8,.18)!important;  color:#fde047!important; }
</style>
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    @if(!$cajaAbierta)
        {{-- ESTADO: CAJA CERRADA --}}
        <div class="w-full flex flex-col items-center justify-center min-h-[75vh]">
            <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 p-12 text-center max-w-lg relative overflow-hidden dashboard-board">
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
        <div class="flex justify-between items-center bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm dashboard-board">
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
            
            {{-- MÉTODOS DE PAGO --}}
            <div class="md:col-span-4 bg-emerald-500 rounded-[2.5rem] p-8 text-white shadow-xl shadow-emerald-100 flex items-center justify-between group transition-all hover:-translate-y-1 dashboard-pay-efectivo">
                <div>
                    <span class="text-[9px] font-black uppercase italic opacity-70 tracking-widest block mb-1">Efectivo</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($efectivoVentas, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 1.13-1.12 1.92-2.8 1.92-1.98 0-2.85-.93-3-2.3H4.21c.15 2.11 1.74 3.41 3.79 3.91V21h3v-2.15c2.02-.42 3.51-1.6 3.51-3.66 0-2.35-1.9-3.61-4.71-4.29z"/></svg>
                </div>
            </div>

            <div class="md:col-span-4 bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-100 flex items-center justify-between group transition-all hover:-translate-y-1 dashboard-pay-tarjeta">
                <div>
                    <span class="text-[9px] font-black uppercase italic opacity-70 tracking-widest block mb-1">Tarjeta</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($tarjetasHoy, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                </div>
            </div>

            <div class="md:col-span-4 bg-purple-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-purple-100 flex items-center justify-between group transition-all hover:-translate-y-1 dashboard-pay-transferencia">
                <div>
                    <span class="text-[9px] font-black uppercase italic opacity-70 tracking-widest block mb-1">Transferencia</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($transferenciasHoy, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V9h2v7zm4 0h-2V7h2v9z"/></svg>
                </div>
            </div>

        </div>

        {{-- ── RANKINGS ── --}}
        @php
            $maxPizza   = $topPizzas->max('total') ?: 1;
            $maxProd    = $topProductos->max('total') ?: 1;
            $catClasses = [
                'Pizza'       => 'dash-cat-pizza',
                'Hamburguesa' => 'dash-cat-hamburguesa',
                'Alitas'      => 'dash-cat-alitas',
                'Costillas'   => 'dash-cat-costillas',
                'Spaguetti'   => 'dash-cat-spaguetti',
                'Papas'       => 'dash-cat-papas',
            ];
            $catColors = [
                'Pizza'       => '#fd7e14',
                'Hamburguesa' => '#ef4444',
                'Alitas'      => '#f59e0b',
                'Costillas'   => '#ef4444',
                'Spaguetti'   => '#6366f1',
                'Papas'       => '#eab308',
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- TOP PIZZAS --}}
            <div class="dash-rank-card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#fd7e14] to-[#ffc107] flex items-center justify-center shadow-md text-xl shrink-0">🍕</div>
                    <div>
                        <h3 class="text-[13px] font-black text-slate-900 italic uppercase tracking-tight leading-none">Pizzas más vendidas</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Histórico — todas las ventas</p>
                    </div>
                </div>

                @if($topPizzas->isEmpty())
                    <div class="flex flex-col items-center py-8 gap-2 text-slate-400">
                        <span class="text-4xl opacity-30">🍕</span>
                        <p class="text-xs font-bold italic">Sin datos aún</p>
                    </div>
                @else
                    {{-- Spotlight #1 --}}
                    @php $p1 = $topPizzas->first(); @endphp
                    <div class="dash-pizza-spotlight flex items-center justify-between mb-4">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest opacity-80 block mb-0.5">🥇 La más vendida</span>
                            <h4 class="text-xl font-black italic tracking-tight leading-none">{{ $p1->nombre }}</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black italic leading-none">{{ $p1->total }}</span>
                            <span class="text-[10px] font-bold opacity-80 block">unidades</span>
                        </div>
                    </div>

                    {{-- Ranks 2-5 --}}
                    <div class="space-y-3">
                        @foreach($topPizzas->skip(1) as $i => $p)
                            @php $pct = round(($p->total / $maxPizza) * 100); @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-black text-slate-400 w-4 text-center">{{ $i + 2 }}</span>
                                        <span class="text-[13px] font-bold text-slate-800 italic truncate max-w-[180px]">{{ $p->nombre }}</span>
                                    </div>
                                    <span class="text-[12px] font-black text-slate-600 italic shrink-0">{{ $p->total }}</span>
                                </div>
                                <div class="dash-rank-bar-track ml-6">
                                    <div class="dash-rank-bar-fill bg-gradient-to-r from-[#fd7e14] to-[#ffc107]" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- TOP PRODUCTOS (todas las categorías) --}}
            <div class="dash-rank-card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center shadow-md text-xl shrink-0">🏆</div>
                    <div>
                        <h3 class="text-[13px] font-black text-slate-900 italic uppercase tracking-tight leading-none">Top productos</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Todas las categorías — histórico</p>
                    </div>
                </div>

                @if($topProductos->isEmpty())
                    <div class="flex flex-col items-center py-8 gap-2 text-slate-400">
                        <span class="text-4xl opacity-30">📊</span>
                        <p class="text-xs font-bold italic">Sin datos aún</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topProductos as $i => $prod)
                            @php
                                $pct      = round(($prod->total / $maxProd) * 100);
                                $catKey   = $prod->categoria ?? 'Pizza';
                                $badgeCls = $catClasses[$catKey] ?? 'dash-cat-pizza';
                                $barColor = $catColors[$catKey] ?? '#fd7e14';
                                $medals   = ['🥇','🥈','🥉'];
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-[13px] shrink-0">{{ $medals[$i] ?? ($i+1) . '.' }}</span>
                                        <span class="text-[13px] font-bold text-slate-800 italic truncate">{{ $prod->nombre }}</span>
                                        <span class="dash-cat-badge {{ $badgeCls }} shrink-0">{{ $catKey }}</span>
                                    </div>
                                    <span class="text-[12px] font-black text-slate-600 italic shrink-0 ml-2">{{ $prod->total }}</span>
                                </div>
                                <div class="dash-rank-bar-track">
                                    <div class="dash-rank-bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>{{-- /rankings --}}

    @endif
</div>
@endsection