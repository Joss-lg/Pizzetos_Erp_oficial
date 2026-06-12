@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <a href="{{ route('mariscos.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium mb-6 transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Volver</a>
    <h2 class="text-2xl font-black text-gray-800 tracking-tight mb-8">Añadir Pizza de Mariscos</h2>

    <form action="{{ route('mariscos.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre <span class="text-red-500">*</span></label>
            <input type="text" name="nombre" required placeholder="Ej. Camaron" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción <span class="text-red-500">*</span></label>
            <input type="text" name="descripcion" required placeholder="Ej. Camaron que se duerme..." class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 outline-none">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tamaño <span class="text-red-500">*</span></label>
            <select name="id_tamañop" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 outline-none bg-white">
                <option value="" disabled selected>Selecciona un tamaño</option>
                @foreach($tamanos as $tamano) 
                    <option value="{{ $tamano->id_tamañop }}">{{ $tamano->tamano }}</option> 
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría <span class="text-red-500">*</span></label>
            <select name="id_cat" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 outline-none bg-white">
                <option value="" disabled selected>Selecciona una categoría</option>
                @foreach($categorias as $cat) <option value="{{ $cat->id_cat }}">{{ $cat->descripcion }}</option> @endforeach
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2">Guardar Producto</button>
            <a href="{{ route('mariscos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold">Cancelar</a>
        </div>
    </form>
</div>
@endsection