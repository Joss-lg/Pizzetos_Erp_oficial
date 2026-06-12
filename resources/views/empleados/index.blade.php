@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">
    
    <div class="mb-12">
        <h2 class="text-6xl font-black text-gray-900 italic tracking-tighter uppercase leading-[0.8]">Personal</h2>
        <div class="h-2 w-24 bg-[#eab308] mt-6 rounded-full"></div>
    </div>

    @if(session('success'))
    <div class="mb-8 flex items-center bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
        <div class="text-green-500 mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-black text-green-800 uppercase tracking-widest text-xs">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-10 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em]">Listado oficial de colaboradores</p>
            
            <a href="{{ route('empleados.create') }}" class="bg-[#eab308] hover:bg-black hover:text-white text-black px-8 py-4 rounded-2xl font-black text-xs transition-all shadow-xl shadow-yellow-500/20 uppercase tracking-[0.2em] inline-block text-center decoration-0">
                + Registrar Empleado
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="text-gray-400 uppercase text-[9px] tracking-[0.3em] font-black bg-gray-50/50">
                        <th class="px-8 py-6 border-b border-gray-50">Nombre</th>
                        <th class="px-8 py-6 border-b border-gray-50">Dirección</th>
                        <th class="px-8 py-6 border-b border-gray-50">Teléfono</th>
                        <th class="px-8 py-6 border-b border-gray-50">Cargo</th>
                        <th class="px-8 py-6 border-b border-gray-50">Sucursal</th>
                        <th class="px-8 py-6 border-b border-gray-50">Usuario</th>
                        <th class="px-8 py-6 border-b border-gray-50 text-center">Estado</th>
                        <th class="px-8 py-6 border-b border-gray-50 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($empleados as $empleado)
                    <tr class="hover:bg-yellow-50/30 transition-all group">
                        
                        <td class="px-8 py-8 font-black text-gray-900 italic uppercase tracking-tighter text-lg">
                            {{ $empleado->nombre }}
                        </td>

                        <td class="px-8 py-8 text-sm text-gray-500 font-medium">
                            {{ $empleado->direccion ?? 'Sin dirección' }}
                        </td>

                        <td class="px-8 py-8">
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-[11px] font-black tracking-wider italic">
                                {{ $empleado->telefono }}
                            </span>
                        </td>

                        <td class="px-8 py-8">
                            <span class="text-gray-800 font-black text-sm uppercase italic">
                                {{ $empleado->cargo->nombre ?? 'Staff' }}
                            </span>
                        </td>

                        <td class="px-8 py-8">
                            <span class="text-[#eab308] font-black text-[10px] uppercase tracking-widest">
                                {{ $empleado->sucursal->nombre ?? 'Matriz' }}
                            </span>
                        </td>

                        <td class="px-8 py-8">
                            <span class="text-gray-400 font-bold text-xs">
                                @ {{ $empleado->nickName }}
                            </span>
                        </td>

                        <td class="px-8 py-8">
                            <div class="flex justify-center">
                                <form action="{{ route('empleados.status', $empleado->id_emp) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" onchange="this.form.submit()" {{ $empleado->status == 1 ? 'checked' : '' }}>
                                        
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 shadow-sm"></div>
                                        
                                        <span class="ml-3 inline-block w-16 text-left text-[10px] font-black uppercase tracking-widest {{ $empleado->status == 1 ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $empleado->status == 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </form>
                            </div>
                        </td>

                        <td class="px-8 py-8 text-right">
                            <div class="flex justify-end gap-2 transition-all">
                                
                                <a href="{{ route('empleados.edit', $empleado->id_emp) }}" 
                                   class="p-3 bg-white text-gray-400 hover:text-black hover:bg-[#eab308] rounded-xl shadow-sm border border-gray-100 transition-all flex items-center justify-center"
                                   title="Editar colaborador">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>

                                <form action="{{ route('empleados.destroy', $empleado->id_emp) }}" method="POST" 
                                      onsubmit="return confirm('¿Estás SEGURO de eliminar a {{ $empleado->nombre }}? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <button type="submit" 
                                            class="p-3 bg-white text-gray-400 hover:text-white hover:bg-black rounded-xl shadow-sm border border-gray-100 transition-all flex items-center justify-center"
                                            title="Eliminar colaborador">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            <div class="flex flex-col items-center gap-4">
                                <span class="bg-gray-100 p-4 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </span>
                                No hay empleados registrados aún.
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