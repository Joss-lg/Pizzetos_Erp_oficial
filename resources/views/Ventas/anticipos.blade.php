@extends('layouts.app')

@section('content')
<div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-8 min-h-[400px]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#1e293b] tracking-tight">Pedidos Especiales / Anticipos</h2>
            <p class="text-sm text-gray-500 mt-1">Gestión de pedidos con fecha de entrega programada</p>
        </div>
        
        <form action="{{ route('ventas.anticipos') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="estado" class="border border-blue-500 text-gray-700 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white min-w-[140px]">
                <option value="pendientes" {{ $filtroEstado == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                <option value="completados" {{ $filtroEstado == 'completados' ? 'selected' : '' }}>Completados</option>
                <option value="cancelados" {{ $filtroEstado == 'cancelados' ? 'selected' : '' }}>Cancelados</option>
            </select>

            <button type="submit" class="bg-[#eab308] hover:bg-[#ca8a04] text-white px-5 py-2 rounded-md text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Actualizar
            </button>
        </form>
    </div>

    @if($anticipos->isEmpty())
        <div class="w-full border-2 border-dashed border-gray-200 rounded-lg flex flex-col items-center justify-center py-20 bg-gray-50/50">
            <p class="text-gray-600 text-[15px] font-semibold mb-1">No hay pedidos especiales {{ $filtroEstado }}</p>
            <p class="text-gray-400 text-sm">Los nuevos pedidos especiales aparecerán aquí</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($anticipos as $pedido)
                <div class="border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow relative bg-white">
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Folio #{{ $pedido->id_venta }}</span>
                            <h4 class="font-bold text-gray-900 text-lg leading-tight mt-1">{{ $pedido->nombre }} {{ $pedido->apellido }}</h4>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-200">
                            {{ ucfirst($filtroEstado) }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Venta:</span>
                            <span class="font-black text-green-600">${{ number_format($pedido->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Fecha Creación:</span>
                            <span class="text-gray-700 font-medium">{{ \Carbon\Carbon::parse($pedido->fecha_creacion)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-sm bg-amber-50 p-2 rounded border border-amber-100 mt-2">
                            <span class="text-amber-800 font-semibold">Entrega:</span>
                            <span class="text-amber-900 font-black">
                                {{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y H:i') : 'Por definir' }}
                            </span>
                        </div>
                    </div>

                    <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 rounded text-sm transition-colors border border-gray-300">
                        Ver Detalles
                    </button>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection