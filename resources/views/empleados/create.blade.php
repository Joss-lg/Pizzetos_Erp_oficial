@extends('layouts.app')

@section('content')
<div class="max-w-[1000px] mx-auto"> 
    <div class="mb-12 flex justify-between items-end">
        <div>
            <h2 class="text-6xl font-black text-gray-900 italic tracking-tighter uppercase leading-[0.8]">Nuevo<br>Colaborador</h2>
            <div class="h-2 w-24 bg-[#eab308] mt-6 rounded-full"></div>
        </div>
        <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-black font-black uppercase tracking-widest text-xs mb-4">
            ← Cancelar y volver
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden p-12">
        
        @if ($errors->any())
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm leading-5 font-black text-red-800 uppercase tracking-wider">
                            No se pudo guardar
                        </h3>
                        <div class="mt-2 text-sm text-red-700 font-medium">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                
                <div class="col-span-2">
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Nombre Completo</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] transition-all" placeholder="Ej. Juan Pérez" required>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Usuario (Nick)</label>
                    <input type="text" name="nickName" value="{{ old('nickName') }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]" placeholder="@juanp" required>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]" placeholder="55 1234 5678" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Contraseña</label>
                    <input type="password" name="password" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] transition-all" placeholder="Escribe una contraseña segura" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308]" placeholder="Calle Principal #123">
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Cargo</label>
                    <select name="id_ca" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] appearance-none" required>
                        <option value="" disabled {{ old('id_ca') ? '' : 'selected' }}>Seleccionar...</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id_ca }}" {{ old('id_ca') == $cargo->id_ca ? 'selected' : '' }}>
                                {{ $cargo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-2">Sucursal</label>
                    <select name="id_suc" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-gray-900 focus:ring-2 focus:ring-[#eab308] appearance-none" required>
                        <option value="" disabled {{ old('id_suc') ? '' : 'selected' }}>Seleccionar...</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id_suc }}" {{ old('id_suc') == $sucursal->id_suc ? 'selected' : '' }}>
                                {{ $sucursal->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-12 flex justify-end">
                <button type="submit" class="bg-[#eab308] hover:bg-black hover:text-white text-black px-10 py-5 rounded-2xl font-black text-sm transition-all shadow-xl shadow-yellow-500/20 uppercase tracking-[0.2em]">
                    Guardar Registro
                </button>
            </div>

        </form>
    </div>
</div>
@endsection