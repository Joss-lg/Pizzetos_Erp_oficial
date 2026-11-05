@extends('layouts.repartidor')
@section('title', 'Mis Pedidos')

@section('content')
<div x-data="repMio()" x-init="init()" class="min-h-screen pb-6">

    {{-- Sin pedidos --}}
    <template x-if="pedidos.length === 0">
        <div class="flex flex-col items-center justify-center min-h-[70vh] px-6 text-center">
            <div class="w-20 h-20 rounded-3xl bg-white/[.05] flex items-center justify-center mb-4 text-4xl">✅</div>
            <p class="text-xl font-black text-white">¡Todo al día!</p>
            <p class="text-sm text-slate-400 mt-1">No hay pedidos pendientes</p>
            <button @click="refrescar()" class="mt-6 px-5 py-2.5 rounded-2xl bg-white/[.07] text-slate-300 text-sm font-bold flex items-center gap-2">
                <svg class="w-4 h-4" :class="cargando ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Verificar
            </button>
        </div>
    </template>

    {{-- Lista de pedidos --}}
    <div class="px-4 pt-4 space-y-4 max-w-lg mx-auto">
        <template x-for="p in pedidos" :key="p.id_pdomicilio">
            <div class="slide-up rounded-3xl overflow-hidden"
                 :class="p._nuevo ? 'ring-2 ring-[#fd7e14]' : ''"
                 style="background: #131c2e; border: 1px solid rgba(255,255,255,.07);">

                {{-- Banda superior con degradado de estado --}}
                <div class="h-1.5 w-full"
                     :class="{
                        'bg-gradient-to-r from-slate-600 to-slate-500': p.status_reparto == 0,
                        'bg-gradient-to-r from-amber-500 to-amber-400': p.status_reparto == 1,
                        'bg-gradient-to-r from-[#fd7e14] to-amber-400': p.status_reparto == 2,
                        'bg-gradient-to-r from-green-500 to-emerald-400': p.status_reparto == 3,
                     }">
                </div>

                <div class="p-4 space-y-4">

                    {{-- Folio prominente --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-[#fd7e14]/15 border border-[#fd7e14]/30 rounded-2xl px-3 py-1.5 flex items-center gap-1.5">
                                <span class="text-[10px] font-black text-[#fd7e14]/60 uppercase tracking-widest">#</span>
                                <span class="text-2xl font-black text-[#fd7e14] leading-none tracking-tight"
                                      x-text="p.id_venta"></span>
                            </div>
                            <span x-show="p._nuevo" x-cloak
                                  class="text-[9px] font-black bg-[#fd7e14] text-white px-2 py-0.5 rounded-full uppercase tracking-wider animate-pulse">
                                Nuevo
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-semibold" x-text="formatHora(p.fecha_hora)"></span>
                    </div>

                    {{-- ── PROGRESS STEPPER ── --}}
                    <div class="flex items-center gap-0">

                        {{-- Paso 1: Tomado --}}
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all"
                                 :class="p.status_reparto >= 1
                                    ? (p.status_reparto == 1 ? 'bg-amber-500 text-white pulse-ring' : 'bg-amber-500/30 text-amber-400')
                                    : 'bg-white/[.08] text-slate-600'">
                                <template x-if="p.status_reparto > 1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="p.status_reparto <= 1">
                                    <span>1</span>
                                </template>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-wider"
                                  :class="p.status_reparto >= 1 ? 'text-amber-400' : 'text-slate-600'">
                                Tomado
                            </span>
                        </div>

                        {{-- Línea 1→2 --}}
                        <div class="flex-1 h-0.5 mb-4 mx-1 transition-all"
                             :class="p.status_reparto >= 2 ? 'bg-[#fd7e14]' : 'bg-white/[.08]'"></div>

                        {{-- Paso 2: En camino --}}
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all"
                                 :class="p.status_reparto >= 2
                                    ? (p.status_reparto == 2 ? 'bg-[#fd7e14] text-white pulse-ring' : 'bg-[#fd7e14]/30 text-orange-400')
                                    : 'bg-white/[.08] text-slate-600'">
                                <template x-if="p.status_reparto > 2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="p.status_reparto <= 2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7c0-1.1-.9-2-2-2h-3l2 4h3c1.1 0 2-.9 2-2zM4.5 11H2v2h2.09A5.004 5.004 0 002 17c0 2.76 2.24 5 5 5s5-2.24 5-5c0-.34-.04-.67-.1-1H14v-1.5l-1-3H9.5L8 11H4.5zm2.5 8c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm10-1c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-6c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2v-2z"/>
                                    </svg>
                                </template>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-wider"
                                  :class="p.status_reparto >= 2 ? 'text-orange-400' : 'text-slate-600'">
                                En camino
                            </span>
                        </div>

                        {{-- Línea 2→3 --}}
                        <div class="flex-1 h-0.5 mb-4 mx-1 transition-all"
                             :class="p.status_reparto >= 3 ? 'bg-green-500' : 'bg-white/[.08]'"></div>

                        {{-- Paso 3: Entregado --}}
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all"
                                 :class="p.status_reparto >= 3
                                    ? 'bg-green-500/30 text-green-400'
                                    : 'bg-white/[.08] text-slate-600'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-wider"
                                  :class="p.status_reparto >= 3 ? 'text-green-400' : 'text-slate-600'">
                                Entregado
                            </span>
                        </div>

                    </div>
                    {{-- ── FIN STEPPER ── --}}

                    {{-- Separador --}}
                    <div class="border-t border-white/[.06]"></div>

                    {{-- Cliente + dirección + llamar --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-white text-base leading-tight" x-text="p.cliente || 'Cliente'"></p>
                            <p class="text-sm text-slate-400 font-medium mt-0.5" x-text="p.telefono || ''"></p>
                            <div class="mt-2 text-sm text-slate-300 leading-relaxed">
                                <p class="font-semibold" x-text="(p.calle || '') + (p.manzana ? ' Mza '+p.manzana : '') + (p.lote ? ' Lt '+p.lote : '')"></p>
                                <p class="text-slate-400" x-text="p.colonia || ''"></p>
                                <p x-show="p.referencia" class="mt-1 text-xs text-slate-500 italic" x-text="'📍 ' + p.referencia"></p>
                            </div>
                        </div>
                        <a x-show="p.telefono" :href="'tel:' + p.telefono"
                           class="w-11 h-11 rounded-2xl bg-green-500/20 border border-green-500/30 flex items-center justify-center shrink-0 transition-all hover:bg-green-500/30">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between bg-white/[.04] rounded-2xl px-4 py-3">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Total a cobrar</span>
                        <span class="font-black text-[#fd7e14] text-xl" x-text="'$' + Number(p.total).toFixed(2)"></span>
                    </div>

                    {{-- ── BOTÓN DE ACCIÓN ── --}}

                    {{-- Disponible → Tomar --}}
                    <template x-if="p.status_reparto == 0">
                        <button @click="tomar(p)" :disabled="p._loading"
                                class="w-full py-4 rounded-2xl font-black text-sm uppercase tracking-widest flex items-center justify-center gap-2.5 transition-all"
                                style="background: linear-gradient(135deg, #fd7e14, #ffc107); color: #000;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Tomar pedido
                        </button>
                    </template>

                    {{-- Tomado → Ya voy en camino --}}
                    <template x-if="p.status_reparto == 1 && p.id_repartidor == {{ $user->id_emp }}">
                        <button @click="salir(p)" :disabled="p._loading"
                                class="w-full py-4 rounded-2xl font-black text-sm uppercase tracking-widest flex items-center justify-center gap-2.5 transition-all text-white"
                                style="background: linear-gradient(135deg, #fd7e14, #e96b00);">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7c0-1.1-.9-2-2-2h-3l2 4h3c1.1 0 2-.9 2-2zM4.5 11H2v2h2.09A5.004 5.004 0 002 17c0 2.76 2.24 5 5 5s5-2.24 5-5c0-.34-.04-.67-.1-1H14v-1.5l-1-3H9.5L8 11H4.5zm2.5 8c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm10-1c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-6c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2v-2z"/>
                            </svg>
                            Ya voy en camino
                        </button>
                    </template>

                    {{-- En camino → Swipe para entregar (estilo Rappi) --}}
                    <template x-if="p.status_reparto == 2 && p.id_repartidor == {{ $user->id_emp }}">
                        <div class="relative select-none touch-none overflow-hidden"
                             style="height:64px; border-radius:9999px; background:#0d2318; border: 1.5px solid rgba(34,197,94,0.25);">

                            {{-- Fill verde que crece al deslizar --}}
                            <div class="absolute inset-y-0 left-0 pointer-events-none"
                                 style="border-radius:9999px; background: linear-gradient(90deg,rgba(34,197,94,.35),rgba(34,197,94,.1)); transition:none;"
                                 :style="`width:${(p._swipeX||0)+68}px`"></div>

                            {{-- Texto central que se desvanece al deslizar --}}
                            <div class="absolute inset-0 flex items-center justify-center gap-2 pointer-events-none"
                                 :style="`opacity:${Math.max(0, 1 - (p._swipeX||0)/100)}`">
                                <svg class="w-4 h-4 text-green-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-green-400/60 font-black text-xs uppercase tracking-widest">Desliza para entregar</span>
                                <svg class="w-4 h-4 text-green-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>

                            {{-- Thumb --}}
                            <div class="absolute top-2 left-2 flex items-center justify-center cursor-grab"
                                 style="width:48px; height:48px; border-radius:9999px; background:linear-gradient(135deg,#22c55e,#16a34a); box-shadow:0 4px 20px rgba(34,197,94,.55), 0 2px 8px rgba(0,0,0,.3);"
                                 :style="`transform:translateX(${p._swipeX||0}px); transition:${p._swipeDragging?'none':'transform .4s cubic-bezier(.34,1.56,.64,1)'}`"
                                 @mousedown.prevent="startSwipe($event, p)"
                                 @touchstart.prevent="startSwipe($event, p)">
                                <template x-if="!p._swipeDone">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h14"/>
                                    </svg>
                                </template>
                                <template x-if="p._swipeDone">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                            </div>

                        </div>
                    </template>

                    {{-- Entregado --}}
                    <template x-if="p.status_reparto == 3">
                        <div class="w-full py-3 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-400 font-black text-sm text-center uppercase tracking-widest">
                            ✓ Entregado
                        </div>
                    </template>

                </div>
            </div>
        </template>
    </div>

    {{-- Toast --}}
    <div x-show="toast" x-cloak x-transition.opacity
         :class="toastType === 'ok' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-2xl shadow-2xl text-white font-bold text-sm whitespace-nowrap"
         x-text="toast">
    </div>

    {{-- FAB Refresh --}}
    <button @click="refrescar()"
            class="fixed bottom-6 right-5 w-12 h-12 rounded-2xl bg-white/[.08] hover:bg-white/[.12] flex items-center justify-center transition-all shadow-xl backdrop-blur z-40">
        <svg class="w-5 h-5 text-slate-300" :class="cargando ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
    </button>

</div>

<script>
function repMio() {
    return {
        pedidos: @json($pedidos),
        cargando: false,
        toast: '',
        toastType: 'ok',
        _ids: null,
        _timer: null,

        init() {
            this.pedidos = this.pedidos.map(p => ({ ...p, _loading: false, _nuevo: false, _swipeX: 0, _swipeDragging: false, _swipeDone: false }));
            this._ids = new Set(this.pedidos.map(p => p.id_pdomicilio));
            this._timer = setInterval(() => this.refrescar(), 5000);
        },

        startSwipe(e, p) {
            const touch = e.touches ? e.touches[0] : e;
            const track = e.currentTarget.parentElement;
            const maxX = track.offsetWidth - 60; // pista - thumb - margen
            const startClientX = touch.clientX;
            p._swipeDragging = true;

            const onMove = (ev) => {
                ev.preventDefault();
                const t = ev.touches ? ev.touches[0] : ev;
                const diff = Math.max(0, Math.min(t.clientX - startClientX, maxX));
                p._swipeX = diff;
                if (diff >= maxX - 4) finish(true);
            };
            const finish = (success) => {
                cleanup();
                p._swipeDragging = false;
                if (success) {
                    p._swipeX = maxX;
                    p._swipeDone = true;
                    setTimeout(() => this.entregar(p), 350);
                } else {
                    p._swipeX = 0;
                }
            };
            const cleanup = () => {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onEnd);
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onEnd);
            };
            const onEnd = () => finish(false);
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onEnd);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onEnd);
        },

        async refrescar() {
            this.cargando = true;
            try {
                const r = await fetch('/repartidor/estado');
                const data = await r.json();
                const prevIds = this._ids;
                let countNuevos = 0;
                const actualizados = data.map(p => {
                    const esNuevo = !prevIds.has(p.id_pdomicilio);
                    if (esNuevo) countNuevos++;
                    return { ...p, _loading: false, _nuevo: esNuevo, _swipeX: 0, _swipeDragging: false };
                });
                this.pedidos = actualizados;
                this._ids = new Set(actualizados.map(p => p.id_pdomicilio));
                if (countNuevos > 0) this.showToast('¡Nuevo pedido disponible! 🏍️', 'ok');
            } catch(e) {}
            this.cargando = false;
        },

        async tomar(p) {
            p._loading = true;
            const ok = await this.post('/repartidor/tomar', { id_pdomicilio: p.id_pdomicilio });
            if (ok) { this.showToast('¡Pedido tomado!', 'ok'); await this.refrescar(); }
            p._loading = false;
        },

        async salir(p) {
            p._loading = true;
            const ok = await this.post('/repartidor/salir', { id_pdomicilio: p.id_pdomicilio });
            if (ok) { this.showToast('¡En camino! 🏍️', 'ok'); await this.refrescar(); }
            p._loading = false;
        },

        async entregar(p) {
            p._loading = true;
            const ok = await this.post('/repartidor/entregar', { id_pdomicilio: p.id_pdomicilio });
            if (ok) {
                this.playSuccessSound();
                this.showToast('¡Entregado! ✅', 'ok');
                await this.refrescar();
            } else {
                p._swipeDone = false;
                p._swipeX = 0;
            }
            p._loading = false;
        },

        playSuccessSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const play = (freq, start, duration) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + start);
                    gain.gain.setValueAtTime(0, ctx.currentTime + start);
                    gain.gain.linearRampToValueAtTime(0.28, ctx.currentTime + start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + duration);
                    osc.start(ctx.currentTime + start);
                    osc.stop(ctx.currentTime + start + duration);
                };
                play(523, 0,    0.25); // C5
                play(659, 0.12, 0.25); // E5
                play(784, 0.24, 0.5);  // G5
            } catch(e) {}
        },

        async post(url, body) {
            try {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(body),
                });
                const data = await r.json();
                if (!data.success) { this.showToast(data.error || 'Error', 'err'); return false; }
                return true;
            } catch(e) { this.showToast('Error de conexión', 'err'); return false; }
        },

        showToast(msg, type) {
            this.toast = msg; this.toastType = type;
            setTimeout(() => { this.toast = ''; }, 3000);
        },

        formatHora(dt) {
            if (!dt) return '';
            return new Date(dt.replace(' ', 'T')).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
        },
    };
}
</script>
@endsection
