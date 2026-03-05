@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-xl">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Iniciar Nuevo Turno</h2>
            <p class="text-gray-600">Pizzetos POS - Sucursal {{ auth()->user()->sucursal->nombre ?? 'Principal' }}</p>
        </div>

        <form action="{{ route('flujo.caja.abrir') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Monto Inicial en Caja</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                    <input type="number" step="0.01" name="monto_inicial" 
                        class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                        placeholder="0.00" required autofocus>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Observaciones</label>
                <textarea name="observaciones" rows="2" 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 outline-none"
                    placeholder="Ej. Dejo cambio de $200 en monedas..."></textarea>
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition duration-200">
                ABRIR CAJA
            </button>
        </form>
    </div>
</div>
@endsection