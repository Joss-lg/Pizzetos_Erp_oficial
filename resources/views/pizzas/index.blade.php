@extends('layouts.app')

@section('content')
<div x-data="{ mostrarModalEliminar: false, formAccion: '' }" class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8 relative">
    
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-50">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Pizzas</h2>
            <p class="text-sm text-gray-500 mt-1">Gestiona los productos de pizzas</p>
        </div>
        
        <a href="{{ route('pizzas.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Añadir
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-400 uppercase text-[10px] tracking-widest font-bold border-b border-gray-100">
                    <th class="px-6 py-4 font-semibold">Especialidad</th>
                    <th class="px-6 py-4 font-semibold">Tamaño</th>
                    <th class="px-6 py-4 font-semibold">Categoría</th>
                    <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @if($pizzas->isEmpty())
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">No hay pizzas registradas por el momento.</td>
                    </tr>
                @endif

                @foreach($pizzas as $pizza)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-900 font-bold text-sm">{{ $pizza->especialidad }}</td>
                        <td class="px-6 py-4 text-blue-500 font-medium text-sm">{{ $pizza->tamano }}</td>
                        <td class="px-6 py-4 text-gray-400 italic text-sm">{{ $pizza->categoria }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                
                                <a href="{{ route('pizzas.edit', $pizza->id_pizza) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <button type="button" @click="formAccion = '{{ route('pizzas.destroy', $pizza->id_pizza) }}'; mostrarModalEliminar = true" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="mostrarModalEliminar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 overflow-hidden text-center" @click.away="mostrarModalEliminar = false" x-transition.scale.origin.bottom>
            
            <div class="w-16 h-16 rounded-full bg-red-50 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">¿Eliminar Pizza?</h3>
            <p class="text-gray-500 text-sm mb-6">Esta acción no se puede deshacer. La pizza desaparecerá permanentemente de tu menú.</p>
            
            <div class="flex gap-3">
                <button @click="mostrarModalEliminar = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition-colors text-sm">
                    Cancelar
                </button>
                
                <form :action="formAccion" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection