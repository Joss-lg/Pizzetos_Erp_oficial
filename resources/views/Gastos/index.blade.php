@extends('layouts.app')

@section('content')
<style>
    .modal-gasto {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 50;
    }

    .modal-gasto.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-gasto__overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.35);
        backdrop-filter: blur(4px);
    }

    .modal-gasto__panel {
        position: relative;
        z-index: 1;
        width: min(100%, 32rem);
        margin: 0 auto;
    }

    .gastos-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-left: 5px solid #eab308;
        border-radius: 1.4rem;
        box-shadow: 0 14px 30px -18px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }

    .btn-nuevo-gasto {
        transition: all 0.2s ease;
        box-shadow: 0 6px 14px -6px rgba(234, 179, 8, 0.7);
    }

    .btn-nuevo-gasto:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 18px -10px rgba(234, 179, 8, 0.8);
        background-color: #d19a00;
    }

    .btn-nuevo-gasto:active {
        transform: scale(0.96);
    }

    .gastos-table thead th {
        background: #f8fafc;
        font-size: 0.72rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
    }

    .gastos-table tbody td {
        vertical-align: middle;
    }

    .gastos-table tbody tr:hover {
        background: #f9fafb;
    }

    .gastos-table__desc-text {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.2;
    }

    .gastos-table__meta {
        color: #64748b;
        font-size: 0.78rem;
    }

    .gastos-table__amount {
        color: #dc2626;
        font-weight: 700;
        white-space: nowrap;
    }

    .gastos-table__action-btn {
        transition: all 0.2s ease;
    }

    .gastos-table__action-btn:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .app-dark .gastos-card {
        background: #182235 !important;
        border-color: #2f3c53 !important;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.18) !important;
    }

    .app-dark .gastos-table thead th {
        background: #131c2e !important;
        color: #94a3b8 !important;
    }

    .app-dark .gastos-table tbody tr:hover {
        background: #1d2940 !important;
    }

    .app-dark .gastos-table__desc-text,
    .app-dark .text-\[\#1e293b\],
    .app-dark .text-gray-800,
    .app-dark .text-red-800 {
        color: #f8fafc !important;
    }

    .app-dark .gastos-table__meta,
    .app-dark .text-gray-500,
    .app-dark .text-gray-400,
    .app-dark .text-red-600 {
        color: #94a3b8 !important;
    }

    .app-dark .modal-gasto__panel .bg-white,
    .app-dark .modal-gasto__panel .ring-1,
    .app-dark .modal-gasto__panel .ring-gray-200 {
        background: #182235 !important;
        --tw-ring-color: #334155 !important;
    }

    .app-dark .modal-gasto__panel button.text-gray-400:hover {
        background: #1d2940 !important;
        color: #f8fafc !important;
    }
</style>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <div class="bg-white rounded-full p-0.5"><svg class="w-4 h-4 text-[#00b300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></div>
    <span class="font-medium text-[15px]">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-6 right-6 z-50 bg-red-600 text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <div class="bg-white rounded-full p-0.5"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg></div>
    <span class="font-medium text-[15px]">{{ session('error') }}</span>
</div>
@endif

<div class="w-full">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h2 class="text-2xl font-black text-[#1e293b] tracking-tight">Gastos del Día</h2>
            <p class="text-sm text-gray-500 mt-1">Registra las salidas de dinero de la caja actual</p>
        </div>
        @if($cajaAbierta)
            <button type="button" onclick="toggleModal('modal-gasto')" class="btn-nuevo-gasto inline-flex items-center justify-center gap-2 bg-[#eab308] hover:bg-[#ca8a04] text-white text-sm font-semibold px-4 py-2.5 rounded-xl self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Gasto
            </button>
        @endif
    </div>

    @if(!$cajaAbierta)
        <div class="bg-red-50 border border-red-200 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[300px]">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-red-800 mb-2">Caja Cerrada</h3>
            <p class="text-red-600 mb-6">No puedes registrar gastos sin haber abierto el turno primero.</p>
            <a href="{{ route('flujo.caja.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors">
                Ir a Abrir Caja
            </a>
        </div>
    @else
        <div class="gastos-card">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-50 text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-800">Gastos del Turno Actual ( Total: ${{ number_format($gastos->sum('precio'), 2) }} )</h3>
            </div>

            @if($gastos->isEmpty())
                <div class="flex min-h-[240px] items-center justify-center px-6">
                    <p class="text-sm text-gray-400">No hay gastos registrados en este turno.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="gastos-table w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 text-left">HORA</th>
                                <th class="px-5 py-3 text-left">DESCRIPCIÓN / USUARIO</th>
                                <th class="px-5 py-3 text-right">MONTO</th>
                                <th class="px-5 py-3 text-right">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gastos as $gasto)
                                @php
                                    $usuarioGasto = 'Sistema';
                                    $descReal = $gasto->descripcion;
                                    
                                    if (str_contains($gasto->descripcion, 'Registró:')) {
                                        $partes = explode('|', $gasto->descripcion, 2);
                                        $usuarioGasto = trim(str_replace('Registró:', '', $partes[0]));
                                        $descReal = isset($partes[1]) ? trim($partes[1]) : $descReal;
                                    }
                                @endphp
                                <tr class="border-b border-gray-100 last:border-b-0">
                                    <td class="px-5 py-3 text-sm font-semibold text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($gasto->fecha)->format('h:i A') }}</td>
                                    <td class="px-5 py-3">
                                        <p class="gastos-table__desc-text">{{ $descReal }}</p>
                                        <p class="gastos-table__meta">{{ $usuarioGasto }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-right gastos-table__amount">-${{ number_format($gasto->precio, 2) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <form action="{{ route('gastos.destroy', $gasto->id_gastos) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este gasto?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="gastos-table__action-btn inline-flex items-center justify-center text-gray-400 rounded-md p-1.5" title="Eliminar Gasto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>

<div id="modal-gasto" class="modal-gasto">
    <div class="modal-gasto__overlay" onclick="toggleModal('modal-gasto')"></div>
    <div class="modal-gasto__panel p-4">
        <div class="w-full rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">Registrar Nuevo Gasto</h3>
                <button type="button" onclick="toggleModal('modal-gasto')" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-400 hover:bg-gray-50 hover:text-gray-700">
                    <span class="text-xl leading-none">×</span>
                </button>
            </div>
            <form action="{{ route('gastos.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-wide mb-1">Descripción <span class="text-red-500">*</span></label>
                    <input type="text" name="descripcion" required placeholder="Ej. Compra de jitomate..." class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-wide mb-1">Monto ($) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="precio" required placeholder="0.00" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400">
                </div>
                <div class="pt-1">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-[#eab308] hover:bg-[#ca8a04] text-white font-semibold py-2.5 px-4 rounded-xl transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Guardar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.toggle('active');
}
</script>
@endsection