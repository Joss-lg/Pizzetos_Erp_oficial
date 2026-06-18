@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak] { display: none !important; }
    
    /* Variables de diseño Pizzetos */
    :root {
        --pizzetos-amber: #fbbf24;
        --pizzetos-radius: 20px;
    }

    .pizzetos-table-header {
        font-weight: 900 !important;
        font-style: italic !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        font-size: 10px !important;
        color: #94a3b8 !important;
    }

    .folio-badge {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-weight: 900;
        letter-spacing: -0.5px;
    }

    .resume-surface {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.7);
        background: linear-gradient(145deg, rgba(255,255,255,0.97), rgba(255,255,255,0.9));
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(12px);
    }

    .resume-surface::after {
        content: "";
        position: absolute;
        right: -2.5rem;
        bottom: -2.5rem;
        width: 8rem;
        height: 8rem;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.22), rgba(251, 191, 36, 0));
        pointer-events: none;
    }

    .resume-chip {
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(248, 250, 252, 0.86);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
    }

    .resume-row {
        transition: transform 160ms ease, background-color 160ms ease, box-shadow 160ms ease;
    }

    .resume-row:hover {
        transform: translateY(-1px);
        box-shadow: inset 0 0 0 1px rgba(251, 191, 36, 0.12);
    }

    .resume-action {
        transition: transform 140ms ease, box-shadow 140ms ease, background-color 140ms ease, color 140ms ease;
    }

    .resume-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .app-dark .resume-surface {
        border-color: #2f3c53 !important;
        background: linear-gradient(145deg, rgba(24,34,53,0.98), rgba(19,28,46,0.96)) !important;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.18) !important;
    }

    .app-dark .resume-surface::after {
        background: radial-gradient(circle, rgba(217, 119, 6, 0.2), rgba(217, 119, 6, 0));
    }

    .app-dark .resume-chip {
        border-color: #334155 !important;
        background: rgba(15, 23, 42, 0.58) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.03) !important;
    }

    .app-dark .resume-row:hover {
        box-shadow: inset 0 0 0 1px rgba(217, 119, 6, 0.18);
    }

    .app-dark .resume-action {
        background-color: #182235 !important;
        border-color: #334155 !important;
    }

    .app-dark .resume-action:hover {
        background-color: #1d2940 !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18) !important;
    }

    html.app-dark .resume-page-shell {
        background: radial-gradient(circle at top left, rgba(217,119,6,0.12), transparent 22%), linear-gradient(180deg, #0b1220 0%, #101828 100%) !important;
        border-color: #243041 !important;
    }

    html.app-dark .resume-page-shell,
    html.app-dark .resume-page-shell .overflow-x-auto,
    html.app-dark .resume-page-shell .bg-white,
    html.app-dark .resume-page-shell .bg-slate-50,
    html.app-dark .resume-page-shell .bg-slate-50\/50,
    html.app-dark .resume-page-shell .bg-slate-100,
    html.app-dark .resume-page-shell .bg-slate-100\/60,
    html.app-dark .resume-page-shell .bg-gray-50,
    html.app-dark .resume-page-shell .bg-gray-100 {
        background-color: #182235 !important;
    }

    html.app-dark .resume-page-shell .overflow-x-auto {
        border-color: #2f3c53 !important;
        box-shadow: inset 0 0 0 1px rgba(47,60,83,0.45), 0 18px 36px rgba(0,0,0,0.18) !important;
    }

    html.app-dark .resume-page-shell .pizzetos-table-header,
    html.app-dark .resume-page-shell thead tr,
    html.app-dark .resume-page-shell thead th {
        background-color: #131c2e !important;
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }

    html.app-dark .resume-page-shell tbody,
    html.app-dark .resume-page-shell tbody tr,
    html.app-dark .resume-page-shell tbody td {
        background-color: #182235 !important;
        border-color: #334155 !important;
    }

    html.app-dark .resume-page-shell tbody tr:hover,
    html.app-dark .resume-page-shell tbody tr:hover td,
    html.app-dark .resume-page-shell .resume-row:hover,
    html.app-dark .resume-page-shell .hover\:bg-white:hover {
        background-color: #1d2940 !important;
    }

    html.app-dark .resume-page-shell .text-slate-900,
    html.app-dark .resume-page-shell .text-slate-800,
    html.app-dark .resume-page-shell .text-\[\#0f172a\],
    html.app-dark .resume-page-shell .text-black,
    html.app-dark .resume-page-shell .folio-badge {
        color: #f8fafc !important;
    }

    html.app-dark .resume-page-shell .text-slate-700,
    html.app-dark .resume-page-shell .text-gray-700,
    html.app-dark .resume-page-shell .text-slate-500,
    html.app-dark .resume-page-shell .text-slate-400,
    html.app-dark .resume-page-shell .text-gray-500,
    html.app-dark .resume-page-shell .text-gray-400 {
        color: #94a3b8 !important;
    }

    html.app-dark .resume-page-shell select,
    html.app-dark .resume-page-shell textarea,
    html.app-dark .resume-page-shell input {
        background-color: #101828 !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    html.app-dark .resume-page-shell .bg-red-100,
    html.app-dark .resume-page-shell .bg-green-100,
    html.app-dark .resume-page-shell .bg-blue-100,
    html.app-dark .resume-page-shell .bg-amber-100,
    html.app-dark .resume-page-shell .bg-slate-200 {
        box-shadow: none !important;
    }

    html.app-dark .resume-page-shell .rounded-\[30px\],
    html.app-dark .resume-page-shell .rounded-\[35px\],
    html.app-dark .resume-page-shell .rounded-2xl,
    html.app-dark .resume-page-shell .rounded-xl {
        box-shadow: 0 18px 36px rgba(0,0,0,0.18) !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .resume-row,
        .resume-action {
            transition: none !important;
        }

        .resume-row:hover,
        .resume-action:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="resume-page-shell w-full bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.16),_transparent_24%),linear-gradient(180deg,_#ffffff_0%,_#f6f8fb_100%)] rounded-[45px] shadow-sm border border-gray-100 p-4 md:p-8 min-h-[400px]" x-data="historialApp()">
    <div class="resume-surface rounded-[28px] p-5 md:p-6 mb-8">
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">Filtros</p>
                <h3 class="text-2xl font-black text-[#0f172a] tracking-tighter italic uppercase">Refina el historial</h3>
                <p class="mt-1 text-xs font-bold text-slate-400 uppercase tracking-wider">Cruza fecha y estado para encontrar pedidos más rápido</p>
            </div>

            <form action="{{ route('ventas.resume') }}" method="GET" class="w-full lg:w-auto grid grid-cols-1 md:grid-cols-[minmax(180px,1fr)_minmax(190px,1fr)_auto] items-center gap-3">
            <select name="fecha" class="border border-gray-200 text-gray-700 font-bold rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-slate-50 min-w-[160px]">
                <option value="hoy" {{ ($filtroFecha ?? 'hoy') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ ($filtroFecha ?? '') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                <option value="mes" {{ ($filtroFecha ?? '') == 'mes' ? 'selected' : '' }}>Este mes</option>
                <option value="todos" {{ ($filtroFecha ?? '') == 'todos' ? 'selected' : '' }}>Todos los registros</option>
            </select>

            <select name="estado" class="border border-gray-200 text-gray-700 font-bold rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-slate-50 min-w-[160px]">
                <option value="todos" {{ ($filtroEstado ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                <option value="0" {{ ($filtroEstado ?? '') == '0' ? 'selected' : '' }}>Esperando / Abierta</option>
                <option value="1" {{ ($filtroEstado ?? '') == '1' ? 'selected' : '' }}>Pagado</option>
                <option value="3" {{ ($filtroEstado ?? '') == '3' ? 'selected' : '' }}>Cancelado</option>
            </select>

            <button type="submit" class="bg-amber-400 hover:bg-amber-500 text-black px-6 py-3 rounded-2xl text-sm font-black italic uppercase tracking-tighter flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Actualizar
            </button>
            </form>
        </div>
    </div>

    @if($ventas->isEmpty())
        <div class="resume-surface rounded-[34px] flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="bg-slate-50 p-6 rounded-full mb-4 shadow-inner">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <p class="text-slate-700 font-black uppercase italic text-base tracking-widest">No hay pedidos para mostrar</p>
            <p class="mt-2 text-slate-400 font-bold uppercase text-xs tracking-[0.22em]">Prueba otro rango o cambia el estado del filtro</p>
        </div>
    @else
        <div class="overflow-x-auto border border-gray-100 rounded-[30px] shadow-inner bg-slate-50/50">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/60 border-b border-gray-200 pizzetos-table-header">
                        <th class="px-6 py-5">Folio Virtual</th>
                        <th class="px-6 py-5">Fecha / Hora</th>
                        <th class="px-6 py-5">Atendió</th>
                        <th class="px-6 py-5">Cliente / Mesa</th>
                        <th class="px-6 py-5 text-center">Items</th>
                        <th class="px-6 py-5">Total</th>
                        <th class="px-6 py-5 text-center">Estado</th>
                        <th class="px-6 py-5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach($ventas as $venta)
                        @php
                            $usuarioVenta = 'Sistema';
                            $motivoCancelacion = '';
                            $repartidor = '-';
                            $esCortesia100 = false;
                            $esCortesia40 = false;

                            if ($venta->comentarios) {
                                $comentarioUpper = strtoupper($venta->comentarios);
                                $esCortesia100 = str_contains($comentarioUpper, 'CORTESÍA 100%') || str_contains($comentarioUpper, 'CORTESIA 100%') || str_contains($comentarioUpper, 'DESCUENTO 100%');
                                $esCortesia40 = str_contains($comentarioUpper, 'CORTESÍA 40%') || str_contains($comentarioUpper, 'CORTESIA 40%') || str_contains($comentarioUpper, 'DESCUENTO 40%');

                                $partes = explode('|', $venta->comentarios);
                                foreach($partes as $p) {
                                    $p = trim($p);
                                    if (str_contains($p, 'Atendió:')) {
                                        $usuarioVenta = trim(str_replace('Atendió:', '', $p));
                                    } elseif (str_contains($p, 'CANCELADO - Motivo:')) {
                                        $motivoCancelacion = trim(str_replace('CANCELADO - Motivo:', '', $p));
                                    } elseif (preg_match('/Repartidor:\s*([^|]+)/i', $p, $matches)) {
                                        $repartidor = trim($matches[1]);
                                    }
                                }
                            }

                            // BUSCADOR BLINDADO DE NOMBRE DE CLIENTE
                            $nClie = '';
                            $nombreExtraLlevar = '';
                            
                            if ($venta->tipo_servicio == 1) {
                                $nClie = ' - ' . $venta->cliente_display;
                            } elseif ($venta->tipo_servicio == 2) {
                                // AGREGAMOS EL NOMBRE SI ES PARA LLEVAR
                                if (isset($venta->nombreClie) && !empty(trim($venta->nombreClie))) {
                                    $nClie = ' - ' . mb_strtoupper(trim($venta->nombreClie));
                                    $nombreExtraLlevar = ' - ' . mb_strtoupper(trim($venta->nombreClie));
                                }
                            } elseif ($venta->tipo_servicio == 3) {
                                // Buscar nombre real por si el sistema traía "DOMICILIO" guardado por error
                                $clie_real = \Illuminate\Support\Facades\DB::table('PDomicilio')
                                    ->join('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
                                    ->where('PDomicilio.id_venta', $venta->id_venta)
                                    ->select('Clientes.nombre', 'Clientes.apellido')
                                    ->first();
                                if ($clie_real && !empty(trim($clie_real->nombre))) {
                                    $nClie = ' - ' . mb_strtoupper(trim($clie_real->nombre . ' ' . $clie_real->apellido));
                                } else {
                                    $nClie = ' - ' . $venta->cliente_display;
                                }
                            }
                        @endphp
                        <tr class="resume-row {{ $venta->status == 3 ? 'bg-red-50/50 grayscale-[0.5]' : 'hover:bg-white' }}">
                            <td class="px-6 py-6 font-black text-slate-900 folio-badge text-base">
                                #{{ $venta->folio_virtual }}
                            </td>
                            
                            <td class="px-6 py-6 text-slate-500 font-bold uppercase text-[11px] whitespace-nowrap leading-tight">
                                {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/y') }}<br>
                                <span class="text-slate-400">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('h:i A') }}</span>
                            </td>
                            
                            <td class="px-6 py-6 font-black text-blue-600 uppercase italic tracking-tighter text-xs">{{ $usuarioVenta }}</td>

                            <td class="px-6 py-6 text-slate-800 font-black uppercase text-xs tracking-tighter leading-tight">
                                {{ $venta->cliente_display }}{{ $nombreExtraLlevar }}
                                @if($venta->tipo_servicio == 3 && $repartidor != '-')
                                    <div class="mt-1 flex items-center gap-1 text-[9px] text-amber-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Repartidor: {{ $repartidor }}
                                    </div>
                                @endif
                                @if($motivoCancelacion)
                                    <div class="text-[10px] text-red-500 font-black mt-1 italic">Motivo: {{ $motivoCancelacion }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-6 text-center">
                                <span class="bg-slate-200 text-slate-700 font-black px-2.5 py-1 rounded-lg text-[10px]">
                                    {{ $venta->total_productos }}
                                </span>
                            </td>

                            <td class="px-6 py-6 font-black text-lg {{ $venta->status == 3 ? 'text-red-400 line-through' : 'text-slate-900 tracking-tighter' }}">
                                ${{ number_format($venta->total, 2) }}
                                
                                @if($esCortesia100)
                                    <div class="text-[9px] bg-red-100 text-red-600 px-2 py-0.5 rounded mt-1 w-max uppercase tracking-widest border border-red-200">Descuento 100%{{ $nClie }}</div>
                                @elseif($esCortesia40)
                                    <div class="text-[9px] bg-amber-100 text-amber-600 px-2 py-0.5 rounded mt-1 w-max uppercase tracking-widest border border-amber-200">Descuento 40%{{ $nClie }}</div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-6 text-center">
                                @if($venta->status == 0)
                                    <span class="bg-amber-100 text-amber-700 font-black px-3 py-1.5 rounded-full text-[10px] uppercase italic">Abierta</span>
                                @elseif($venta->status == 1)
                                    <span class="bg-green-100 text-green-700 font-black px-3 py-1.5 rounded-full text-[10px] uppercase italic">Pagado</span>
                                @elseif($venta->status == 3)
                                    <span class="bg-red-500 text-white font-black px-3 py-1.5 rounded-full text-[10px] uppercase italic shadow-sm">Anulado</span>
                                @endif
                            </td>

                            <td class="px-6 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    @if($venta->status != 3)
                                        
                                        {{-- ESTE ES EL BOTÓN QUE MOVIMOS: AHORA SIEMPRE ESTÁ VISIBLE (Para estado 0 y 1) --}}
                                        <a href="/venta/pos?edit={{ $venta->id_venta }}" title="Editar / Añadir Productos" class="resume-action text-blue-500 bg-white shadow-sm border border-gray-100 p-2 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        @if($venta->status == 0)
                                            <button @click="abrirPago({{ $venta->id_venta }}, {{ $venta->total }}, false, '{{ $venta->folio_virtual }}')" title="Cobrar" class="resume-action text-green-600 bg-white shadow-sm border border-gray-100 p-2 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </button>
                                        @endif

                                        @if($venta->status == 1)
                                            <button @click="abrirPago({{ $venta->id_venta }}, {{ $venta->total }}, true, '{{ $venta->folio_virtual }}')" title="Ajustar Pago" class="resume-action text-amber-600 bg-white shadow-sm border border-gray-100 p-2 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                            </button>
                                        @endif

                                        <button @click="abrirCancelar({{ $venta->id_venta }}, '{{ $venta->folio_virtual }}')" title="Cancelar" class="resume-action text-red-500 bg-white shadow-sm border border-gray-100 p-2 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endif

                                    <button @click="imprimirTicketPop({{ $venta->id_venta }})" title="Reimprimir" class="resume-action text-slate-400 bg-white shadow-sm border border-gray-100 p-2 rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- MODAL CANCELAR PEDIDO --}}
    <div x-show="modalCancelar" x-cloak class="fixed inset-0 bg-slate-900/80 z-[100] flex items-center justify-center p-4 backdrop-blur-md">
        <div class="bg-white rounded-[30px] shadow-2xl w-[400px] max-w-full flex flex-col overflow-hidden" @click.away="modalCancelar = false">
            <div class="bg-red-500 p-6 flex justify-between items-center text-white">
                <div>
                    <p class="text-[10px] font-black uppercase italic opacity-70">Anulación de Pedido</p>
                    <h2 class="text-xl font-black italic uppercase" x-text="'Folio: ' + folio_virtual_cancelar"></h2>
                </div>
                <button @click="modalCancelar = false" class="hover:rotate-90 transition-transform font-black text-2xl leading-none">&times;</button>
            </div>
            <div class="p-8 bg-white space-y-4 text-center">
                <p class="text-slate-500 font-bold uppercase italic text-[11px] leading-tight">Esta acción es irreversible y anulará el ingreso de caja.</p>
                <textarea x-model="motivo_cancelacion" rows="3" placeholder="Ingresa el motivo de cancelación..." class="w-full border-2 border-slate-100 bg-slate-50 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-red-400 transition-colors"></textarea>
            </div>
            <div class="p-6 bg-slate-50 flex gap-3">
                <button @click="modalCancelar = false" class="flex-1 bg-white border border-gray-200 text-slate-400 font-black italic uppercase text-[10px] py-4 rounded-2xl transition-all">Volver</button>
                <button @click="procesarCancelacion()" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black italic uppercase text-[10px] py-4 rounded-2xl shadow-lg shadow-red-100 transition-all">Anular Ticket</button>
            </div>
        </div>
    </div>

    {{-- MODAL MULTIPAGO CON CORTESÍAS --}}
    <div x-show="modalPago" x-cloak class="fixed inset-0 bg-slate-900/80 z-[100] flex items-center justify-center p-4 backdrop-blur-md">
        <div class="bg-white rounded-[35px] shadow-2xl w-[450px] max-w-full flex flex-col h-auto max-h-[90vh] overflow-hidden" @click.away="modalPago = false">
            <div :class="es_edicion_pago ? 'bg-blue-600' : 'bg-green-600'" class="p-6 flex justify-between items-center text-white relative">
                <div>
                    <p class="text-[10px] font-black uppercase italic opacity-70" x-text="es_edicion_pago ? 'Gestión de Cobro' : 'Procesar Pago'"></p>
                    <h2 class="text-xl font-black italic uppercase" x-text="'Folio: ' + folio_virtual_pago"></h2>
                </div>
                <button @click="modalPago = false" class="hover:rotate-90 transition-transform font-black text-2xl leading-none">&times;</button>
            </div>
            
            <div class="p-8 text-center bg-white border-b border-gray-50">
                {{-- BOTONES DE DESCUENTO --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <button @click="cortesia = 0; autoFillAfterCortesia()" :class="cortesia === 0 ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-colors shadow-sm">Sin Descuento</button>
                    <button @click="cortesia = 40; autoFillAfterCortesia()" :class="cortesia === 40 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-colors shadow-sm">Descuento 40%</button>
                    <button @click="cortesia = 100; autoFillAfterCortesia()" :class="cortesia === 100 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-colors shadow-sm">Descuento 100%</button>
                </div>

                <div class="text-[10px] font-black text-slate-400 uppercase italic mb-1 tracking-widest">Total a Cobrar</div>
                <div class="font-black text-slate-900 text-5xl leading-none tracking-tighter italic" x-text="'$' + getGranTotal().toFixed(2)"></div>
                
                {{-- Muestra el subtotal tachado si hay cortesía --}}
                <div x-show="cortesia > 0" class="text-sm font-bold text-amber-500 mt-2" x-cloak>
                    Subtotal Original: <span class="line-through" x-text="'$' + total_pago.toFixed(2)"></span> (-<span x-text="cortesia"></span>%)
                </div>

                <div class="mt-4 text-[11px] font-black uppercase italic inline-block px-4 py-1.5 rounded-full" 
                     :class="faltaPagar() === 0 ? 'bg-green-100 text-green-600' : (faltaPagar() < 0 ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-500')" 
                     x-text="faltaPagar() === 0 ? 'Monto completo asignado' : (faltaPagar() < 0 ? 'Cambio: $' + Math.abs(faltaPagar()).toFixed(2) : 'Faltante: $' + faltaPagar().toFixed(2))"></div>
            </div>

            {{-- OCULTA LOS PAGOS SI ES 100% CORTESÍA --}}
            <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50 space-y-4" x-show="cortesia !== 100">
                <div class="border-2 rounded-[25px] overflow-hidden transition-all bg-white" :class="pagos.efectivo.activo ? 'border-amber-400 shadow-lg shadow-amber-50' : 'border-slate-100'">
                    <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                        <input type="checkbox" x-model="pagos.efectivo.activo" @change="autoFillPago('efectivo')" class="w-5 h-5 rounded-lg border-gray-300 text-amber-400 focus:ring-amber-400">
                        <span class="font-black italic uppercase text-xs text-slate-700">Efectivo</span>
                    </label>
                    <div x-show="pagos.efectivo.activo" class="px-5 pb-5 pt-1 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 italic">Monto</label>
                                <input type="number" step="0.01" min="0" x-model.number="pagos.efectivo.monto" class="w-full border-2 border-slate-100 rounded-xl px-4 py-2 text-sm font-black focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-green-500 uppercase mb-1 italic">Recibido</label>
                                <input type="number" step="0.01" min="0" x-model.number="pagos.efectivo.entregado" class="w-full border-2 border-green-50 rounded-xl px-4 py-2 text-sm font-black bg-green-50 focus:outline-none focus:border-green-400">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-2 rounded-[25px] overflow-hidden transition-all bg-white" :class="pagos.tarjeta.activo ? 'border-amber-400 shadow-lg shadow-amber-50' : 'border-slate-100'">
                    <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                        <input type="checkbox" x-model="pagos.tarjeta.activo" @change="autoFillPago('tarjeta')" class="w-5 h-5 rounded-lg border-gray-300 text-amber-400 focus:ring-amber-400">
                        <span class="font-black italic uppercase text-xs text-slate-700">Tarjeta</span>
                    </label>
                    <div x-show="pagos.tarjeta.activo" class="px-5 pb-5 pt-1">
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 italic">Monto Tarjeta</label>
                        <input type="number" step="0.01" min="0" x-model.number="pagos.tarjeta.monto" class="w-full border-2 border-slate-100 rounded-xl px-4 py-2 text-sm font-black focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <div class="border-2 rounded-[25px] overflow-hidden transition-all bg-white" :class="pagos.transferencia.activo ? 'border-amber-400 shadow-lg shadow-amber-50' : 'border-slate-100'">
                    <label class="flex items-center gap-3 p-4 cursor-pointer select-none">
                        <input type="checkbox" x-model="pagos.transferencia.activo" @change="autoFillPago('transferencia')" class="w-5 h-5 rounded-lg border-gray-300 text-amber-400 focus:ring-amber-400">
                        <span class="font-black italic uppercase text-xs text-slate-700">Transferencia</span>
                    </label>
                    <div x-show="pagos.transferencia.activo" class="px-5 pb-5 pt-1 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 italic">Monto</label>
                                <input type="number" step="0.01" min="0" x-model.number="pagos.transferencia.monto" class="w-full border-2 border-slate-100 rounded-xl px-4 py-2 text-sm font-black focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-blue-500 uppercase mb-1 italic">Referencia</label>
                                <input type="text" x-model="pagos.transferencia.referencia" class="w-full border-2 border-blue-50 bg-blue-50 rounded-xl px-4 py-2 text-sm font-black focus:outline-none focus:border-blue-400">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white border-t border-gray-50 flex flex-col gap-2">
                <button @click="procesarPagoFinal()" :disabled="!pagosValidos() || isProcessing" :class="(!pagosValidos() || isProcessing) ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-black hover:bg-slate-800 text-white shadow-xl'" class="w-full font-black italic uppercase text-xs py-5 rounded-2xl transition-all active:scale-95">
                    <span x-show="!isProcessing" x-text="es_edicion_pago ? 'Actualizar Pago' : 'Confirmar Venta'"></span>
                    <span x-show="isProcessing">Procesando...</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('historialApp', () => ({
                modalPago: false,
                modalCancelar: false,
                id_venta_pago: null,
                id_venta_cancelar: null,
                folio_virtual_pago: '',
                folio_virtual_cancelar: '',
                total_pago: 0,
                cortesia: 0,
                es_edicion_pago: false,
                isProcessing: false,
                motivo_cancelacion: '',
                pagos: {
                    efectivo: { activo: false, monto: null, entregado: null },
                    tarjeta: { activo: false, monto: null },
                    transferencia: { activo: false, monto: null, referencia: '' }
                },

                imprimirTicketPop(id) {
                    const width = 400;
                    const height = 650;
                    const left = (window.screen.width / 2) - (width / 2);
                    const top = (window.screen.height / 2) - (height / 2);
                    
                    window.open(
                        '/venta/pos/ticket/' + id, 
                        'TicketPizzetos', 
                        `width=${width},height=${height},left=${left},top=${top},toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes`
                    );
                },

                abrirCancelar(id, folio) {
                    this.id_venta_cancelar = id;
                    this.folio_virtual_cancelar = folio;
                    this.motivo_cancelacion = '';
                    this.modalCancelar = true;
                },

                procesarCancelacion() {
                    if(!this.motivo_cancelacion.trim()) return alert("Ingresa el motivo.");
                    fetch("{{ route('ventas.cancelar') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}', id_venta: this.id_venta_cancelar, motivo: this.motivo_cancelacion })
                    }).then(r => r.json()).then(res => {
                        if(res.success) { window.location.reload(); } else { alert("Error: " + res.message); }
                    });
                },

                abrirPago(id, total, esEdicion, folio) {
                    this.id_venta_pago = id;
                    this.folio_virtual_pago = folio;
                    this.total_pago = parseFloat(total);
                    this.cortesia = 0; 
                    this.es_edicion_pago = esEdicion;
                    this.isProcessing = false;
                    this.pagos = {
                        efectivo: { activo: false, monto: null, entregado: null },
                        tarjeta: { activo: false, monto: null },
                        transferencia: { activo: false, monto: null, referencia: '' }
                    };
                    this.modalPago = true;
                },

                getGranTotal() {
                    let descontado = this.total_pago - (this.total_pago * (this.cortesia / 100));
                    return this.cortesia > 0 ? Math.ceil(descontado) : descontado;
                },

                autoFillAfterCortesia() {
                    if(this.cortesia === 100) {
                        this.pagos.efectivo.activo = false; this.pagos.efectivo.monto = null; this.pagos.efectivo.entregado = null;
                        this.pagos.tarjeta.activo = false; this.pagos.tarjeta.monto = null;
                        this.pagos.transferencia.activo = false; this.pagos.transferencia.monto = null;
                    } else {
                        ['efectivo', 'tarjeta', 'transferencia'].forEach(t => {
                            if(this.pagos[t].activo) this.autoFillPago(t);
                        });
                    }
                },

                autoFillPago(tipo) {
                    if(this.pagos[tipo].activo) {
                        let falta = this.faltaPagar();
                        if(falta > 0) this.pagos[tipo].monto = falta;
                    } else {
                        this.pagos[tipo].monto = null;
                    }
                },

                getTotalPagarInputs() {
                    let pE = this.pagos.efectivo.activo ? parseFloat(this.pagos.efectivo.monto || 0) : 0;
                    let pT = this.pagos.tarjeta.activo ? parseFloat(this.pagos.tarjeta.monto || 0) : 0;
                    let pTr = this.pagos.transferencia.activo ? parseFloat(this.pagos.transferencia.monto || 0) : 0;
                    return pE + pT + pTr;
                },

                faltaPagar() { 
                    return parseFloat((this.getGranTotal() - this.getTotalPagarInputs()).toFixed(2)); 
                },
                
                pagosValidos() { 
                    if (this.faltaPagar() !== 0) return false;
                    if (this.getGranTotal() === 0) return true; 
                    if (!this.pagos.efectivo.activo && !this.pagos.tarjeta.activo && !this.pagos.transferencia.activo) return false;
                    if (this.pagos.transferencia.activo && (!this.pagos.transferencia.referencia || this.pagos.transferencia.referencia.trim() === '')) return false;
                    return true;
                },

                procesarPagoFinal() {
                    if(!this.pagosValidos()) return;
                    this.isProcessing = true;

                    let pagosToSend = [];
                    if(this.cortesia !== 100) {
                        if(this.pagos.efectivo.activo && this.pagos.efectivo.monto > 0) {
                            pagosToSend.push({ id_metpago: 2, monto: this.pagos.efectivo.monto, entregado: this.pagos.efectivo.entregado || this.pagos.efectivo.monto });
                        }
                        if(this.pagos.tarjeta.activo && this.pagos.tarjeta.monto > 0) {
                            pagosToSend.push({ id_metpago: 1, monto: this.pagos.tarjeta.monto }); 
                        }
                        if(this.pagos.transferencia.activo && this.pagos.transferencia.monto > 0) {
                            pagosToSend.push({ id_metpago: 3, monto: this.pagos.transferencia.monto, referencia: this.pagos.transferencia.referencia });
                        }
                    }

                    let url = this.es_edicion_pago ? "{{ route('ventas.editar_pago') }}" : "{{ route('ventas.pagar') }}";

                    fetch(url, {
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ 
                            _token: '{{ csrf_token() }}', 
                            id_venta: this.id_venta_pago, 
                            pagos: pagosToSend,
                            cortesia: this.cortesia,
                            nuevo_total: this.getGranTotal()
                        })
                    }).then(r => r.json()).then(res => {
                        if(res.success) {
                            this.modalPago = false;
                            if(!this.es_edicion_pago) {
                                this.imprimirTicketPop(this.id_venta_pago);
                            }
                            setTimeout(() => { window.location.reload(); }, 1000);
                        } else { 
                            alert("Error: " + res.message); 
                            this.isProcessing = false;
                        }
                    });
                }
            }));
        });
    </script>
</div>
@endsection