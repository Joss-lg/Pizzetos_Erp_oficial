@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    
    <a href="{{ route('costillas.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium mb-6 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver
    </a>

    <h2 class="text-2xl font-black text-gray-800 tracking-tight mb-8">Añadir Costillas</h2>

    <form action="{{ route('costillas.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Orden <span class="text-red-500">*</span></label>
            <input type="text" name="orden" required placeholder="Ej. Medio Costillar, Costillar Entero..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 focus:border-amber-400 outline-none text-gray-600 bg-white transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Precio ($) <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" name="precio" required placeholder="Ej. 180.00" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 focus:border-amber-400 outline-none text-gray-600 bg-white transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría <span class="text-red-500">*</span></label>
            <select name="id_cat" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-amber-400 focus:border-amber-400 outline-none text-gray-600 bg-white transition-all">
                <option value="" disabled selected>Selecciona una categoría</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id_cat }}">{{ $cat->descripcion }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-sm cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Guardar Costillas
            </button>
            <a href="{{ route('costillas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection