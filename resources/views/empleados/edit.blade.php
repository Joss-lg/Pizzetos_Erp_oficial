@extends('layouts.app')

@section('content')
<div class="max-w-[1000px] mx-auto"> 
    <div class="mb-12 flex justify-between items-end">
        <div>
            <h2 class="text-6xl font-black text-gray-900 italic tracking-tighter uppercase leading-[0.8]">Editar<br>Colaborador</h2>
            <div class="h-2 w-24 bg-[#eab308] mt-6 rounded-full"></div>
        </div>
        <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-black font-black uppercase tracking-widest text-xs mb-4">← Cancelar</a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden p-12">
        
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl">
                <ul>@foreach ($errors->all() as $error) <li>• {{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('empleados.update', $empleado->id_emp) }}" method="POST">
            @csrf
            @method('PUT') 
            
            <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                
                <div class="col-span-2">
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Nombre Completo</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]" required>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Usuario</label>
                    <input type="text" name="nickName" value="{{ old('nickName', $empleado->nickName) }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]" required>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $empleado->telefono) }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">
                        Nueva Contraseña <span class="text-gray-300 normal-case tracking-normal">(Opcional: Deja en blanco para mantener la actual)</span>
                    </label>
                    <input type="password" name="password" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] transition-all" placeholder="Escribe para cambiar la contraseña...">
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $empleado->direccion) }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]">
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Cargo</label>
                    <select name="id_ca" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] appearance-none" required>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id_ca }}" {{ (old('id_ca', $empleado->id_ca) == $cargo->id_ca) ? 'selected' : '' }}>
                                {{ $cargo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Sucursal</label>
                    <select name="id_suc" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] appearance-none" required>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id_suc }}" {{ (old('id_suc', $empleado->id_suc) == $sucursal->id_suc) ? 'selected' : '' }}>
                                {{ $sucursal->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-12 flex justify-end">
                <button type="submit" class="bg-[#eab308] hover:bg-black hover:text-white text-black px-10 py-5 rounded-2xl font-black text-sm transition-all shadow-xl shadow-yellow-500/20 uppercase tracking-[0.2em]">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection