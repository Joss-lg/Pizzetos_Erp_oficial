@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-12">
    {{-- ENCABEZADO --}}
    <div class="mb-10">
        <h2 class="text-5xl font-black text-gray-900 italic tracking-tighter uppercase mb-2 leading-none">
            ¡Qué onda, {{ Auth::user()->nombre }}! 🍕
        </h2>
        <div class="h-1.5 w-32 bg-amber-400 rounded-full"></div>
        <p class="text-gray-400 font-bold uppercase tracking-[0.3em] text-xs italic mt-4">
            Resumen de hoy, {{ now()->format('d M, Y') }}
        </p>
    </div>

    {{-- GRID DE MÉTRICAS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        
        {{-- Card: Ventas del Día --}}
        <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all group">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center group-hover:bg-amber-400 group-hover:text-black transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase text-gray-300 tracking-[0.3em]">Finanzas</span>
            </div>
            <span class="text-gray-400 font-black text-[10px] uppercase tracking-[0.3em]">Ventas de hoy</span>
            <h3 class="text-4xl font-black text-gray-900 mt-2 tracking-tighter italic leading-none">
                ${{ number_format($ventasHoy, 2) }}
            </h3>
        </div>

        {{-- Card: Pedidos Pendientes --}}
        <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all group">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center group-hover:bg-amber-400 group-hover:text-black transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase text-gray-300 tracking-[0.3em]">Cocina</span>
            </div>
            <span class="text-gray-400 font-black text-[10px] uppercase tracking-[0.3em]">Pedidos en espera</span>
            <h3 class="text-4xl font-black text-gray-900 mt-2 tracking-tighter italic leading-none">
                {{ $pedidosPendientes }}
            </h3>
        </div>

        {{-- Card: Producto Estrella --}}
        <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all group">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center group-hover:bg-amber-400 group-hover:text-black transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase text-gray-300 tracking-[0.3em]">Top</span>
            </div>
            <span class="text-gray-400 font-black text-[10px] uppercase tracking-[0.3em]">Producto Estrella</span>
            <h3 class="text-2xl font-black text-gray-900 mt-2 tracking-tighter italic leading-none uppercase truncate">
                {{ $nombreEstrella }}
            </h3>
            <p class="text-[9px] font-bold text-amber-500 mt-1 uppercase">{{ $topProducto->total_cantidad ?? 0 }} unidades vendidas</p>
        </div>
    </div>

    {{-- TABLA DE ACTIVIDAD RECIENTE --}}
    <div class="bg-white p-12 rounded-[3.5rem] border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-10">
            <h4 class="text-2xl font-black text-gray-800 italic uppercase tracking-tighter">Últimos Tickets</h4>
            <div class="h-1.5 w-24 bg-amber-400 rounded-full"></div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] border-b border-gray-50">
                        <th class="pb-6">Folio</th>
                        <th class="pb-6">Cliente</th>
                        <th class="pb-6">Total</th>
                        <th class="pb-6 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ultimasVentas as $venta)
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="py-6 font-black text-sm text-gray-900 italic">#{{ $venta->id_venta }}</td>
                        <td class="py-6 text-sm text-gray-600 font-bold uppercase tracking-tight">{{ $venta->nombreClie ?? 'Mostrador' }}</td>
                        <td class="py-6 font-black text-sm text-gray-900">${{ number_format($venta->total, 2) }}</td>
                        <td class="py-6 text-right">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $venta->status == 1 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $venta->status == 1 ? 'Pagado' : 'Pendiente' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-gray-400 font-black uppercase text-[10px] tracking-[0.4em]">Sin movimientos hoy</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection