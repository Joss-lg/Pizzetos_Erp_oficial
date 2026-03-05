@extends('layouts.app')

@section('content')
<div class="w-full p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Historial de Cierres de Caja</h2>
        <a href="{{ route('flujo.caja.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-700">
            Volver a Caja Actual
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">ID Caja</th>
                    <th class="px-6 py-4">Cajero</th>
                    <th class="px-6 py-4">Apertura</th>
                    <th class="px-6 py-4">Cierre</th>
                    <th class="px-6 py-4">Monto Final</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($cajas as $c)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-700">#{{ $c->id_caja }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $c->cajero_nombre }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($c->fecha_apertura)->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($c->fecha_cierre)->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">${{ number_format($c->monto_final, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('flujo.caja.pdf', $c->id_caja) }}" target="_blank" 
                           class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded shadow-sm text-xs font-bold transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Reimprimir PDF
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $cajas->links() }} {{-- Paginación de Laravel --}}
    </div>
</div>
@endsection