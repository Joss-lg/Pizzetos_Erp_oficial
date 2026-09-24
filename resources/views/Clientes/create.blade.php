@extends('layouts.app')

@section('content')
<div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
    <a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium mb-6 transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Volver</a>
    
    <h2 class="text-2xl font-black text-gray-800 tracking-tight mb-8">Agregar Nuevo Cliente</h2>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf
        
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Información del Cliente</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-2">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" required placeholder="Nombre del cliente" class="w-full border border-gray-300 rounded p-2.5 outline-none focus:border-amber-400 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-2">Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido" placeholder="Apellido del cliente" class="w-full border border-gray-300 rounded p-2.5 outline-none focus:border-amber-400 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-2">Teléfono <span class="text-red-500">*</span></label>
                <input type="text" name="telefono" required placeholder="Número de teléfono" class="w-full border border-gray-300 rounded p-2.5 outline-none focus:border-amber-400 text-sm">
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-2 mt-8">Dirección de Entrega (Opcional)</h3>
        <p class="text-xs text-gray-500 mb-6 border-b pb-2">Puedes agregar una dirección ahora o hacerlo después.</p>
        
        <div class="space-y-4 mb-8">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Calle</label>
                <input type="text" name="calle" placeholder="Calle y número" class="w-full border rounded p-2.5 text-sm outline-none focus:border-amber-400">
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Manzana</label>
                    <input type="text" name="manzana" placeholder="Manzana" class="w-full border rounded p-2.5 text-sm outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Lote</label>
                    <input type="text" name="lote" placeholder="Lote" class="w-full border rounded p-2.5 text-sm outline-none focus:border-amber-400">
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Colonia</label>
                <input type="text" name="colonia" placeholder="Colonia" class="w-full border rounded p-2.5 text-sm outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Referencia</label>
                <input type="text" name="referencia" placeholder="Punto de referencia" class="w-full border rounded p-2.5 text-sm outline-none focus:border-amber-400">
            </div>
        </div>
        
        <div class="flex gap-3 pt-6 border-t">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded font-bold text-sm flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg> Guardar Cliente</button>
            <a href="{{ route('clientes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded font-bold text-sm">Cancelar</a>
        </div>
    </form>
</div>
@endsection