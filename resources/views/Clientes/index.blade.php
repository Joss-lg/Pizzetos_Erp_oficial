@extends('layouts.app')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<style>
/* ── Avatar ── */
.clie-avatar { width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:900;flex-shrink:0;letter-spacing:-0.5px; }

/* ── Row card (mobile) ── */
.clie-row-card { display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:14px;border:1px solid rgba(226,232,240,0.6);background:rgba(248,250,252,0.6);transition:all .15s;cursor:default; }
.clie-row-card:hover { background:rgba(255,255,255,0.9);box-shadow:0 4px 12px rgba(0,0,0,.07);transform:translateY(-1px); }
html.app-dark .clie-row-card { background:rgba(255,255,255,0.04)!important;border-color:rgba(255,255,255,0.08)!important; }
html.app-dark .clie-row-card:hover { background:rgba(255,255,255,0.08)!important;box-shadow:0 4px 16px rgba(0,0,0,.3)!important; }

/* ── Table desktop ── */
.clie-table th { padding:10px 16px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;border-bottom:1px solid rgba(226,232,240,0.6); }
.clie-table td { padding:10px 16px;vertical-align:middle; }
.clie-table tr { border-bottom:1px solid rgba(226,232,240,0.4);transition:background .12s; }
.clie-table tr:last-child { border-bottom:none; }
.clie-table tr:hover td { background:rgba(248,250,252,0.8); }
html.app-dark .clie-table th { color:#475569!important;border-color:rgba(255,255,255,0.07)!important; }
html.app-dark .clie-table tr { border-color:rgba(255,255,255,0.06)!important; }
html.app-dark .clie-table tr:hover td { background:rgba(255,255,255,0.04)!important; }

/* ── Action btns ── */
.clie-btn { width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;transition:all .15s;flex-shrink:0; }
.clie-btn-green  { background:rgba(34,197,94,.1);color:#16a34a; }
.clie-btn-green:hover  { background:rgba(34,197,94,.2);color:#15803d; }
.clie-btn-blue   { background:rgba(59,130,246,.1);color:#2563eb; }
.clie-btn-blue:hover   { background:rgba(59,130,246,.2);color:#1d4ed8; }
.clie-btn-red    { background:rgba(239,68,68,.1);color:#dc2626; }
.clie-btn-red:hover    { background:rgba(239,68,68,.2);color:#b91c1c; }
.clie-btn-orange { background:rgba(253,126,20,.1);color:#ea580c; }
.clie-btn-orange:hover { background:rgba(253,126,20,.2);color:#c2410c; }
html.app-dark .clie-btn-green  { background:rgba(34,197,94,.12)!important;color:#4ade80!important; }
html.app-dark .clie-btn-green:hover  { background:rgba(34,197,94,.22)!important; }
html.app-dark .clie-btn-blue   { background:rgba(59,130,246,.12)!important;color:#60a5fa!important; }
html.app-dark .clie-btn-blue:hover   { background:rgba(59,130,246,.22)!important; }
html.app-dark .clie-btn-red    { background:rgba(239,68,68,.12)!important;color:#f87171!important; }
html.app-dark .clie-btn-red:hover    { background:rgba(239,68,68,.22)!important; }
html.app-dark .clie-btn-orange { background:rgba(253,126,20,.12)!important;color:#fb923c!important; }
html.app-dark .clie-btn-orange:hover { background:rgba(253,126,20,.22)!important; }

/* ── Search input ── */
.clie-search { background:rgba(248,250,252,0.8);border:1.5px solid rgba(226,232,240,0.7);border-radius:12px;padding:9px 14px 9px 40px;font-size:13px;outline:none;transition:border-color .15s,box-shadow .15s;width:100%; }
.clie-search:focus { border-color:#fd7e14;box-shadow:0 0 0 3px rgba(253,126,20,.1); }
html.app-dark .clie-search { background:rgba(255,255,255,0.06)!important;border-color:rgba(255,255,255,0.12)!important;color:#f1f5f9!important; }
html.app-dark .clie-search::placeholder { color:#475569!important; }
html.app-dark .clie-search:focus { border-color:#fd7e14!important; }

/* ── Stats chip ── */
.clie-stat { display:flex;align-items:center;gap:6px;padding:5px 12px;border-radius:999px;font-size:12px;font-weight:700; }
</style>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded-xl shadow-lg flex items-center gap-3">
    <div class="bg-white rounded-full p-0.5">
        <svg class="w-4 h-4 text-[#00b300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
    </div>
    <span class="font-medium text-[15px]">{{ session('success') }}</span>
    <button @click="show = false" class="ml-4 hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
@endif

@php
    $totalActivos   = $clientes->total() > 0 ? $clientes->getCollection()->where('status', 1)->count() : 0;
    $totalInactivos = $clientes->total() > 0 ? $clientes->getCollection()->where('status', 0)->count() : 0;
    $avatarColors = [
        'bg-orange-500','bg-blue-500','bg-purple-500','bg-green-500',
        'bg-rose-500','bg-cyan-500','bg-amber-500','bg-indigo-500',
    ];
@endphp

<div x-data="{
        mostrarModalEstado: false, formAccionEstado: '', accionTexto: '', colorBoton: '',
        mostrarModalDirecciones: false, formAccionDireccion: '',
        clienteActivo: '', clienteId: null, direccionesActivas: [],
        searchQuery: '{{ request('buscar') }}',
        checkMatch(nombre, telefono) {
            if (this.searchQuery.trim() === '') return true;
            let removeAccents = (str) => str.normalize('NFD').replace(/[̀-ͯ]/g, '');
            let q = removeAccents(this.searchQuery.trim().toLowerCase());
            let n = removeAccents(nombre.toLowerCase());
            let t = telefono.toLowerCase();
            return n.includes(q) || t.includes(q);
        }
    }" class="w-full">

    {{-- ── Header ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

            {{-- Title + stats --}}
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#fd7e14] to-[#ffc107] flex items-center justify-center shadow-md shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight leading-none">Clientes</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="clie-stat bg-blue-50 text-blue-600">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                            {{ $clientes->total() }} total
                        </span>
                        <span class="clie-stat bg-green-50 text-green-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                            {{ $clientes->getCollection()->where('status',1)->count() }} activos
                        </span>
                        @if($clientes->getCollection()->where('status',0)->count() > 0)
                        <span class="clie-stat bg-red-50 text-red-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                            {{ $clientes->getCollection()->where('status',0)->count() }} inactivos
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Search + add --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto md:max-w-lg flex-1 justify-end">
                <form action="{{ route('clientes.index') }}" method="GET" class="relative w-full sm:max-w-xs">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="buscar" x-model="searchQuery" placeholder="Buscar cliente..." class="clie-search">
                    <a href="{{ route('clientes.index') }}" x-show="searchQuery.length > 0" x-cloak
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                </form>

                <a href="{{ route('clientes.create') }}"
                   class="bg-gradient-to-r from-[#fd7e14] to-[#ffc107] text-white w-full sm:w-auto px-5 py-2.5 rounded-xl text-sm font-black flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:brightness-105 transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Cliente
                </a>
            </div>
        </div>
    </div>

    {{-- ── Table / Cards ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="clie-table w-full">
                <thead>
                    <tr>
                        <th class="text-left w-10 pl-5">#</th>
                        <th class="text-left">Cliente</th>
                        <th class="text-left">Teléfono</th>
                        <th class="text-center">Status</th>
                        <th class="text-right pr-5">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $i => $clie)
                        @php
                            $initials = mb_strtoupper(mb_substr($clie->nombre, 0, 1) . mb_substr($clie->apellido ?? '', 0, 1));
                            $color    = $avatarColors[($clie->id_clie) % count($avatarColors)];
                        @endphp
                        <tr x-show="checkMatch('{{ addslashes($clie->nombre . ' ' . $clie->apellido) }}', '{{ addslashes($clie->telefono) }}')">

                            <td class="pl-5 text-xs text-gray-400 font-bold w-10">{{ $clientes->firstItem() + $i }}</td>

                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="clie-avatar {{ $color }} text-white shrink-0">{{ $initials }}</div>
                                    <div>
                                        <p class="text-[14px] font-black text-gray-900 leading-none">{{ $clie->nombre }} {{ $clie->apellido }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">ID #{{ $clie->id_clie }}</p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center gap-2 text-gray-500 text-[13px]">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $clie->telefono ?: '—' }}
                                </div>
                            </td>

                            <td class="text-center">
                                @if($clie->status == 1)
                                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 font-bold px-3 py-1 rounded-full text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 font-bold px-3 py-1 rounded-full text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="pr-5">
                                <div class="flex justify-end gap-2 items-center">
                                    <button type="button" title="Gestionar Direcciones"
                                        @click="clienteId = {{ $clie->id_clie }}; clienteActivo = '{{ addslashes($clie->nombre . ' ' . $clie->apellido) }}'; direccionesActivas = {{ json_encode($todasDirecciones[$clie->id_clie] ?? []) }}; formAccionDireccion = '{{ route('clientes.storeDireccion', $clie->id_clie) }}'; mostrarModalDirecciones = true"
                                        class="clie-btn clie-btn-green">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </button>

                                    <a href="{{ route('clientes.edit', $clie->id_clie) }}" title="Editar" class="clie-btn clie-btn-blue">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    @if(Auth::user()->id_ca == 1)
                                        @if($clie->status == 1)
                                        <button type="button" title="Desactivar"
                                            @click="formAccionEstado = '{{ route('clientes.destroy', $clie->id_clie) }}'; accionTexto = 'Desactivar'; colorBoton = 'bg-red-600 hover:bg-red-700'; mostrarModalEstado = true"
                                            class="clie-btn clie-btn-red">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </button>
                                        @else
                                        <button type="button" title="Activar"
                                            @click="formAccionEstado = '{{ route('clientes.activar', $clie->id_clie) }}'; accionTexto = 'Activar'; colorBoton = 'bg-green-600 hover:bg-green-700'; mostrarModalEstado = true"
                                            class="clie-btn clie-btn-orange">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-700 text-sm">Sin resultados</p>
                                        <p class="text-xs text-gray-400 mt-1">No se encontraron clientes con esa búsqueda</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden p-3 space-y-2">
            @forelse($clientes as $i => $clie)
                @php
                    $initials = mb_strtoupper(mb_substr($clie->nombre, 0, 1) . mb_substr($clie->apellido ?? '', 0, 1));
                    $color    = $avatarColors[($clie->id_clie) % count($avatarColors)];
                @endphp
                <div x-show="checkMatch('{{ addslashes($clie->nombre . ' ' . $clie->apellido) }}', '{{ addslashes($clie->telefono) }}')"
                     class="clie-row-card">

                    <div class="clie-avatar {{ $color }} text-white text-[13px]">{{ $initials }}</div>

                    <div class="flex-1 min-w-0">
                        <p class="text-[14px] font-black text-gray-900 truncate leading-none">{{ $clie->nombre }} {{ $clie->apellido }}</p>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            <span class="text-[12px] text-gray-500 flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $clie->telefono ?: '—' }}
                            </span>
                            @if($clie->status == 1)
                                <span class="text-[10px] font-bold bg-green-50 text-green-600 px-2 py-0.5 rounded-full flex items-center gap-1"><span class="w-1 h-1 rounded-full bg-green-500 inline-block"></span>Activo</span>
                            @else
                                <span class="text-[10px] font-bold bg-red-50 text-red-500 px-2 py-0.5 rounded-full flex items-center gap-1"><span class="w-1 h-1 rounded-full bg-red-400 inline-block"></span>Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-1.5 shrink-0">
                        <button type="button"
                            @click="clienteId = {{ $clie->id_clie }}; clienteActivo = '{{ addslashes($clie->nombre . ' ' . $clie->apellido) }}'; direccionesActivas = {{ json_encode($todasDirecciones[$clie->id_clie] ?? []) }}; formAccionDireccion = '{{ route('clientes.storeDireccion', $clie->id_clie) }}'; mostrarModalDirecciones = true"
                            class="clie-btn clie-btn-green">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                        <a href="{{ route('clientes.edit', $clie->id_clie) }}" class="clie-btn clie-btn-blue">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        @if(Auth::user()->id_ca == 1)
                            @if($clie->status == 1)
                            <button type="button"
                                @click="formAccionEstado = '{{ route('clientes.destroy', $clie->id_clie) }}'; accionTexto = 'Desactivar'; colorBoton = 'bg-red-600 hover:bg-red-700'; mostrarModalEstado = true"
                                class="clie-btn clie-btn-red">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            </button>
                            @else
                            <button type="button"
                                @click="formAccionEstado = '{{ route('clientes.activar', $clie->id_clie) }}'; accionTexto = 'Activar'; colorBoton = 'bg-green-600 hover:bg-green-700'; mostrarModalEstado = true"
                                class="clie-btn clie-btn-orange">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-16 flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="font-black text-gray-700 text-sm">Sin resultados</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="border-t border-gray-100 px-5 py-3 flex justify-between items-center">
            @if ($clientes->onFirstPage())
                <span class="text-xs font-bold text-gray-300 bg-gray-50 px-4 py-2 rounded-lg border border-gray-100 cursor-not-allowed select-none">← Anterior</span>
            @else
                <a href="{{ $clientes->appends(request()->query())->previousPageUrl() }}" class="text-xs font-bold text-gray-600 bg-white hover:bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 transition-colors shadow-sm">← Anterior</a>
            @endif

            <span class="text-xs font-black text-gray-500 tracking-wide">
                Pág. {{ $clientes->currentPage() }} / {{ $clientes->lastPage() }}
                <span class="text-gray-400 font-medium"> · {{ $clientes->total() }} clientes</span>
            </span>

            @if ($clientes->hasMorePages())
                <a href="{{ $clientes->appends(request()->query())->nextPageUrl() }}" class="text-xs font-bold text-gray-600 bg-white hover:bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 transition-colors shadow-sm">Siguiente →</a>
            @else
                <span class="text-xs font-bold text-gray-300 bg-gray-50 px-4 py-2 rounded-lg border border-gray-100 cursor-not-allowed select-none">Siguiente →</span>
            @endif
        </div>
    </div>

    {{-- ── Modal Estado ── --}}
    <div x-show="mostrarModalEstado" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" @click.away="mostrarModalEstado = false">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-1 tracking-tight" x-text="'¿' + accionTexto + ' Cliente?'"></h3>
            <p class="text-sm text-gray-500 mb-6">Esta acción cambiará el estado del cliente en el sistema.</p>
            <div class="flex gap-3">
                <button @click="mostrarModalEstado = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition-colors text-sm">Cancelar</button>
                <form :action="formAccionEstado" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button type="submit" :class="colorBoton" class="w-full text-white font-bold py-3 rounded-xl shadow-sm text-sm transition-colors" x-text="'Sí, ' + accionTexto"></button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Modal Direcciones ── --}}
    <div x-show="mostrarModalDirecciones" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl flex flex-col" @click.away="mostrarModalDirecciones = false">

            <div class="p-5 border-b flex justify-between items-center sticky top-0 bg-white z-10 rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Direcciones</h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="clienteActivo"></p>
                </div>
                <button @click="mostrarModalDirecciones = false" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 pb-2">
                <h4 class="font-black text-gray-700 text-sm mb-3 uppercase tracking-wide">Registradas</h4>
                <div class="space-y-2 max-h-48 overflow-y-auto mb-6 pr-1">
                    <template x-for="dir in direccionesActivas" :key="dir.id_dir">
                        <div class="border border-gray-100 rounded-xl p-3.5 relative bg-gray-50 flex gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800" x-text="dir.calle"></p>
                                <p class="text-xs text-gray-500 mt-0.5">Mzna <span x-text="dir.manzana"></span> · Lote <span x-text="dir.lote"></span> · <span x-text="dir.colonia"></span></p>
                                <p class="text-xs text-gray-400 italic mt-0.5" x-text="dir.referencia"></p>
                            </div>
                            <form :action="'/direcciones/' + dir.id_dir" method="POST" class="shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                </button>
                            </form>
                        </div>
                    </template>
                    <div x-show="direccionesActivas.length === 0" class="py-6 flex flex-col items-center gap-2 text-gray-400">
                        <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        <p class="text-xs font-medium">Sin direcciones registradas</p>
                    </div>
                </div>

                <h4 class="font-black text-gray-700 text-sm mb-4 pt-4 border-t uppercase tracking-wide">Nueva Dirección</h4>
                <form :action="formAccionDireccion" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Calle y número</label>
                        <input type="text" name="calle" required placeholder="Ej. Av. Principal 123" class="w-full border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Manzana</label>
                            <input type="text" name="manzana" placeholder="Mzna" class="w-full border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Lote</label>
                            <input type="text" name="lote" placeholder="Lote" class="w-full border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Colonia</label>
                        <input type="text" name="colonia" placeholder="Colonia" class="w-full border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Referencia</label>
                        <input type="text" name="referencia" placeholder="Punto de referencia" class="w-full border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-all">
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t mt-2 pb-4">
                        <button type="button" @click="mostrarModalDirecciones = false" class="px-5 py-2.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cerrar</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-black text-sm text-white bg-gradient-to-r from-[#fd7e14] to-[#ffc107] shadow-md hover:brightness-105 transition-all">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
