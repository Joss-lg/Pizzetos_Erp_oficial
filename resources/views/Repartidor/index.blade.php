@extends('layouts.app')

@section('title', 'Repartidor')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js" defer></script>
<style>
    [x-cloak] { display: none !important; }

    .app-dark .rep-card    { background: #131c2e; border-color: rgba(255,255,255,.07); }
    .app-dark .rep-title   { color: #f1f5f9; }
    .app-dark .rep-sub     { color: #94a3b8; }
    .app-dark .rep-select  { background: #1e293b; color: #f1f5f9; border-color: rgba(255,255,255,.12); }
    .app-dark .rep-select:focus { border-color: #fd7e14; outline: none; }
    .app-dark .rep-divider { border-color: rgba(255,255,255,.06); }
    .app-dark .stat-card   { background: #0f1929; border-color: rgba(255,255,255,.06); }

    html:not(.app-dark) .rep-card    { background: #fff; border-color: rgba(0,0,0,.08); }
    html:not(.app-dark) .rep-title   { color: #111827; }
    html:not(.app-dark) .rep-sub     { color: #6b7280; }
    html:not(.app-dark) .rep-select  { background: #fff; color: #111827; border-color: rgba(0,0,0,.15); }
    html:not(.app-dark) .rep-select:focus { border-color: #fd7e14; outline: none; }
    html:not(.app-dark) .rep-divider { border-color: rgba(0,0,0,.06); }
    html:not(.app-dark) .stat-card   { background: #f8fafc; border-color: rgba(0,0,0,.06); }

    .status-bar-0 { background: linear-gradient(90deg, #64748b, #475569); }
    .status-bar-1 { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .status-bar-2 { background: linear-gradient(90deg, #fd7e14, #fbbf24); }
    .status-bar-3 { background: linear-gradient(90deg, #22c55e, #16a34a); }

    .rep-card { transition: box-shadow .2s, border-color .2s; }
    .rep-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.18); }
</style>

<div x-data="repAdmin()" x-init="init()" class="p-4 md:p-6 max-w-7xl mx-auto">

    {{-- ── HEADER ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg text-2xl shrink-0"
                 style="background: linear-gradient(135deg,#fd7e14,#ffc107);">🏍️</div>
            <div>
                <h1 class="text-2xl font-black rep-title tracking-tight leading-none">Repartidor</h1>
                <p class="text-[11px] rep-sub font-bold mt-0.5 uppercase tracking-widest">Domicilios del día</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Badge nuevo pedido --}}
            <div x-show="nuevos > 0" x-cloak x-transition
                 class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-500 text-white text-xs font-black animate-pulse shadow-lg">
                ¡+<span x-text="nuevos"></span> nuevo<span x-show="nuevos > 1">s</span>!
            </div>
            <button @click="refrescar()"
                    class="w-9 h-9 rounded-xl flex items-center justify-center transition-all"
                    style="background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);">
                <svg class="w-4 h-4 rep-sub" :class="cargando ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-4 gap-3 mb-6">
        <div class="stat-card border rounded-2xl p-3 text-center">
            <p class="text-2xl font-black rep-title" x-text="sinAsignar"></p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-0.5" style="color:#64748b;">Sin asignar</p>
        </div>
        <div class="stat-card border rounded-2xl p-3 text-center">
            <p class="text-2xl font-black" style="color:#f59e0b;" x-text="tomados"></p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-0.5" style="color:#f59e0b80;">Tomados</p>
        </div>
        <div class="stat-card border rounded-2xl p-3 text-center">
            <p class="text-2xl font-black" style="color:#fd7e14;" x-text="enCamino"></p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-0.5" style="color:#fd7e1480;">En camino</p>
        </div>
        <div class="stat-card border rounded-2xl p-3 text-center">
            <p class="text-2xl font-black" style="color:#22c55e;" x-text="entregados"></p>
            <p class="text-[10px] font-black uppercase tracking-widest mt-0.5" style="color:#22c55e80;">Entregados</p>
        </div>
    </div>

    {{-- Sin pedidos --}}
    <template x-if="pedidosActivos.length === 0 && !cargando && pedidosHistorial.length === 0">
        <div class="text-center py-20 rep-sub">
            <div class="text-5xl mb-3">🏍️</div>
            <p class="font-bold">No hay domicilios registrados hoy</p>
        </div>
    </template>

    {{-- Sin activos pero hay historial --}}
    <template x-if="pedidosActivos.length === 0 && pedidosHistorial.length > 0">
        <div class="text-center py-12 rep-sub">
            <div class="text-4xl mb-2">✅</div>
            <p class="font-bold text-sm">Todos los pedidos entregados</p>
        </div>
    </template>

    {{-- ── GRID DE TARJETAS ACTIVAS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="p in pedidosActivos" :key="p.id_pdomicilio">
            <div class="rep-card rounded-2xl border overflow-hidden flex flex-col"
                 :class="p._nuevo ? 'ring-2 ring-[#fd7e14] shadow-[0_0_24px_rgba(253,126,20,.2)]' : ''">

                {{-- Barra de color de estado --}}
                <div class="h-1" :class="`status-bar-${p.status_reparto}`"></div>

                <div class="p-4 flex flex-col gap-3 flex-1">

                    {{-- Folio + status + hora --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black" style="color:#fd7e14;" x-text="'#' + p.id_venta"></span>
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full"
                                  :class="{
                                    'bg-slate-500/15 text-slate-400': p.status_reparto == 0,
                                    'bg-amber-500/15 text-amber-400': p.status_reparto == 1,
                                    'bg-orange-500/15 text-orange-400': p.status_reparto == 2,
                                    'bg-green-500/15 text-green-400': p.status_reparto == 3,
                                  }"
                                  x-text="['Sin asignar','Tomado','En camino','Entregado'][p.status_reparto]">
                            </span>
                        </div>
                        <span class="text-[11px] rep-sub font-semibold shrink-0" x-text="formatHora(p.fecha_hora)"></span>
                    </div>

                    {{-- Cliente + teléfono --}}
                    <div>
                        <p class="font-black rep-title text-base leading-tight" x-text="p.cliente || 'Cliente'"></p>
                        <p class="text-xs rep-sub font-semibold mt-0.5" x-text="p.telefono || ''"></p>
                    </div>

                    {{-- Dirección --}}
                    <div class="border-t rep-divider pt-3 text-xs rep-sub leading-relaxed">
                        <p class="font-bold rep-title" x-text="(p.calle||'') + (p.manzana ? ' Mza '+p.manzana : '') + (p.lote ? ' Lt '+p.lote : '')"></p>
                        <p x-text="p.colonia || ''"></p>
                        <p x-show="p.referencia" class="mt-1 italic opacity-70" x-text="'📍 ' + p.referencia"></p>
                    </div>

                    {{-- Total + repartidor --}}
                    <div class="flex items-center justify-between border-t rep-divider pt-3">
                        <div x-show="p.repartidor_nombre" class="flex items-center gap-1.5 text-xs rep-sub">
                            <div class="w-5 h-5 rounded-full bg-[#fd7e14]/20 flex items-center justify-center text-[9px] font-black text-[#fd7e14]"
                                 x-text="(p.repartidor_nombre||'?')[0].toUpperCase()"></div>
                            <span x-text="p.repartidor_nombre"></span>
                        </div>
                        <div x-show="!p.repartidor_nombre" class="text-xs rep-sub opacity-50">Sin asignar</div>
                        <span class="font-black text-lg" style="color:#fd7e14;" x-text="'$'+Number(p.total).toFixed(2)"></span>
                    </div>

                    {{-- ── ACCIONES ── --}}
                    <div class="flex flex-col gap-2 mt-auto pt-1">

                        {{-- Sin asignar: selector + asignar --}}
                        <template x-if="p.status_reparto == 0">
                            <div class="flex gap-2">
                                <select x-model="p._repSelected"
                                        class="rep-select flex-1 text-xs font-bold border rounded-xl px-3 py-2.5 transition-colors">
                                    <option value="">— Repartidor —</option>
                                    <template x-for="r in repartidores" :key="r.id_emp">
                                        <option :value="r.id_emp" x-text="r.nombre"></option>
                                    </template>
                                </select>
                                <button @click="asignar(p)" :disabled="!p._repSelected"
                                        class="px-4 py-2.5 rounded-xl font-black text-xs uppercase tracking-wide transition-all disabled:opacity-30 text-white shrink-0"
                                        style="background: linear-gradient(135deg,#fd7e14,#fbbf24);">
                                    Asignar
                                </button>
                            </div>
                        </template>

                        {{-- Tomado: reasignar + en camino --}}
                        <template x-if="p.status_reparto == 1">
                            <div class="flex gap-2">
                                <select x-model="p._repSelected"
                                        class="rep-select flex-1 text-xs font-bold border rounded-xl px-3 py-2.5 transition-colors">
                                    <option value="">Reasignar...</option>
                                    <template x-for="r in repartidores" :key="r.id_emp">
                                        <option :value="r.id_emp" x-text="r.nombre"></option>
                                    </template>
                                </select>
                                <button @click="reasignar(p)" :disabled="!p._repSelected"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white shrink-0 transition-all disabled:opacity-30"
                                        style="background:#f59e0b;" title="Reasignar">↺</button>
                                <button @click="salir(p)"
                                        class="px-3 py-2 rounded-xl font-black text-xs text-white shrink-0 transition-all flex items-center gap-1"
                                        style="background: linear-gradient(135deg,#fd7e14,#e96b00);">
                                    🏍️ Camino
                                </button>
                            </div>
                        </template>

                        {{-- En camino: reasignar + entregado --}}
                        <template x-if="p.status_reparto == 2">
                            <div class="flex gap-2">
                                <select x-model="p._repSelected"
                                        class="rep-select flex-1 text-xs font-bold border rounded-xl px-3 py-2.5 transition-colors">
                                    <option value="">Reasignar...</option>
                                    <template x-for="r in repartidores" :key="r.id_emp">
                                        <option :value="r.id_emp" x-text="r.nombre"></option>
                                    </template>
                                </select>
                                <button @click="reasignar(p)" :disabled="!p._repSelected"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white shrink-0 transition-all disabled:opacity-30"
                                        style="background:#f59e0b;" title="Reasignar">↺</button>
                                <button @click="entregar(p)"
                                        class="px-3 py-2 rounded-xl font-black text-xs text-white shrink-0 transition-all"
                                        style="background: linear-gradient(135deg,#22c55e,#16a34a);">
                                    ✓ Entregado
                                </button>
                            </div>
                        </template>

                        {{-- Entregado --}}
                        <template x-if="p.status_reparto == 3">
                            <div class="flex items-center justify-center gap-2 py-2 rounded-xl text-xs font-black"
                                 style="background: rgba(34,197,94,.1); color: #22c55e;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Entregado al cliente
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ── HISTORIAL DE ENTREGADOS ── --}}
    <template x-if="pedidosHistorial.length > 0">
        <div class="mt-8">
            {{-- Toggle header --}}
            <button @click="historialAbierto = !historialAbierto"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-2xl border rep-divider transition-all mb-3"
                    style="background: rgba(255,255,255,.03);">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-xl bg-green-500/15 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-black text-sm rep-title">Historial de entregados</span>
                    <span class="text-xs font-black px-2 py-0.5 rounded-full bg-green-500/15 text-green-400"
                          x-text="pedidosHistorial.length"></span>
                </div>
                <svg class="w-4 h-4 rep-sub transition-transform duration-300"
                     :class="historialAbierto ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Lista compacta de entregados --}}
            <div x-show="historialAbierto" x-collapse>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="p in pedidosHistorial" :key="p.id_pdomicilio">
                        <div class="rep-card rounded-2xl border overflow-hidden opacity-70">
                            <div class="h-1 status-bar-3"></div>
                            <div class="p-3 flex items-center gap-3">
                                <span class="text-xl font-black shrink-0" style="color:#22c55e;" x-text="'#'+p.id_venta"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold rep-title text-sm truncate" x-text="p.cliente||'Cliente'"></p>
                                    <p class="text-xs rep-sub truncate" x-text="(p.calle||'')+(p.colonia?' · '+p.colonia:'')"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-black text-sm" style="color:#22c55e;" x-text="'$'+Number(p.total).toFixed(2)"></p>
                                    <p class="text-[10px] rep-sub" x-text="formatHora(p.fecha_hora)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
function repAdmin() {
    return {
        pedidos:      @json($pedidos),
        repartidores: @json($repartidores),
        cargando: false,
        nuevos: 0,
        _ids: new Set(),

        get sinAsignar()    { return this.pedidos.filter(p => p.status_reparto == 0).length; },
        get tomados()       { return this.pedidos.filter(p => p.status_reparto == 1).length; },
        get enCamino()      { return this.pedidos.filter(p => p.status_reparto == 2).length; },
        get entregados()    { return this.pedidos.filter(p => p.status_reparto == 3).length; },
        get pedidosActivos(){ return this.pedidos.filter(p => p.status_reparto < 3); },
        get pedidosHistorial(){ return this.pedidos.filter(p => p.status_reparto == 3); },

        historialAbierto: false,

        init() {
            this.pedidos = this.pedidos.map(p => ({ ...p, _repSelected: '', _nuevo: false }));
            this._ids = new Set(this.pedidos.map(p => p.id_pdomicilio));
            setInterval(() => this.refrescar(), 5000);
        },

        async refrescar() {
            this.cargando = true;
            try {
                const r = await fetch('/repartidor/estado');
                const data = await r.json();
                const prevIds = this._ids;
                let countNuevos = 0;
                const actualizados = data.pedidos.map(p => {
                    const esNuevo = !prevIds.has(p.id_pdomicilio);
                    if (esNuevo) countNuevos++;
                    const prev = this.pedidos.find(x => x.id_pdomicilio === p.id_pdomicilio);
                    return { ...p, _repSelected: prev?._repSelected || '', _nuevo: esNuevo };
                });
                this.pedidos = actualizados;
                this._ids = new Set(actualizados.map(p => p.id_pdomicilio));
                this.repartidores = data.repartidores;
                if (countNuevos > 0) {
                    this.nuevos += countNuevos;
                    setTimeout(() => { this.nuevos = Math.max(0, this.nuevos - countNuevos); }, 6000);
                }
            } catch(e) {}
            this.cargando = false;
        },

        async post(url, body) {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            return r.json();
        },

        async asignar(p) {
            if (!p._repSelected) return;
            const d = await this.post('/repartidor/asignar', { id_pdomicilio: p.id_pdomicilio, id_repartidor: p._repSelected });
            if (d.success) await this.refrescar();
        },
        async salir(p) {
            const d = await this.post('/repartidor/salir', { id_pdomicilio: p.id_pdomicilio });
            if (d.success) await this.refrescar();
        },
        async reasignar(p) {
            if (!p._repSelected) return;
            const d = await this.post('/repartidor/reasignar', { id_pdomicilio: p.id_pdomicilio, id_repartidor: p._repSelected });
            if (d.success) await this.refrescar();
        },
        async entregar(p) {
            const d = await this.post('/repartidor/entregar', { id_pdomicilio: p.id_pdomicilio });
            if (d.success) await this.refrescar();
        },

        formatHora(dt) {
            if (!dt) return '';
            return new Date(dt.replace(' ', 'T')).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
        },
    };
}
</script>
@endsection
