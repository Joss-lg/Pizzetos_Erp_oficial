@extends('layouts.app')

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <div class="bg-white rounded-full p-0.5">
        <svg class="w-4 h-4 text-[#00b300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
    </div>
    <span class="font-medium text-[15px]">{{ session('success') }}</span>
    <button @click="show = false" class="ml-4 hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
@endif

<div x-data="{ 
        mostrarModalEstado: false, formAccionEstado: '', accionTexto: '', colorBoton: '',
        mostrarModalDirecciones: false, formAccionDireccion: '',
        clienteActivo: '', clienteId: null, direccionesActivas: [],
        
        /* VARIABLE PARA LA BÚSQUEDA EN TIEMPO REAL */
        searchQuery: '{{ request('buscar') }}',
        
        /* FUNCIÓN QUE EVALÚA SI EL CLIENTE COINCIDE CON LO QUE ESCRIBES */
        checkMatch(nombre, telefono) {
            if (this.searchQuery.trim() === '') return true;
            
            // Función para quitar acentos y hacer la búsqueda más tolerante
            let removeAccents = (str) => str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            
            let q = removeAccents(this.searchQuery.trim().toLowerCase());
            let n = removeAccents(nombre.toLowerCase());
            let t = telefono.toLowerCase();
            
            return n.includes(q) || t.includes(q);
        }
    }" class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-8 relative">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Clientes</h2>
            <p class="text-sm text-gray-500 mt-1">Gestiona la información de los clientes</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto flex-1 justify-end">
            
            <div class="relative w-full sm:max-w-md">
                <input type="text" x-model="searchQuery" placeholder="Buscar por nombre o teléfono..." 
                       class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-colors">
                
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                
                <button x-show="searchQuery.length > 0" x-cloak @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors" title="Limpiar búsqueda">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <a href="{{ route('clientes.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition-colors shadow-sm whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg> Añadir Cliente
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-400 uppercase text-[10px] tracking-widest font-bold border-b border-gray-100">
                    <th class="px-6 py-4 font-semibold">NOMBRE</th>
                    <th class="px-6 py-4 font-semibold">APELLIDO</th>
                    <th class="px-6 py-4 font-semibold">TELÉFONO</th>
                    <th class="px-6 py-4 font-semibold text-center">STATUS</th>
                    <th class="px-6 py-4 font-semibold text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($clientes as $clie)
                    <tr x-show="checkMatch('{{ addslashes($clie->nombre . ' ' . $clie->apellido) }}', '{{ addslashes($clie->telefono) }}')" 
                        class="hover:bg-gray-50 transition-colors">
                        
                        <td class="px-6 py-4 text-gray-900 font-bold text-sm">{{ $clie->nombre }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $clie->apellido }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $clie->telefono }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($clie->status == 1)
                                <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full text-xs">Activo</span>
                            @else
                                <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-xs">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3 items-center">
                                <button type="button" @click="
                                    clienteId = {{ $clie->id_clie }};
                                    clienteActivo = '{{ addslashes($clie->nombre . ' ' . $clie->apellido) }}';
                                    direccionesActivas = {{ json_encode($todasDirecciones[$clie->id_clie] ?? []) }};
                                    formAccionDireccion = '{{ route('clientes.storeDireccion', $clie->id_clie) }}';
                                    mostrarModalDirecciones = true;
                                " class="text-green-500 hover:text-green-700 transition-colors" title="Gestionar Direcciones">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 512 512"><path d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H96zM208 288h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H144c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"/></svg>
                                </button>

                                <a href="{{ route('clientes.edit', $clie->id_clie) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar Cliente">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 512 512"><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.9 21.9l-23.2 63.6c-3.1 8.5-1.5 18 4.1 23.6s15.1 7.1 23.6 4.1l63.6-23.2c8.2-3 15.8-7.8 21.9-13.9L431.1 145.4l-97.9-97.9L172.4 241.7zM96 64C43 64 0 107 0 159.1V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V159.1c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
                                </a>

                                {{-- BOTONES DE ESTADO (SOLO VISIBLES PARA EL ADMINISTRADOR) --}}
                                @if(Auth::user()->id_ca == 1)
                                    @if($clie->status == 1)
                                    <button type="button" @click="formAccionEstado = '{{ route('clientes.destroy', $clie->id_clie) }}'; accionTexto = 'Desactivar'; colorBoton = 'bg-red-700 hover:bg-red-800'; mostrarModalEstado = true" class="text-red-700 hover:text-red-900 transition-colors relative" title="Desactivar Cliente">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 640 512"><path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM471 143c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>
                                    </button>
                                    @else
                                    <button type="button" @click="formAccionEstado = '{{ route('clientes.activar', $clie->id_clie) }}'; accionTexto = 'Activar'; colorBoton = 'bg-green-600 hover:bg-green-700'; mostrarModalEstado = true" class="text-[#00b300] hover:text-green-800 transition-colors relative" title="Activar Cliente">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 640 512"><path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM625 177L497 305c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L591 143c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center border-t border-gray-100">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 rounded-full p-4 mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">La base de datos está vacía</h3>
                                <p class="text-sm text-gray-500 mt-1">Aún no hay clientes registrados en el sistema.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="mostrarModalEstado" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 overflow-hidden text-center" @click.away="mostrarModalEstado = false">
            <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight" x-text="'¿' + accionTexto + ' Cliente?'"></h3>
            <p class="text-sm text-gray-500 mb-6">Confirma si deseas cambiar el estado del cliente.</p>
            <div class="flex gap-3 mt-6">
                <button @click="mostrarModalEstado = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition-colors text-sm">Cancelar</button>
                <form :action="formAccionEstado" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button type="submit" :class="colorBoton" class="w-full text-white font-bold py-3 rounded-xl shadow-sm text-sm" x-text="'Sí, ' + accionTexto"></button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="mostrarModalDirecciones" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-lg flex flex-col" @click.away="mostrarModalDirecciones = false">
            
            <div class="p-6 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-800 tracking-tight">Gestionar Direcciones</h3>
                <button @click="mostrarModalDirecciones = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            
            <div class="p-6 pb-2">
                <p class="text-sm text-gray-600 mb-4">Cliente: <span class="font-bold text-gray-900" x-text="clienteActivo"></span></p>
                <h4 class="font-bold text-gray-800 mb-3">Direcciones Registradas</h4>
                
                <div class="space-y-3 max-h-48 overflow-y-auto mb-6 pr-2">
                    <template x-for="dir in direccionesActivas" :key="dir.id_dir">
                        <div class="border rounded-md p-4 relative bg-gray-50">
                            <form :action="'/direcciones/' + dir.id_dir" method="POST" class="absolute top-4 right-4">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Eliminar Dirección"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg></button>
                            </form>
                            <p class="text-sm text-gray-800" x-text="dir.calle"></p>
                            <p class="text-xs text-gray-500 mt-1">Manzana: <span x-text="dir.manzana"></span> • Lote: <span x-text="dir.lote"></span></p>
                            <p class="text-xs text-gray-500">Colonia: <span x-text="dir.colonia"></span></p>
                            <p class="text-xs text-gray-500 italic mt-1" x-text="'Ref: ' + dir.referencia"></p>
                        </div>
                    </template>
                    <div x-show="direccionesActivas.length === 0" class="text-xs text-gray-400 italic">No hay direcciones registradas.</div>
                </div>

                <h4 class="font-bold text-gray-800 mb-4 pt-4 border-t">Agregar Nueva Dirección</h4>
                <form :action="formAccionDireccion" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Calle</label>
                        <input type="text" name="calle" required placeholder="Calle y número" class="w-full border rounded p-2 text-sm outline-none focus:border-amber-400">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Manzana</label>
                            <input type="text" name="manzana" placeholder="Manzana" class="w-full border rounded p-2 text-sm outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Lote</label>
                            <input type="text" name="lote" placeholder="Lote" class="w-full border rounded p-2 text-sm outline-none focus:border-amber-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Colonia</label>
                        <input type="text" name="colonia" placeholder="Colonia" class="w-full border rounded p-2 text-sm outline-none focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Referencia</label>
                        <input type="text" name="referencia" placeholder="Punto de referencia" class="w-full border rounded p-2 text-sm outline-none focus:border-amber-400">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t mt-4 pb-6">
                        <button type="button" @click="mostrarModalDirecciones = false" class="bg-gray-500 text-white px-4 py-2 rounded font-bold text-sm">Cerrar</button>
                        <button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded font-bold text-sm">Agregar Dirección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection