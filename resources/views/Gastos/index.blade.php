@extends('layouts.app')

@section('content')
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
    <div class="mb-8">
        <h2 class="text-2xl font-black text-[#1e293b] tracking-tight">Gastos del Día</h2>
        <p class="text-sm text-gray-500 mt-1">Registra las salidas de dinero de la caja actual</p>
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                <h3 class="font-bold text-gray-800 mb-5 border-b pb-3">Registrar Nuevo Gasto</h3>
                <form action="{{ route('gastos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Descripción <span class="text-red-500">*</span></label>
                        <input type="text" name="descripcion" required placeholder="Ej. Compra de jitomate, Pago de luz..." class="w-full border border-gray-300 rounded p-2.5 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Monto ($) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="precio" required placeholder="0.00" class="w-full border border-gray-300 rounded p-2.5 outline-none focus:ring-1 focus:ring-amber-400 focus:border-amber-400 text-sm">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#eab308] hover:bg-[#ca8a04] text-white font-bold py-2.5 rounded-md transition-colors text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg> Guardar Gasto
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-5 border-b pb-3">
                    <h3 class="font-bold text-gray-800">Gastos del Turno Actual</h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-semibold">Total: ${{ number_format($gastos->sum('precio'), 2) }}</span>
                </div>

                @if($gastos->isEmpty())
                    <div class="w-full border border-dashed border-red-200 rounded-lg flex flex-col items-center justify-center py-16 bg-red-50/30">
                        <p class="text-red-500 text-[15px] font-semibold">No hay gastos registrados en esta caja</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 uppercase text-[10px] tracking-widest font-bold border-b border-gray-100">
                                    <th class="px-4 py-3 font-semibold">FECHA / HORA</th>
                                    <th class="px-4 py-3 font-semibold">REGISTRADO POR</th>
                                    <th class="px-4 py-3 font-semibold">DESCRIPCIÓN</th>
                                    <th class="px-4 py-3 font-semibold text-right">MONTO</th>
                                    <th class="px-4 py-3 font-semibold text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($gastos as $gasto)
                                    @php
                                        // Extraer el nombre usando el truco
                                        $usuarioGasto = 'Sistema';
                                        $descReal = $gasto->descripcion;
                                        
                                        if (str_contains($gasto->descripcion, 'Registró:')) {
                                            $partes = explode('|', $gasto->descripcion, 2);
                                            $usuarioGasto = trim(str_replace('Registró:', '', $partes[0]));
                                            $descReal = isset($partes[1]) ? trim($partes[1]) : $descReal;
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($gasto->fecha)->format('h:i A') }}</td>
                                        <td class="px-4 py-3 font-bold text-blue-600 whitespace-nowrap">{{ $usuarioGasto }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-800">{{ $descReal }}</td>
                                        <td class="px-4 py-3 font-black text-red-600 text-right">-${{ number_format($gasto->precio, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('gastos.destroy', $gasto->id_gastos) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este gasto?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded" title="Eliminar Gasto">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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

        </div>
    @endif
</div>
@endsection