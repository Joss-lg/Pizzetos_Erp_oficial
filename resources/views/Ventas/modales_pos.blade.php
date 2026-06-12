<div x-show="modalComentarios" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6" @click.away="modalComentarios = false">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Comentarios de la orden</h3>
        <p class="text-sm text-gray-500 mb-2">Agrega instrucciones especiales para esta orden (opcional)</p>
        <textarea x-model="comentarios" rows="4" placeholder="Ejemplo: Sin cebolla, extra queso, bien cocida..." class="w-full border rounded p-3 text-sm focus:border-yellow-500 outline-none"></textarea>
        <div class="flex gap-2 mt-4">
            <button @click="modalComentarios = false" class="w-1/2 bg-gray-200 text-gray-800 font-bold py-2 rounded">Cancelar</button>
            <button @click="modalComentarios = false" class="w-1/2 bg-[#ffb300] text-white font-bold py-2 rounded">Guardar</button>
        </div>
    </div>
</div>

<div x-show="modalPaquete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm overflow-hidden" @click.away="modalPaquete = false">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-2xl font-black text-gray-900" x-text="paqueteSeleccionado ? paqueteSeleccionado.nombre : ''"></h3>
            <button @click="modalPaquete = false" class="text-gray-400 hover:text-gray-800 font-bold text-xl">X</button>
        </div>
        <div class="p-6">
            <p class="font-bold text-gray-800 text-sm mb-2">Este paquete incluye:</p>
            
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm p-3 rounded-lg mb-6 leading-relaxed" 
                 x-text="paqueteSeleccionado ? paqueteSeleccionado.descripcion : ''">
            </div>
            
            <p class="font-bold text-gray-900 text-sm mb-2" x-show="paqueteSeleccionado && paqueteSeleccionado.id == 1">Selecciona tus pizzas (Ejemplo visual):</p>
            <div class="space-y-2 mb-6" x-show="paqueteSeleccionado && paqueteSeleccionado.id == 1">
                <label class="block border border-yellow-400 bg-yellow-50 p-3 rounded cursor-pointer">
                    <input type="radio" name="paq_opcion" class="hidden" checked>
                    <div class="font-bold text-gray-900 text-sm">Combinado</div>
                    <div class="text-xs text-gray-500">1 Hawaiana y 1 Pepperoni</div>
                </label>
            </div>

            <div class="text-center font-black text-green-600 text-2xl mb-6" x-text="paqueteSeleccionado ? 'Precio: $' + paqueteSeleccionado.precio.toFixed(2) : '$0.00'"></div>

            <div class="flex gap-3">
                <button @click="modalPaquete = false" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded transition-colors">Cancelar</button>
                <button @click="agregarPaquete(paqueteSeleccionado.id, paqueteSeleccionado.nombre, paqueteSeleccionado.precio); modalPaquete = false;" class="flex-1 bg-[#ffc107] hover:bg-yellow-500 text-white font-bold py-3 rounded shadow-sm transition-colors">Agregar al Ticket</button>
            </div>
        </div>
    </div>
</div>

<div x-show="modalMitades" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row" @click.away="modalMitades = false">
        <div class="w-full md:w-2/3 p-6 border-r">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-black text-red-600 flex items-center gap-2"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 512 512"><path d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg> Mitades</h3>
            </div>
            <p class="font-bold text-gray-800 mb-3">1. Selecciona el tamaño:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="border rounded-lg p-3 text-center cursor-pointer hover:border-red-500">
                    <p class="font-bold text-sm">Chica</p><p class="text-red-600 font-black text-xs">$180.00</p>
                </div>
                <div class="border border-red-500 bg-red-50 rounded-lg p-3 text-center cursor-pointer">
                    <p class="font-bold text-sm">Mediana</p><p class="text-red-600 font-black text-xs">$255.00</p>
                </div>
                <div class="border rounded-lg p-3 text-center cursor-pointer hover:border-red-500">
                    <p class="font-bold text-sm">Grande</p><p class="text-red-600 font-black text-xs">$315.00</p>
                </div>
                <div class="border rounded-lg p-3 text-center cursor-pointer hover:border-red-500">
                    <p class="font-bold text-sm">Familiar</p><p class="text-red-600 font-black text-xs">$375.00</p>
                </div>
            </div>
            <div class="flex justify-between items-center mb-3">
                <p class="font-bold text-gray-800">2. Selecciona 2 especialidades:</p>
                <span class="text-xs text-gray-500">Seleccionadas: <span class="text-red-600 font-bold">0/2</span></span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-2">
                <button class="border rounded py-2 text-sm text-gray-700 hover:border-red-500">Hawaiana</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:border-red-500">Pepperoni</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:border-red-500">Mexicana</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:border-red-500">Carnes Frias</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:border-red-500">Azteca</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:border-red-500">Cubana</button>
            </div>
        </div>
        <div class="w-full md:w-1/3 p-6 bg-gray-50 flex flex-col relative">
            <button @click="modalMitades = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 font-bold">X</button>
            <h4 class="font-bold text-lg border-b pb-2 mb-4">Resumen</h4>
            <div class="mb-4">
                <p class="text-xs text-gray-500">Tamaño</p>
                <p class="font-bold text-gray-900">Mediana</p>
            </div>
            <div class="mb-auto">
                <p class="text-xs text-gray-500 mb-2">Especialidades</p>
                <div class="border border-dashed border-gray-300 p-2 text-sm text-gray-400 mb-2 bg-white">1/2 Selecciona...</div>
                <div class="border border-dashed border-gray-300 p-2 text-sm text-gray-400 bg-white">2/2 Selecciona...</div>
            </div>
            <div class="border-t pt-4 mt-4 flex justify-between items-end mb-4">
                <span class="text-gray-500 text-sm">Total</span>
                <span class="text-3xl font-black text-green-500">$0.00</span>
            </div>
            <button class="w-full bg-gray-300 text-white font-bold py-3 rounded mb-2 cursor-not-allowed">Agregar al Pedido</button>
            <button @click="modalMitades = false" class="w-full text-gray-500 font-bold py-2 hover:bg-gray-200 rounded">Cancelar</button>
        </div>
    </div>
</div>

<div x-show="modalIngredientes" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]" @click.away="modalIngredientes = false">
        <div class="bg-[#ff5722] p-4 flex justify-between items-center text-white">
            <h3 class="text-xl font-bold flex items-center gap-2">🍕 Pizza Por Ingrediente</h3>
            <button @click="modalIngredientes = false" class="font-bold hover:text-gray-200">X</button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <p class="font-bold text-gray-800 mb-3">1. Selecciona el tamaño:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="border rounded-lg p-3 text-center cursor-pointer hover:border-orange-500">
                    <p class="font-bold text-sm">Chica</p><p class="text-green-600 font-black text-xs">$180.00</p>
                </div>
                <div class="border border-orange-500 bg-orange-50 rounded-lg p-3 text-center cursor-pointer">
                    <p class="font-bold text-sm">Mediana</p><p class="text-green-600 font-black text-xs">$255.00</p>
                </div>
                <div class="border rounded-lg p-3 text-center cursor-pointer hover:border-orange-500">
                    <p class="font-bold text-sm">Grande</p><p class="text-green-600 font-black text-xs">$315.00</p>
                </div>
                <div class="border rounded-lg p-3 text-center cursor-pointer hover:border-orange-500">
                    <p class="font-bold text-sm">Familiar</p><p class="text-green-600 font-black text-xs">$375.00</p>
                </div>
            </div>
            <p class="font-bold text-gray-800 mb-3">2. Selecciona los ingredientes:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Jamón</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Pepperoni</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Salchicha</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Tocino</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Champiñón</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Cebolla</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Jalapeño</button>
                <button class="border rounded py-2 text-sm text-gray-700 hover:bg-orange-50">Queso</button>
            </div>
        </div>
        <div class="p-4 border-t bg-gray-50 flex gap-3">
            <button @click="modalIngredientes = false" class="w-1/3 bg-gray-300 text-gray-700 font-bold py-3 rounded">Cancelar</button>
            <button class="w-2/3 bg-[#ff5722] text-white font-bold py-3 rounded">Agregar al Carrito</button>
        </div>
    </div>
</div>

<div x-show="modalDireccion" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden" @click.away="modalDireccion = false">
        <div class="p-4 border-b bg-gray-50 flex justify-between"><h3 class="font-bold">📍 Seleccionar Dirección</h3><button @click="modalDireccion = false" class="font-bold text-gray-400">X</button></div>
        <div class="p-6">
            <select x-model="id_clie" class="w-full border rounded p-2 mb-4">
                <option value="">Seleccione un cliente...</option>
                <template x-for="clie in clientes" :key="clie.id_clie"><option :value="clie.id_clie" x-text="clie.nombre"></option></template>
            </select>
            <div x-show="id_clie !== ''" class="space-y-2 max-h-40 overflow-y-auto">
                <template x-for="dir in direcciones.filter(d => d.id_clie == id_clie)" :key="dir.id_dir">
                    <label class="flex items-start gap-2 p-3 border rounded cursor-pointer" :class="id_dir == dir.id_dir ? 'border-orange-500 bg-orange-50' : ''">
                        <input type="radio" :value="dir.id_dir" x-model="id_dir" class="mt-1">
                        <div><p class="font-bold text-sm" x-text="dir.calle"></p><p class="text-xs text-gray-500" x-text="dir.colonia"></p></div>
                    </label>
                </template>
            </div>
            <button @click="confirmarDireccion()" :disabled="id_dir === ''" class="w-full bg-[#ff5722] text-white font-bold py-3 rounded mt-6">Confirmar Selección</button>
        </div>
    </div>
</div>

<div x-show="modalPago" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
    <div class="bg-white rounded-lg w-full max-w-md overflow-hidden" @click.away="modalPago = false">
        <div class="bg-[#ffc107] p-4 text-white text-center flex justify-between items-center"><span class="font-bold">Método de Pago</span><button @click="modalPago = false" class="font-bold">X</button></div>
        <div class="p-6 space-y-3">
            <div class="text-center mb-4"><p class="text-sm text-gray-500">Total a Pagar</p><h2 class="text-3xl font-black" x-text="'$'+calcularTotal().toFixed(2)"></h2></div>
            <div class="border rounded p-3"><label class="flex items-center gap-2 font-bold"><input type="checkbox" x-model="pagoEf"> 💵 Efectivo</label><input x-show="pagoEf" type="number" x-model="montoEf" class="w-full border rounded mt-2 p-2 text-sm"></div>
            <div class="border rounded p-3"><label class="flex items-center gap-2 font-bold"><input type="checkbox" x-model="pagoTa"> 💳 Tarjeta</label><input x-show="pagoTa" type="number" x-model="montoTa" class="w-full border rounded mt-2 p-2 text-sm"></div>
            <div class="border rounded p-3"><label class="flex items-center gap-2 font-bold"><input type="checkbox" x-model="pagoTr"> ⇄ Transferencia</label><input x-show="pagoTr" type="number" x-model="montoTr" class="w-full border rounded mt-2 p-2 text-sm"><input x-show="pagoTr" type="text" x-model="refTr" placeholder="Referencia" class="w-full border mt-2 p-2 text-sm"></div>
            
            <button @click="procesarVentaFinal()" :disabled="!validaPagos()" :class="!validaPagos() ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#0f172a] hover:bg-black'" class="w-full text-white font-bold py-3 rounded mt-4 transition-colors">Confirmar Pagos</button>
            <div x-show="!validaPagos()" class="text-red-500 text-xs text-center mt-1">Los montos no coinciden con el total.</div>
        </div>
    </div>
</div>