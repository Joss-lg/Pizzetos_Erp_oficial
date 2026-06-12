@extends('layouts.app')

@section('content')
<div x-data="{ mostrarModalEliminar: false, formAccion: '' }" class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-8 relative">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Cargos y Permisos</h2>
            <p class="text-sm text-gray-500 mt-1">Gestiona los cargos y sus permisos del sistema</p>
        </div>
        <a href="{{ route('cargos.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg> Agregar Cargo
        </a>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="w-full text-center border-collapse">
            <thead>
                <tr class="text-gray-500 uppercase text-[10px] tracking-widest font-bold bg-gray-50">
                    <th rowspan="2" class="px-6 py-4 border-b border-r text-left align-middle w-1/5">CARGO</th>
                    <th colspan="4" class="px-2 py-2 border-b border-r bg-blue-50/50">PRODUCTOS</th>
                    <th colspan="4" class="px-2 py-2 border-b border-r bg-green-50/50">EMPLEADOS</th>
                    <th colspan="4" class="px-2 py-2 border-b border-r bg-yellow-50/50">VENTAS</th>
                    <th colspan="4" class="px-2 py-2 border-b border-r bg-purple-50/50">RECURSOS</th>
                    <th rowspan="2" class="px-6 py-4 border-b align-middle">ACCIONES</th>
                </tr>
                <tr class="text-gray-500 uppercase text-[10px] tracking-widest font-bold bg-gray-50">
                    <th class="py-2 border-b border-r bg-blue-50/50">C</th> <th class="py-2 border-b border-r bg-blue-50/50">M</th> <th class="py-2 border-b border-r bg-blue-50/50">E</th> <th class="py-2 border-b border-r bg-blue-50/50">V</th>
                    <th class="py-2 border-b border-r bg-green-50/50">C</th> <th class="py-2 border-b border-r bg-green-50/50">M</th> <th class="py-2 border-b border-r bg-green-50/50">E</th> <th class="py-2 border-b border-r bg-green-50/50">V</th>
                    <th class="py-2 border-b border-r bg-yellow-50/50">C</th> <th class="py-2 border-b border-r bg-yellow-50/50">M</th> <th class="py-2 border-b border-r bg-yellow-50/50">E</th> <th class="py-2 border-b border-r bg-yellow-50/50">V</th>
                    <th class="py-2 border-b border-r bg-purple-50/50">C</th> <th class="py-2 border-b border-r bg-purple-50/50">M</th> <th class="py-2 border-b border-r bg-purple-50/50">E</th> <th class="py-2 border-b border-r bg-purple-50/50">V</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @if($cargos->isEmpty())
                    <tr><td colspan="18" class="px-6 py-8 text-center text-gray-400 italic">No hay cargos registrados.</td></tr>
                @endif
                @foreach($cargos as $cargo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-left font-bold text-gray-900 border-r">{{ $cargo->nombre }}</td>
                        
                        @php 
                            $bloques = [
                                'producto' => 'bg-blue-50/10', 
                                'empleado' => 'bg-green-50/10', 
                                'venta' => 'bg-yellow-50/10', 
                                'recurso' => 'bg-purple-50/10'
                            ];
                            $acciones = ['crear', 'modificar', 'eliminar', 'ver'];
                        @endphp

                        @foreach($bloques as $seccion => $bgClass)
                            @foreach($acciones as $accion)
                                @php $columnaBD = $accion . '_' . $seccion; @endphp
                                <td class="py-4 border-r {{ $bgClass }}">
                                    @if($cargo->$columnaBD == 1)
                                        <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </td>
                            @endforeach
                        @endforeach
                        
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('cargos.edit', $cargo->id_ca) }}" class="text-blue-500 hover:text-blue-700 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>
                                <button type="button" @click="formAccion = '{{ route('cargos.destroy', $cargo->id_ca) }}'; mostrarModalEliminar = true" class="text-red-500 hover:text-red-700 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-xs text-gray-500">
        <p>Leyenda:</p>
        <p>C = Crear | M = Modificar | E = Eliminar | V = Ver</p>
    </div>

    <div x-show="mostrarModalEliminar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 overflow-hidden text-center" @click.away="mostrarModalEliminar = false">
            <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">¿Eliminar Cargo?</h3>
            <div class="flex gap-3 mt-6">
                <button @click="mostrarModalEliminar = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition-colors text-sm">Cancelar</button>
                <form :action="formAccion" method="POST" class="flex-1">@csrf @method('DELETE')<button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl shadow-sm">Sí, Eliminar</button></form>
            </div>
        </div>
    </div>
</div>
@endsection