@extends('layouts.app')

@section('content')
<div x-data="configuracionPOS()" x-init="init()" class="w-full flex flex-col items-center justify-center min-h-[70vh]">
    
    <div x-show="showToast" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
         class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
        <div class="bg-white rounded-full p-0.5"><svg class="w-4 h-4 text-[#00b300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></div>
        <span class="font-medium text-[15px]">Configuración guardada en el navegador.</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 w-full max-w-2xl overflow-hidden">
        
        <div class="p-8 border-b border-gray-50">
            <h2 class="text-2xl font-black text-[#1e293b] tracking-tight">Configuración del POS</h2>
            <p class="text-sm text-gray-500 mt-1">Ajustes locales de la terminal</p>
        </div>

        <div class="p-8 space-y-6">
            
            <div class="flex items-center justify-between bg-gray-50 p-5 rounded-lg border border-gray-100">
                <div class="pr-4">
                    <h3 class="font-bold text-gray-800 text-[15px]">Bloquear Altura (Viewport Lock)</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Evita que la pantalla se desplace verticalmente (recomendado para POS).</p>
                </div>
                
                <button type="button" @click="viewportLock = !viewportLock"
                        :class="viewportLock ? 'bg-blue-600' : 'bg-gray-300'"
                        class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-inner">
                    <span :class="viewportLock ? 'translate-x-5' : 'translate-x-0'"
                          class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>

            <div class="flex items-center justify-between bg-gray-50 p-5 rounded-lg border border-gray-100">
                <div class="pr-4">
                    <h3 class="font-bold text-gray-800 text-[15px]">Tamaño de Impresión</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Ancho del papel para los tickets.</p>
                </div>
                
                <select x-model="printSize" class="border border-gray-300 text-gray-700 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white w-32 shadow-sm font-medium">
                    <option value="58mm">58mm</option>
                    <option value="80mm">80mm</option>
                </select>
            </div>

            <div class="flex justify-end pt-4">
                <button type="button" @click="guardarConfiguracion()" class="bg-[#eab308] hover:bg-[#ca8a04] text-white px-6 py-2.5 rounded-md text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 288a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
                    Guardar Cambios
                </button>
            </div>

        </div>
    </div>

    <p class="text-[#94a3b8] text-sm mt-6 font-medium">Nota: Estas configuraciones se guardan solo en este navegador.</p>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('configuracionPOS', () => ({
            // Cargamos desde localStorage, si no hay, ponemos valores por defecto
            viewportLock: localStorage.getItem('pos_viewport_lock') === 'true',
            printSize: localStorage.getItem('pos_print_size') || '58mm',
            showToast: false,
            
            init() {
                // Al cargar la página, aplicamos el bloqueo de pantalla si estaba activo
                this.aplicarBloqueoPantalla();
            },

            guardarConfiguracion() {
                // Guardamos en el navegador
                localStorage.setItem('pos_viewport_lock', this.viewportLock);
                localStorage.setItem('pos_print_size', this.printSize);
                
                // Aplicamos el bloqueo inmediatamente
                this.aplicarBloqueoPantalla();

                // Mostramos mensaje de éxito
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3000);
            },

            aplicarBloqueoPantalla() {
                if(this.viewportLock) {
                    document.body.style.overflow = 'hidden'; // Evita el scroll
                } else {
                    document.body.style.overflow = 'auto';   // Permite el scroll
                }
            }
        }))
    })
</script>
@endsection