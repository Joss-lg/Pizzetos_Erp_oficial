@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* ── Contenedor del mapa ── */
.mapa-wrap {
    position: relative;
    width: 100%;
    padding-bottom: 84%;
    background: #0d1117;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.07);
    background-image:
        linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size: 40px 40px;
}
html:not(.app-dark) .mapa-wrap {
    background-color: #1a2235;
    background-image:
        linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
}

/* ── Tarjeta de mesa ── */
.mesa-card {
    position: absolute;
    transform: translate(-50%, -50%);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 4px; border-radius: 14px;
    border: 2px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.05);
    cursor: pointer; transition: all .2s; user-select: none;
    backdrop-filter: blur(4px); padding: 6px 8px;
}
.mesa-card:hover { transform: translate(-50%, -50%) scale(1.06); border-color: rgba(253,126,20,.5); background: rgba(253,126,20,.08); box-shadow: 0 0 20px rgba(253,126,20,.15); z-index: 10; }
.mesa-card.ocupada { border-color: rgba(253,126,20,.55); background: rgba(253,126,20,.1); box-shadow: 0 0 16px rgba(253,126,20,.2); }
.mesa-card.ocupada:hover { border-color: #fd7e14; background: rgba(253,126,20,.18); box-shadow: 0 0 28px rgba(253,126,20,.35); }
.mesa-card.libre { border-color: rgba(74,222,128,.2); background: rgba(74,222,128,.04); }
.mesa-card.libre:hover { border-color: rgba(74,222,128,.5); background: rgba(74,222,128,.1); box-shadow: 0 0 20px rgba(74,222,128,.15); }

/* ── Tamaños ── */
.mesa-portrait  { width: 7.2%; height: 0; padding-bottom: 15.5%; }
.mesa-landscape { width: 14%;  height: 0; padding-bottom: 7.5%; }
.mesa-wide      { width: 17%;  height: 0; padding-bottom: 8%; }
.mesa-tall      { width: 7.8%; height: 0; padding-bottom: 17%; }
.mesa-inner { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; padding: 6px 4px; }
.mesa-num  { font-size: clamp(10px,1.1vw,15px); font-weight:900; color:#e2e8f0; letter-spacing:.02em; line-height:1; }
.mesa-clie { font-size: clamp(7px,.8vw,10px); font-weight:700; color:rgba(253,126,20,.85); line-height:1.1; text-align:center; max-width:90%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mesa-total{ font-size: clamp(8px,.9vw,12px); font-weight:900; color:#fbbf24; line-height:1; }
.mesa-time { font-size: clamp(6px,.7vw,9px); font-weight:600; color:rgba(255,255,255,.35); line-height:1; }
.mesa-dot  { width: clamp(5px,.5vw,7px); height: clamp(5px,.5vw,7px); border-radius:50%; margin-bottom:2px; }

/* ── Modal comanda (dark card) ── */
.comanda-modal-bg { position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem; }
.comanda-modal    { background:#182235;border:1px solid rgba(255,255,255,.1);border-radius:24px;width:100%;max-width:440px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.5); }
html:not(.app-dark) .comanda-modal { background:#fff;border-color:rgba(0,0,0,.1); box-shadow:0 24px 60px rgba(0,0,0,.18); }
/* Light mode text overrides */
html:not(.app-dark) .cm-title    { color: #111827 !important; }
html:not(.app-dark) .cm-sub      { color: #6b7280 !important; }
html:not(.app-dark) .cm-divider  { border-color: rgba(0,0,0,.08) !important; }
html:not(.app-dark) .cm-close    { background: rgba(0,0,0,.06) !important; }
html:not(.app-dark) .cm-close:hover { background: rgba(0,0,0,.12) !important; }
html:not(.app-dark) .cm-close svg { color: #6b7280 !important; }
html:not(.app-dark) .cm-items    { background: rgba(0,0,0,.04) !important; }
html:not(.app-dark) .cm-item-name { color: #374151 !important; }
html:not(.app-dark) .cm-item-price{ color: #6b7280 !important; }
html:not(.app-dark) .cm-loading  { color: #9ca3af !important; }
html:not(.app-dark) .cm-empty    { color: #9ca3af !important; }

/* ══ Reusar estilos del POS para los cards de pago ══ */
.pos-pago-card {
    border: 1.5px solid rgba(226,232,240,0.5);
    border-radius: 14px; overflow: hidden; transition: all 0.18s; background: white;
}
.pos-pago-card:hover { border-color: rgba(40,167,69,0.35); }
.pos-pago-card-on {
    border-color: #28a745 !important;
    background: rgba(40,167,69,0.05) !important;
    box-shadow: 0 0 0 3px rgba(40,167,69,0.08) !important;
}
.app-dark .pos-pago-card { background: rgba(255,255,255,0.05) !important; border-color: rgba(255,255,255,0.1) !important; }
.app-dark .pos-pago-card:hover { border-color: rgba(40,167,69,0.45) !important; }
.app-dark .pos-pago-card-on { border-color: #28a745 !important; background: rgba(40,167,69,0.14) !important; box-shadow: 0 0 0 3px rgba(40,167,69,0.14) !important; }
.pos-pago-icon-wrap { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
.pos-pago-green  { background: linear-gradient(135deg, #28a745, #20c997); }
.pos-pago-cyan   { background: linear-gradient(135deg, #17a2b8, #0dcaf0); }
.pos-pago-purple { background: linear-gradient(135deg, #6f42c1, #9d6fe8); }
.pos-pago-label { font-size:14px; font-weight:700; color:#374151; }
.app-dark .pos-pago-label { color:#e2e8f0 !important; }
.pos-pago-check-circle { width:22px; height:22px; border-radius:50%; border:2px solid rgba(203,213,225,0.6); display:flex; align-items:center; justify-content:center; transition:all 0.18s; }
.pos-pago-check-on { background:#28a745 !important; border-color:#28a745 !important; }
.app-dark .pos-pago-check-circle { border-color:rgba(255,255,255,0.22); }
.pos-pago-input { border:1px solid rgba(226,232,240,0.6); border-radius:8px; padding:7px 10px; font-size:13px; outline:none; transition:border-color 0.15s; background:rgba(255,255,255,0.8); color:#374151; width:100%; }
.pos-pago-input:focus { border-color:#28a745; box-shadow:0 0 0 2px rgba(40,167,69,0.12); }
.app-dark .pos-pago-input { background:rgba(255,255,255,0.08) !important; border-color:rgba(255,255,255,0.15) !important; color:#f1f5f9 !important; }
.app-dark .pos-pago-input::placeholder { color:#64748b !important; }
.app-dark .pos-pago-input:focus { border-color:#28a745 !important; }
.pos-discount-toggle { background: #f1f5f9; border-radius: 10px; padding: 5px; }
.app-dark .pos-discount-toggle { background: rgba(255,255,255,0.07) !important; }
.app-dark .pos-discount-toggle button { color: #94a3b8; }
/* modal pago footer dark */
.pago-footer { background:#f9fafb; border-top:1px solid #f1f5f9; }
.app-dark .pago-footer { background:rgba(0,0,0,.2) !important; border-color:rgba(255,255,255,.08) !important; }
/* total box dark */
.total-box { background:#f8fafc; border:1px solid #f1f5f9; border-radius:14px; }
.app-dark .total-box { background:rgba(255,255,255,.05) !important; border-color:rgba(255,255,255,.08) !important; }
/* falta cobrar */
.falta-box { border-radius:12px; border:1px solid; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; }
</style>

@php
    $mesasOcupadas = collect($mesasOcupadas ?? []);
    $csrfToken = csrf_token();
    $esAdmin = auth()->check() && auth()->user()->id_ca == 1;

    $layout = [
         1 => [20.8, 10.2, 'mesa-portrait',  0],
         2 => [41.2, 10.2, 'mesa-portrait',  0],
         3 => [62.6, 10.0, 'mesa-portrait',  0],
         4 => [76.8, 32.6, 'mesa-portrait', -15],
         5 => [80.2, 52.3, 'mesa-landscape', 0],
         6 => [30.7, 29.8, 'mesa-portrait',  0],
         7 => [51.9, 29.8, 'mesa-portrait',  0],
         8 => [51.9, 52.9, 'mesa-portrait',  0],
         9 => [51.9, 73.3, 'mesa-portrait',  0],
        10 => [51.7, 92.4, 'mesa-wide',      0],
        11 => [ 5.7, 36.6, 'mesa-tall',      0],
    ];
@endphp

<div x-data="mesasApp()" x-init="init()" class="max-w-6xl mx-auto pb-10">

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#fd7e14] to-[#ffc107] flex items-center justify-center shadow-md text-xl shrink-0">🍕</div>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none">Mapa de Mesas</h1>
                <p class="text-[11px] text-gray-400 font-bold mt-0.5 uppercase tracking-widest">Pizzetos · Piso único</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-4 text-[11px] font-bold text-gray-500 uppercase tracking-wide">
                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-green-400 opacity-70"></span>Libre</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-orange-400"></span>Ocupada</span>
            </div>
            <div class="text-[11px] font-black text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full">
                <span x-text="ocupadas"></span>/11 ocupadas
            </div>
            <button @click="refrescar()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors" title="Actualizar">
                <svg class="w-4 h-4 text-gray-500" :class="cargando ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
            <button @click="confirmarCierreAnteriores()"
                    :disabled="cierreLoading"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 font-bold text-[11px] uppercase tracking-wide transition-colors disabled:opacity-50"
                    title="Cerrar mesas abiertas de días anteriores">
                <svg class="w-3.5 h-3.5 shrink-0" :class="cierreLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span x-text="cierreLoading ? 'Cerrando...' : 'Cerrar anteriores'"></span>
            </button>
        </div>
    </div>

    {{-- Toast notificación cierre --}}
    <div x-show="cierreToast" x-cloak x-transition
         :class="cierreToastType === 'success' ? 'bg-green-600 text-white' : (cierreToastType === 'info' ? 'bg-blue-600 text-white' : 'bg-red-600 text-white')"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-[200] px-5 py-3 rounded-2xl shadow-xl font-bold text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <template x-if="cierreToastType === 'success'"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></template>
            <template x-if="cierreToastType !== 'success'"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></template>
        </svg>
        <span x-text="cierreToast"></span>
    </div>

    {{-- Mapa --}}
    <div class="mapa-wrap">
        @foreach($layout as $num => [$left, $top, $size, $rot])
            @php
                $ocup  = isset($mesasOcupadas[$num]);
                $style = "left:{$left}%;top:{$top}%;" . ($rot ? "transform:translate(-50%,-50%) rotate({$rot}deg);" : '');
            @endphp
            <div class="mesa-card {{ $size }} {{ $ocup ? 'ocupada' : 'libre' }}"
                 style="{{ $style }}"
                 :class="estado[{{ $num }}] ? 'ocupada' : 'libre'"
                 @click="abrirMesa({{ $num }})">
                <div class="mesa-inner">
                    <div class="mesa-dot" :class="estado[{{ $num }}] ? 'bg-orange-400' : 'bg-green-400 opacity-60'"></div>
                    <span class="mesa-num">Mesa {{ $num }}</span>
                    <template x-if="estado[{{ $num }}]">
                        <span class="mesa-clie" x-text="estado[{{ $num }}].nombreClie || '—'"></span>
                    </template>
                    <template x-if="estado[{{ $num }}]">
                        <span class="mesa-total" x-text="'$' + parseFloat(estado[{{ $num }}].total || 0).toFixed(2)"></span>
                    </template>
                    <template x-if="estado[{{ $num }}]">
                        <span class="mesa-time" x-text="tiempoTranscurrido(estado[{{ $num }}].fecha_hora)"></span>
                    </template>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════
         MODAL COMANDA
    ══════════════════════════════════════════ --}}
    <div x-show="modalAbierto && !modalPago" x-cloak class="comanda-modal-bg" @click.self="cerrarModal()">
        <div class="comanda-modal">
            <div class="cm-divider p-5 border-b border-white/[.07] flex justify-between items-center">
                <div>
                    <h2 class="cm-title text-lg font-black text-gray-100 tracking-tight leading-none">Mesa <span x-text="mesaActual"></span></h2>
                    <p class="cm-sub text-[11px] text-gray-400 mt-0.5" x-text="comanda.nombreClie ? 'Cliente: ' + comanda.nombreClie : 'Sin nombre asignado'"></p>
                </div>
                <button @click="cerrarModal()" class="cm-close w-8 h-8 rounded-xl bg-white/[.07] hover:bg-white/[.12] flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-5">
                {{-- Mesa libre --}}
                <template x-if="!comanda.id_venta">
                    <div class="flex flex-col items-center py-8 gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-green-500/10 flex items-center justify-center">
                            <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="font-black text-gray-300 text-sm">Mesa disponible</p>
                        <a :href="'/venta/pos?mesa=' + mesaActual"
                           class="mt-2 w-full bg-gradient-to-r from-[#fd7e14] to-[#ffc107] text-white font-black rounded-xl py-3 text-sm text-center transition-all hover:brightness-110">
                            Abrir nueva orden
                        </a>
                    </div>
                </template>

                {{-- Mesa ocupada --}}
                <template x-if="comanda.id_venta">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex gap-3">
                                <div class="text-center">
                                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Tiempo</p>
                                    <p class="text-[15px] font-black text-orange-400" x-text="tiempoTranscurrido(comanda.fecha_hora)"></p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Total</p>
                                    <p class="text-[15px] font-black text-amber-400" x-text="'$' + parseFloat(comanda.total || 0).toFixed(2)"></p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black px-3 py-1 rounded-full bg-orange-400/15 text-orange-400 uppercase tracking-wide">Ocupada</span>
                        </div>
                        <div class="cm-items bg-black/20 rounded-xl p-3 mb-4 max-h-48 overflow-y-auto space-y-2">
                            <template x-if="cargandoItems">
                                <div class="cm-loading flex items-center justify-center py-4 gap-2 text-gray-500 text-xs font-bold">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Cargando orden...
                                </div>
                            </template>
                            <template x-if="!cargandoItems && comanda.items && comanda.items.length === 0">
                                <p class="cm-empty text-center text-xs text-gray-500 py-4">Sin productos registrados aún</p>
                            </template>
                            <template x-for="item in comanda.items" :key="item.nombre + item.cantidad">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-black text-orange-400 w-5 text-center" x-text="'×' + item.cantidad"></span>
                                        <span class="cm-item-name text-[13px] font-bold text-gray-200" x-text="item.nombre"></span>
                                    </div>
                                    <span class="cm-item-price text-[12px] font-black text-gray-400" x-text="item.precio ? '$' + parseFloat(item.precio * item.cantidad).toFixed(2) : ''"></span>
                                </div>
                            </template>
                        </div>
                        <div class="flex gap-2">
                            <a :href="'/venta/pos?edit=' + comanda.id_venta + '&from=mesas'"
                               class="flex-1 bg-gradient-to-r from-[#fd7e14] to-[#ffc107] text-white font-black rounded-xl py-3 text-sm text-center flex items-center justify-center gap-2 hover:brightness-110 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                Agregar
                            </a>
                            <button @click="abrirPago()"
                               class="flex-1 bg-[#28a745] hover:bg-[#218838] text-white font-black rounded-xl py-3 text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-green-900/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                                Cobrar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL COBRAR PEDIDO (igual al POS)
    ══════════════════════════════════════════ --}}
    <div x-show="modalPago" x-cloak class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-[450px] flex flex-col overflow-hidden" @click.away="modalPago = false">

            {{-- Header verde --}}
            <div class="bg-[#28a745] p-5 flex justify-between items-center text-white">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Mesa <span x-text="mesaActual"></span></p>
                    <h2 class="text-xl font-black uppercase italic tracking-wider">Cobrar Pedido</h2>
                </div>
                <button @click="modalPago = false" class="hover:text-green-200 font-black text-2xl leading-none">&times;</button>
            </div>

            <div class="p-6 bg-white space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Total --}}
                <div class="total-box text-center p-4 relative">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Total a Cobrar</p>
                    <p class="text-4xl font-black text-[#28a745]" x-text="'$' + getTotal().toFixed(2)"></p>
                    <div x-show="cortesia > 0" class="absolute top-2 right-2 bg-red-100 text-red-600 text-[10px] font-black px-2 py-1 rounded uppercase">
                        - <span x-text="cortesia"></span>% Desc.
                    </div>
                </div>

                {{-- Toggle descuentos --}}
                <div class="pos-discount-toggle flex gap-2">
                    <button @click="cortesia = 0; autoFill()" :class="cortesia === 0 ? 'bg-slate-700 text-white shadow-sm' : 'text-gray-400 hover:text-gray-200'"
                            class="flex-1 py-2 rounded-lg font-black text-[11px] tracking-wide transition-all">Sin Desc.</button>
                    <button @click="pedirCortesia(40)" :class="cortesia === 40 ? 'bg-blue-600 text-white shadow-sm shadow-blue-900/40' : 'text-gray-400 hover:text-gray-200'"
                            class="flex-1 py-2 rounded-lg font-black text-[11px] tracking-wide transition-all">40% Empl.</button>
                    <button @click="pedirCortesia(100)" :class="cortesia === 100 ? 'bg-red-600 text-white shadow-sm shadow-red-900/40' : 'text-gray-400 hover:text-gray-200'"
                            class="flex-1 py-2 rounded-lg font-black text-[11px] tracking-wide transition-all">100% Cort.</button>
                </div>

                {{-- Métodos de pago --}}
                <div x-show="cortesia !== 100" class="space-y-3 pt-1">
                    <label class="block text-[11px] font-black text-gray-600 uppercase mb-2">Métodos de Pago</label>

                    {{-- Efectivo --}}
                    <label class="pos-pago-card block cursor-pointer select-none" :class="pagos.efectivo.activo ? 'pos-pago-card-on' : ''">
                        <input type="checkbox" x-model="pagos.efectivo.activo" @change="autoFillPago('efectivo')" class="sr-only">
                        <div class="flex items-center gap-3 p-3.5">
                            <div class="pos-pago-icon-wrap pos-pago-green shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <span class="pos-pago-label flex-1">Efectivo</span>
                            <div class="pos-pago-check-circle" :class="pagos.efectivo.activo ? 'pos-pago-check-on' : ''">
                                <svg x-show="pagos.efectivo.activo" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div x-show="pagos.efectivo.activo" x-transition class="px-3.5 pb-3.5 flex gap-2">
                            <input type="number" step="0.1" min="0" x-model.number="pagos.efectivo.monto" placeholder="Monto $" class="pos-pago-input w-1/2">
                            <input type="number" step="0.1" min="0" x-model.number="pagos.efectivo.entregado" placeholder="Recibido $" class="pos-pago-input w-1/2">
                        </div>
                    </label>

                    {{-- Tarjeta --}}
                    <label class="pos-pago-card block cursor-pointer select-none" :class="pagos.tarjeta.activo ? 'pos-pago-card-on' : ''">
                        <input type="checkbox" x-model="pagos.tarjeta.activo" @change="autoFillPago('tarjeta')" class="sr-only">
                        <div class="flex items-center gap-3 p-3.5">
                            <div class="pos-pago-icon-wrap pos-pago-cyan shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <span class="pos-pago-label flex-1">Tarjeta</span>
                            <div class="pos-pago-check-circle" :class="pagos.tarjeta.activo ? 'pos-pago-check-on' : ''">
                                <svg x-show="pagos.tarjeta.activo" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div x-show="pagos.tarjeta.activo" x-transition class="px-3.5 pb-3.5">
                            <input type="number" step="0.1" min="0" x-model.number="pagos.tarjeta.monto" placeholder="Monto $" class="pos-pago-input w-full">
                        </div>
                    </label>

                    {{-- Transferencia --}}
                    <label class="pos-pago-card block cursor-pointer select-none" :class="pagos.transferencia.activo ? 'pos-pago-card-on' : ''">
                        <input type="checkbox" x-model="pagos.transferencia.activo" @change="autoFillPago('transferencia')" class="sr-only">
                        <div class="flex items-center gap-3 p-3.5">
                            <div class="pos-pago-icon-wrap pos-pago-purple shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="pos-pago-label flex-1">Transferencia</span>
                            <div class="pos-pago-check-circle" :class="pagos.transferencia.activo ? 'pos-pago-check-on' : ''">
                                <svg x-show="pagos.transferencia.activo" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div x-show="pagos.transferencia.activo" x-transition class="px-3.5 pb-3.5 flex gap-2">
                            <input type="number" step="0.1" min="0" x-model.number="pagos.transferencia.monto" placeholder="Monto $" class="pos-pago-input w-1/2">
                            <input type="text" x-model="pagos.transferencia.referencia" placeholder="Referencia" class="pos-pago-input w-1/2">
                        </div>
                    </label>
                </div>

                {{-- Falta cobrar --}}
                <div x-show="cortesia !== 100"
                     class="falta-box"
                     :class="faltaPagar() === 0 ? 'bg-green-50 border-green-200' : (faltaPagar() < 0 ? 'bg-red-50 border-red-200' : 'bg-orange-50 border-orange-200')">
                    <span class="text-[11px] font-black uppercase"
                          :class="faltaPagar() === 0 ? 'text-green-700' : (faltaPagar() < 0 ? 'text-red-700' : 'text-orange-700')"
                          x-text="faltaPagar() > 0 ? 'Falta Cobrar:' : (faltaPagar() < 0 ? 'Excede:' : 'Completado')"></span>
                    <span class="text-xl font-black"
                          :class="faltaPagar() === 0 ? 'text-green-700' : (faltaPagar() < 0 ? 'text-red-700' : 'text-orange-700')"
                          x-text="Math.abs(faltaPagar()).toFixed(2)"></span>
                </div>

                {{-- Error --}}
                <template x-if="cobrarError">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-[12px] font-bold text-red-600" x-text="cobrarError"></div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="pago-footer p-5 flex gap-3">
                <button @click="modalPago = false" class="flex-1 font-bold text-gray-500 bg-white border border-gray-200 rounded-xl py-3 hover:bg-gray-100 transition-colors">
                    Volver
                </button>
                <button @click="cobrarOrden()"
                        :disabled="!pagosValidos() || cobrarLoading"
                        :class="!pagosValidos() || cobrarLoading ? 'bg-[#ced4da] text-gray-500 cursor-not-allowed' : 'bg-[#28a745] hover:bg-[#218838] text-white shadow-lg shadow-green-900/30'"
                        class="flex-[2] font-black rounded-xl py-3.5 uppercase italic disabled:opacity-60 transition-all text-[13px] tracking-wide">
                    <span x-show="!cobrarLoading">Confirmar Pago</span>
                    <span x-show="cobrarLoading">Procesando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL CONTRASEÑA ADMIN (descuentos)
    ══════════════════════════════════════════ --}}
    <div x-show="modalCortesia" x-cloak class="fixed inset-0 bg-black/60 z-[150] flex items-center justify-center p-4 backdrop-blur-sm"
         @keydown.enter.window="if(modalCortesia) confirmarCortesia()">
        <div class="bg-white rounded-2xl shadow-2xl w-[380px]" @click.away="modalCortesia = false; _pendingCortesia = null;">
            <div class="bg-amber-500 p-5 rounded-t-2xl flex justify-between items-center text-white">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Autorización Requerida</p>
                    <h3 class="font-black text-[18px] uppercase italic tracking-wide leading-tight">Aplicar Descuento</h3>
                </div>
                <button @click="modalCortesia = false; _pendingCortesia = null;" class="font-black text-2xl leading-none hover:text-amber-200 transition-colors">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 font-medium leading-relaxed">
                    Para aplicar un descuento del <strong class="text-amber-600" x-text="_pendingCortesia + '%'"></strong>
                    se necesita la contraseña de un administrador.
                </p>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Contraseña de Administrador</label>
                    <input type="password"
                           x-model="cortesiaPassInput"
                           @keydown.enter="confirmarCortesia()"
                           placeholder="Contraseña..."
                           class="w-full border-2 border-gray-200 rounded-xl py-3 px-4 text-[14px] font-bold focus:outline-none focus:border-amber-400 transition-colors">
                    <p x-show="cortesiaPassError" x-text="cortesiaPassError" class="text-red-500 text-xs font-bold mt-2" x-cloak></p>
                </div>
            </div>
            <div class="p-5 bg-gray-50 rounded-b-2xl flex gap-3 border-t border-gray-100">
                <button @click="modalCortesia = false; _pendingCortesia = null;"
                        class="flex-1 font-bold text-gray-500 bg-white border border-gray-200 rounded-xl py-3 hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
                <button @click="confirmarCortesia()"
                        class="flex-1 font-black bg-amber-500 hover:bg-amber-600 text-white rounded-xl py-3 uppercase italic tracking-wide transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    {{-- ══ ÉXITO (overlay) ══ --}}
    <div x-show="cobrarSuccess" x-cloak class="fixed inset-0 bg-black/70 z-[200] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-[380px] overflow-hidden">
            <div class="bg-[#28a745] p-6 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-2xl font-black uppercase italic tracking-wide">¡Cobro exitoso!</h3>
                <p class="text-sm opacity-80 mt-1">Mesa <span x-text="mesaActual"></span> liberada</p>
            </div>
            <div class="p-5 flex gap-3">
                <button @click="abrirTicket(ventaCobrada)"
                   class="flex-1 font-black bg-[#28a745] hover:bg-[#218838] text-white rounded-xl py-3 text-sm text-center flex items-center justify-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Ver ticket
                </button>
                <button @click="cerrarTodo()"
                        class="flex-1 font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-xl py-3 text-sm transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function mesasApp() {
    return {
        estado: @json($mesasOcupadas),
        modalAbierto: false,
        modalPago: false,
        mesaActual: null,
        comanda: {},
        cargando: false,
        cargandoItems: false,
        esAdmin: @json($esAdmin),
        cierreLoading: false,
        cierreToast: '',
        cierreToastType: 'success',

        // Cobro
        cobrarLoading: false,
        cobrarSuccess: false,
        cobrarError: '',
        ventaCobrada: null,
        cortesia: 0,
        pagos: {
            efectivo:     { activo: false, monto: 0, entregado: 0 },
            tarjeta:      { activo: false, monto: 0 },
            transferencia:{ activo: false, monto: 0, referencia: '' },
        },

        // Modal descuento admin
        modalCortesia: false,
        _pendingCortesia: null,
        cortesiaPassInput: '',
        cortesiaPassError: '',

        get ocupadas() { return Object.keys(this.estado).length; },

        init() { setInterval(() => this.refrescar(), 20000); },

        async refrescar() {
            this.cargando = true;
            try { const r = await fetch('/mesas/estado'); this.estado = await r.json(); } catch(e) {}
            this.cargando = false;
        },

        async abrirMesa(num) {
            this.mesaActual = num;
            this.comanda = {};
            this.modalAbierto = true;
            const mesaDatos = this.estado[num];
            if (!mesaDatos) return;
            this.cargandoItems = true;
            try {
                const r = await fetch('/mesas/detalle/' + mesaDatos.id_venta);
                this.comanda = await r.json();
            } catch(e) { this.comanda = { ...mesaDatos, items: [] }; }
            this.cargandoItems = false;
        },

        abrirPago() {
            this.cobrarError = '';
            this.cortesia = 0;
            this.pagos = {
                efectivo:     { activo: false, monto: 0, entregado: 0 },
                tarjeta:      { activo: false, monto: 0 },
                transferencia:{ activo: false, monto: 0, referencia: '' },
            };
            this.modalPago = true;
        },

        // Total con descuento
        getTotal() {
            const base = parseFloat(this.comanda.total || 0);
            if (this.cortesia === 0) return base;
            if (this.cortesia === 100) return 0;
            return Math.ceil(base - (base * this.cortesia / 100) * 10) / 10;
        },

        // Cuánto falta por cubrir
        faltaPagar() {
            if (this.cortesia === 100) return 0;
            const total = this.getTotal();
            const cubierto = (this.pagos.efectivo.activo  ? (parseFloat(this.pagos.efectivo.monto)       || 0) : 0)
                           + (this.pagos.tarjeta.activo   ? (parseFloat(this.pagos.tarjeta.monto)        || 0) : 0)
                           + (this.pagos.transferencia.activo ? (parseFloat(this.pagos.transferencia.monto) || 0) : 0);
            return parseFloat((total - cubierto).toFixed(2));
        },

        // Llena automáticamente el monto pendiente cuando se activa un método
        autoFillPago(metodo) {
            if (!this.pagos[metodo].activo) { this.pagos[metodo].monto = 0; return; }
            const pendiente = this.faltaPagar();
            if (pendiente > 0) this.pagos[metodo].monto = parseFloat(pendiente.toFixed(2));
        },

        autoFill() {
            // Después de quitar descuento, re-calcula el primer método activo
            const total = this.getTotal();
            if (this.pagos.efectivo.activo)      this.pagos.efectivo.monto = total;
            else if (this.pagos.tarjeta.activo)  this.pagos.tarjeta.monto  = total;
            else if (this.pagos.transferencia.activo) this.pagos.transferencia.monto = total;
        },

        // Verifica que los pagos sean válidos para habilitar "Confirmar"
        pagosValidos() {
            if (this.cortesia === 100) return true;
            const alguno = this.pagos.efectivo.activo || this.pagos.tarjeta.activo || this.pagos.transferencia.activo;
            return alguno && this.faltaPagar() === 0;
        },

        // Descuentos — pedir contraseña admin si no es admin
        pedirCortesia(valor) {
            if (this.esAdmin) {
                this.cortesia = valor;
                this.autoFill();
                return;
            }
            this._pendingCortesia = valor;
            this.cortesiaPassInput = '';
            this.cortesiaPassError = '';
            this.modalCortesia = true;
        },

        async confirmarCortesia() {
            if (!this.cortesiaPassInput.trim()) {
                this.cortesiaPassError = 'Ingresa la contraseña de administrador.';
                return;
            }
            try {
                const res = await fetch('/venta/verificar-admin', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrfToken }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ admin_password: this.cortesiaPassInput }),
                });
                const data = await res.json();
                if (!data.success) {
                    this.cortesiaPassError = data.message || 'Contraseña incorrecta.';
                    this.cortesiaPassInput = '';
                    return;
                }
            } catch(e) {
                this.cortesiaPassError = 'Error al verificar. Intenta de nuevo.';
                return;
            }
            this.cortesia = this._pendingCortesia;
            this.autoFill();
            this.modalCortesia = false;
            this._pendingCortesia = null;
        },

        async cobrarOrden() {
            this.cobrarLoading = true;
            this.cobrarError = '';
            const total = this.getTotal();

            let pagosToSend = [];
            if (this.cortesia === 100) {
                // Cortesía total — no hay pago real
            } else {
                if (this.pagos.efectivo.activo && this.pagos.efectivo.monto > 0)
                    pagosToSend.push({ id_metpago: 2, monto: this.pagos.efectivo.monto, entregado: this.pagos.efectivo.entregado || this.pagos.efectivo.monto });
                if (this.pagos.tarjeta.activo && this.pagos.tarjeta.monto > 0)
                    pagosToSend.push({ id_metpago: 1, monto: this.pagos.tarjeta.monto });
                if (this.pagos.transferencia.activo && this.pagos.transferencia.monto > 0)
                    pagosToSend.push({ id_metpago: 3, monto: this.pagos.transferencia.monto, referencia: this.pagos.transferencia.referencia || null });
            }

            const body = {
                id_venta: this.comanda.id_venta,
                pagos: pagosToSend,
            };
            if (this.cortesia > 0) {
                body.cortesia    = this.cortesia;
                body.nuevo_total = total;
                body.admin_password = this.cortesiaPassInput || '';
            }

            try {
                const resp = await fetch('/venta/pagar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrfToken }}', 'Accept': 'application/json' },
                    body: JSON.stringify(body),
                });
                const data = await resp.json();
                if (data.success) {
                    this.ventaCobrada = this.comanda.id_venta;
                    this.cobrarSuccess = true;
                    this.modalPago = false;
                    this.modalAbierto = false;
                    // Quitar del mapa
                    const nuevo = { ...this.estado };
                    delete nuevo[this.mesaActual];
                    this.estado = nuevo;
                } else {
                    this.cobrarError = data.message || 'Error al procesar el pago.';
                }
            } catch(e) {
                this.cobrarError = 'Error de conexión. Intenta de nuevo.';
            }
            this.cobrarLoading = false;
        },

        confirmarCierreAnteriores() {
            if (!confirm('¿Cerrar todas las mesas abiertas de días anteriores?\n\nEsta acción marcará esas ventas como cerradas.')) return;
            this.ejecutarCierreAnteriores();
        },

        async ejecutarCierreAnteriores() {
            this.cierreLoading = true;
            try {
                const r = await fetch('/mesas/cerrar-anteriores', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                });
                const data = await r.json();
                if (data.success) {
                    if (data.cerradas === 0) {
                        this.cierreToast = 'No hay mesas de días anteriores por cerrar.';
                        this.cierreToastType = 'info';
                    } else {
                        this.cierreToast = `${data.cerradas} mesa${data.cerradas > 1 ? 's' : ''} cerrada${data.cerradas > 1 ? 's' : ''} correctamente.`;
                        this.cierreToastType = 'success';
                        await this.refrescar();
                    }
                } else {
                    this.cierreToast = 'Ocurrió un error. Intenta de nuevo.';
                    this.cierreToastType = 'error';
                }
            } catch(e) {
                this.cierreToast = 'Error de conexión.';
                this.cierreToastType = 'error';
            }
            this.cierreLoading = false;
            setTimeout(() => { this.cierreToast = ''; }, 3500);
        },

        cerrarModal() {
            this.modalAbierto = false;
            this.modalPago = false;
            this.mesaActual = null;
            this.comanda = {};
            this.cobrarError = '';
        },

        cerrarTodo() {
            this.cobrarSuccess = false;
            this.ventaCobrada = null;
            this.mesaActual = null;
            this.comanda = {};
            this.cobrarError = '';
        },

        abrirTicket(id) {
            const width = 420, height = 700;
            const left = Math.round((window.screen.width  / 2) - (width  / 2));
            const top  = Math.round((window.screen.height / 2) - (height / 2));
            window.open('/venta/pos/ticket/' + id, 'TicketPizzetos',
                `width=${width},height=${height},left=${left},top=${top},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`);
        },

        tiempoTranscurrido(fechaHora) {
            if (!fechaHora) return '';
            const diff = Math.floor((Date.now() - new Date(fechaHora).getTime()) / 60000);
            if (diff < 1)  return 'Recién';
            if (diff < 60) return diff + ' min';
            const h = Math.floor(diff / 60), m = diff % 60;
            return h + 'h ' + (m > 0 ? m + 'm' : '');
        },
    }
}
</script>
@endsection
