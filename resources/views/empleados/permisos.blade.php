@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">

    <div class="mb-12 flex items-end justify-between flex-wrap gap-6">
        <div>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] mb-2">Gestión de Personal</p>
            <h2 class="text-5xl font-black text-gray-900 italic tracking-tighter uppercase leading-[0.8]">
                Permisos de {{ $empleado->nombre }}
            </h2>
            <div class="h-2 w-24 bg-[#eab308] mt-6 rounded-full"></div>
        </div>
        <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-gray-900 font-black text-xs uppercase tracking-widest transition-all">
            &larr; Volver al listado
        </a>
    </div>

    @if(session('success'))
    <div class="mb-8 flex items-center bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
        <p class="font-black text-green-800 uppercase tracking-widest text-xs">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-8 flex items-center bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
        <p class="font-black text-red-800 uppercase tracking-widest text-xs">{{ session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('empleados.permisos.update', $empleado->id_emp) }}" method="POST">
        @csrf

        <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-10 border-b border-gray-50 bg-gray-50/30">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em]">
                    Marca a qué puede entrar {{ $empleado->nombre }} en cada módulo del sistema
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0" x-data>
                    <thead>
                        <tr class="text-gray-400 uppercase text-[9px] tracking-[0.3em] font-black bg-gray-50/50">
                            <th class="px-8 py-6 border-b border-gray-50">Módulo</th>
                            @foreach($acciones as $accion)
                                <th class="px-6 py-6 border-b border-gray-50 text-center">{{ ucfirst($accion) }}</th>
                            @endforeach
                            <th class="px-8 py-6 border-b border-gray-50 text-center text-[#eab308]">Todos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($modulos as $clave => $nombre)
                            @php $actual = $permisosActuales->get($clave); @endphp
                            <tr class="hover:bg-yellow-50/30 transition-all">
                                <td class="px-8 py-8 font-black text-gray-900 italic uppercase tracking-tighter text-base">
                                    {{ $nombre }}
                                </td>
                                @foreach($acciones as $accion)
                                    <td class="px-6 py-8 text-center">
                                        <input type="checkbox"
                                               name="permisos[{{ $clave }}][{{ $accion }}]"
                                               value="1"
                                               class="fila-{{ $clave }} w-5 h-5 rounded-md border-gray-300 text-[#eab308] focus:ring-[#eab308]"
                                               {{ $actual && $actual->{$accion} ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                                <td class="px-8 py-8 text-center">
                                    <button type="button"
                                            onclick="document.querySelectorAll('.fila-{{ $clave }}').forEach(c => c.checked = !c.checked)"
                                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-all">
                                        Todos
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-10 border-t border-gray-50 bg-gray-50/30 flex justify-end">
                <button type="submit" class="bg-[#eab308] hover:bg-black hover:text-white text-black px-10 py-4 rounded-2xl font-black text-xs transition-all shadow-xl shadow-yellow-500/20 uppercase tracking-[0.2em]">
                    Guardar Permisos
                </button>
            </div>
        </div>
    </form>
</div>
@endsection