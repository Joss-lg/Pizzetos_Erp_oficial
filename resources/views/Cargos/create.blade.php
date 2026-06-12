@extends('layouts.app')

@section('content')
<div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <a href="{{ route('cargos.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium mb-6 transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Volver a Cargos</a>
    
    <h2 class="text-2xl font-black text-gray-800 tracking-tight">Agregar Nuevo Cargo</h2>
    <p class="text-sm text-gray-500 mt-1 mb-8">Define el nombre del cargo y sus permisos en el sistema</p>

    <form action="{{ route('cargos.store') }}" method="POST">
        @csrf
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cargo <span class="text-red-500">*</span></label>
            <input type="text" name="nombre" required placeholder="Ej: Administrador, Cajero, Gerente..." class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-4">Permisos del Cargo</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            <div x-data="{ c: false, m: false, e: false, v: false, toggle() { let st = !(this.c && this.m && this.e && this.v); this.c = st; this.m = st; this.e = st; this.v = st; } }" class="border border-blue-200 border-t-4 border-t-blue-400 bg-blue-50/20 rounded-lg p-5">
                <div class="flex justify-between items-center mb-4"><h4 class="font-bold text-gray-800">Productos</h4><button type="button" @click="toggle" class="text-xs text-blue-500 hover:underline" x-text="(c && m && e && v) ? 'Desmarcar todos' : 'Marcar todos'"></button></div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="crear_producto" value="1" x-model="c" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Crear</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="modificar_producto" value="1" x-model="m" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Modificar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="eliminar_producto" value="1" x-model="e" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Eliminar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ver_producto" value="1" x-model="v" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Ver</label>
                </div>
            </div>

            <div x-data="{ c: false, m: false, e: false, v: false, toggle() { let st = !(this.c && this.m && this.e && this.v); this.c = st; this.m = st; this.e = st; this.v = st; } }" class="border border-green-200 border-t-4 border-t-green-400 bg-green-50/20 rounded-lg p-5">
                <div class="flex justify-between items-center mb-4"><h4 class="font-bold text-gray-800">Empleados</h4><button type="button" @click="toggle" class="text-xs text-blue-500 hover:underline" x-text="(c && m && e && v) ? 'Desmarcar todos' : 'Marcar todos'"></button></div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="crear_empleado" value="1" x-model="c" class="rounded border-gray-300 text-green-600 focus:ring-green-500"> Crear</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="modificar_empleado" value="1" x-model="m" class="rounded border-gray-300 text-green-600 focus:ring-green-500"> Modificar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="eliminar_empleado" value="1" x-model="e" class="rounded border-gray-300 text-green-600 focus:ring-green-500"> Eliminar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ver_empleado" value="1" x-model="v" class="rounded border-gray-300 text-green-600 focus:ring-green-500"> Ver</label>
                </div>
            </div>

            <div x-data="{ c: false, m: false, e: false, v: false, toggle() { let st = !(this.c && this.m && this.e && this.v); this.c = st; this.m = st; this.e = st; this.v = st; } }" class="border border-yellow-200 border-t-4 border-t-yellow-400 bg-yellow-50/20 rounded-lg p-5">
                <div class="flex justify-between items-center mb-4"><h4 class="font-bold text-gray-800">Ventas</h4><button type="button" @click="toggle" class="text-xs text-blue-500 hover:underline" x-text="(c && m && e && v) ? 'Desmarcar todos' : 'Marcar todos'"></button></div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="crear_venta" value="1" x-model="c" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"> Crear</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="modificar_venta" value="1" x-model="m" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"> Modificar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="eliminar_venta" value="1" x-model="e" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"> Eliminar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ver_venta" value="1" x-model="v" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"> Ver</label>
                </div>
            </div>

            <div x-data="{ c: false, m: false, e: false, v: false, toggle() { let st = !(this.c && this.m && this.e && this.v); this.c = st; this.m = st; this.e = st; this.v = st; } }" class="border border-purple-200 border-t-4 border-t-purple-400 bg-purple-50/20 rounded-lg p-5">
                <div class="flex justify-between items-center mb-4"><h4 class="font-bold text-gray-800">Recursos</h4><button type="button" @click="toggle" class="text-xs text-blue-500 hover:underline" x-text="(c && m && e && v) ? 'Desmarcar todos' : 'Marcar todos'"></button></div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="crear_recurso" value="1" x-model="c" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"> Crear</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="modificar_recurso" value="1" x-model="m" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"> Modificar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="eliminar_recurso" value="1" x-model="e" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"> Eliminar</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ver_recurso" value="1" x-model="v" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"> Ver</label>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2">Guardar Cargo</button>
            <a href="{{ route('cargos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold">Cancelar</a>
        </div>
    </form>
</div>
@endsection