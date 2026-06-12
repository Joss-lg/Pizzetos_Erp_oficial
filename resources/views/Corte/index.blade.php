@extends('layouts.app')

@section('content')
<div x-data="modalDetalleDia()" class="max-w-[1600px] mx-auto relative">
    
<<<<<<< HEAD
    <div class="flex justify-between items-end mb-8">
=======
    <div class="flex flex-col gap-4 mb-8 md:flex-row md:justify-between md:items-end">
>>>>>>> origin/rama_pizzetos_erp
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Corte Mensual</h2>
            <p class="text-sm text-gray-500 mt-1">Resumen financiero de la sucursal</p>
        </div>
<<<<<<< HEAD
        <form method="GET" action="{{ route('corte.index') }}" class="flex items-center gap-3">
            <div class="relative">
                <input type="month" name="mes" value="{{ $mesSeleccionado }}" class="border border-gray-200 text-gray-600 text-sm rounded-lg px-4 py-2.5 focus:ring-amber-400 focus:border-amber-400 outline-none w-48 font-medium">
            </div>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-sm cursor-pointer">
=======
        <form method="GET" action="{{ route('corte.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
            <div class="relative">
                <input type="month" name="mes" value="{{ $mesSeleccionado }}" class="border border-gray-200 text-gray-600 text-sm rounded-lg px-4 py-2.5 focus:ring-amber-400 focus:border-amber-400 outline-none w-full sm:w-48 font-medium">
            </div>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition-colors shadow-sm cursor-pointer w-full sm:w-auto">
>>>>>>> origin/rama_pizzetos_erp
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Actualizar
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-green-500 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Ingresos</p>
<<<<<<< HEAD
                    <h3 class="text-3xl font-black text-green-500 tracking-tighter">${{ number_format($totalIngresos, 2) }}</h3>
=======
                    <h3 class="text-2xl sm:text-3xl font-black text-green-500 tracking-tighter">${{ number_format($totalIngresos, 2) }}</h3>
>>>>>>> origin/rama_pizzetos_erp
                </div>
                <div class="p-2 bg-green-50 rounded-lg text-green-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-blue-500 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Balance Neto</p>
<<<<<<< HEAD
                    <h3 class="text-3xl font-black {{ $balanceNeto >= 0 ? 'text-blue-500' : 'text-red-500' }} tracking-tighter">${{ number_format($balanceNeto, 2) }}</h3>
=======
                    <h3 class="text-2xl sm:text-3xl font-black {{ $balanceNeto >= 0 ? 'text-blue-500' : 'text-red-500' }} tracking-tighter">${{ number_format($balanceNeto, 2) }}</h3>
>>>>>>> origin/rama_pizzetos_erp
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-red-500 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Gastos</p>
<<<<<<< HEAD
                    <h3 class="text-3xl font-black text-red-500 tracking-tighter">${{ number_format($totalGastos, 2) }}</h3>
=======
                    <h3 class="text-2xl sm:text-3xl font-black text-red-500 tracking-tighter">${{ number_format($totalGastos, 2) }}</h3>
>>>>>>> origin/rama_pizzetos_erp
                </div>
                <div class="p-2 bg-red-50 rounded-lg text-red-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-amber-400 relative overflow-hidden">
            <div class="flex justify-between items-start mb-2">
                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Métodos de Pago</p>
                <div class="p-1.5 bg-amber-50 rounded-lg text-amber-500"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between"><span class="text-gray-500 flex items-center gap-1">Efectivo:</span> <span class="font-bold text-green-500">${{ number_format($ingresosEfectivo, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 flex items-center gap-1">Tarjeta:</span> <span class="font-bold text-blue-500">${{ number_format($ingresosTarjeta, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 flex items-center gap-1">Transferencia:</span> <span class="font-bold text-purple-500">${{ number_format($ingresosTransferencia, 2) }}</span></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Desglose Diario por Método de Pago</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] tracking-widest font-bold border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold text-center">Efectivo</th>
                        <th class="px-6 py-4 font-semibold text-center">Tarjeta</th>
                        <th class="px-6 py-4 font-semibold text-center">Transferencia</th>
                        <th class="px-6 py-4 font-semibold text-center">Total Ingresos</th>
                        <th class="px-6 py-4 font-semibold text-center">Gastos</th>
                        <th class="px-6 py-4 font-semibold text-center">Balance</th>
                        <th class="px-6 py-4 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @foreach($desgloseDiario as $dia)
                        @php
                            $totalIngresoDia = $dia['efectivo'] + $dia['tarjeta'] + $dia['transferencia'];
                            $balanceDia = $totalIngresoDia - $dia['gastos'];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $dia['fecha_format'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-500">${{ number_format($dia['efectivo'], 2) }}</td>
                            <td class="px-6 py-4 text-center text-blue-500 font-medium">${{ number_format($dia['tarjeta'], 2) }}</td>
                            <td class="px-6 py-4 text-center text-purple-500 font-medium">${{ number_format($dia['transferencia'], 2) }}</td>
                            <td class="px-6 py-4 text-center text-green-500 font-medium">${{ number_format($totalIngresoDia, 2) }}</td>
                            <td class="px-6 py-4 text-center text-red-500 font-medium">${{ number_format($dia['gastos'], 2) }}</td>
                            <td class="px-6 py-4 text-center {{ $balanceDia >= 0 ? 'text-blue-600' : 'text-red-600' }} font-bold">${{ number_format($balanceDia, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" @click="abrirModal('{{ $dia['fecha_db'] }}', '{{ $dia['fecha_format'] }}')" class="p-2.5 bg-blue-50/50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-full transition-all inline-flex justify-center items-center group shadow-sm hover:shadow-md" title="Ver detalle del día">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="isOpen" x-cloak class="fixed inset-0 bg-black/60 z-50 flex justify-center items-center p-4 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden" @click.away="cerrarModal()" x-transition.scale.origin.bottom>
            
            <div class="p-6 border-b flex justify-between items-start bg-white z-10">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tighter">Detalle del Día</h2>
                    <p class="text-blue-500 font-bold text-sm" x-text="fechaTexto"></p>
                </div>
                <button @click="cerrarModal()" class="text-gray-400 hover:text-gray-800 hover:bg-gray-100 p-2 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div x-show="loading" class="p-12 flex justify-center items-center flex-1 bg-gray-50/50">
                <svg class="animate-spin h-10 w-10 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <div x-show="!loading" class="flex-1 overflow-y-auto p-6 bg-gray-50/50">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Ingresos</p>
                                <h3 class="text-2xl font-black text-green-500 tracking-tighter" x-text="formatMoney(datos.ingresos)"></h3>
                            </div>
                            <div class="p-1.5 bg-green-50 rounded text-green-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg></div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Gastos</p>
                                <h3 class="text-2xl font-black text-red-500 tracking-tighter" x-text="formatMoney(datos.gastos)"></h3>
                            </div>
                            <div class="p-1.5 bg-red-50 rounded text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Balance</p>
                                <h3 class="text-2xl font-black text-blue-600 tracking-tighter" x-text="formatMoney(datos.balance)"></h3>
                            </div>
                            <div class="p-1.5 bg-blue-50 rounded text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
                        </div>
                    </div>
                </div>

                <h4 class="font-bold text-gray-800 mb-3 text-sm">Desglose de Ingresos por Método de Pago</h4>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                    <div class="border border-green-100 bg-green-50/50 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-green-600 mb-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg> <span class="font-bold text-sm">Efectivo</span></div>
                        <h4 class="text-xl font-black text-green-600 tracking-tight" x-text="formatMoney(datos.metodos.efectivo.monto)"></h4>
                        <p class="text-[10px] text-green-500 font-medium mt-1" x-text="datos.metodos.efectivo.pct + '% del total'"></p>
                    </div>
                    <div class="border border-blue-100 bg-blue-50/50 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-blue-600 mb-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> <span class="font-bold text-sm">Tarjeta</span></div>
                        <h4 class="text-xl font-black text-blue-600 tracking-tight" x-text="formatMoney(datos.metodos.tarjeta.monto)"></h4>
                        <p class="text-[10px] text-blue-500 font-medium mt-1" x-text="datos.metodos.tarjeta.pct + '% del total'"></p>
                    </div>
                    <div class="border border-purple-100 bg-purple-50/50 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-purple-600 mb-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg> <span class="font-bold text-sm">Transferencia</span></div>
                        <h4 class="text-xl font-black text-purple-600 tracking-tight" x-text="formatMoney(datos.metodos.transferencia.monto)"></h4>
                        <p class="text-[10px] text-purple-500 font-medium mt-1" x-text="datos.metodos.transferencia.pct + '% del total'"></p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-5 mb-6">
                    <div class="flex items-center gap-2 mb-2 text-purple-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <h4 class="font-bold text-gray-900">Sucursal: <span x-text="datos.sucursal_nombre"></span></h4>
                    </div>
                    <p class="text-sm text-gray-500 italic" x-show="datos.lista_ingresos.length === 0 && datos.lista_gastos.length === 0">No hay datos disponibles para esta sucursal</p>
                    <p class="text-sm text-gray-500 italic" x-show="datos.lista_ingresos.length > 0 || datos.lista_gastos.length > 0">Se encontraron registros detallados de operaciones.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b flex text-center">
                        <button @click="tab = 'ingresos'" :class="tab === 'ingresos' ? 'border-amber-500 text-amber-600 bg-amber-50/30' : 'border-transparent text-gray-500 hover:bg-gray-50'" class="flex-1 py-4 text-sm font-bold border-b-2 transition-colors">
                            Detalle de Ingresos (<span x-text="datos.lista_ingresos.length"></span>)
                        </button>
                        <button @click="tab = 'gastos'" :class="tab === 'gastos' ? 'border-amber-500 text-amber-600 bg-amber-50/30' : 'border-transparent text-gray-500 hover:bg-gray-50'" class="flex-1 py-4 text-sm font-bold border-b-2 transition-colors">
                            Detalle de Gastos (<span x-text="datos.lista_gastos.length"></span>)
                        </button>
                    </div>
                    
                    <div class="p-6 h-64 overflow-y-auto">
                        <div x-show="tab === 'ingresos'">
                            <template x-if="datos.lista_ingresos.length === 0">
                                <p class="text-center text-gray-400 text-sm py-4 italic">No hay registros de ventas para este día.</p>
                            </template>
                            <template x-if="datos.lista_ingresos.length > 0">
                                <div class="space-y-2">
                                    <template x-for="venta in datos.lista_ingresos" :key="venta.id_venta">
                                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-100">
                                            <span class="text-gray-700 font-bold text-sm">Folio Venta #<span x-text="venta.id_venta"></span></span>
                                            <span class="text-green-600 font-black" x-text="formatMoney(venta.total)"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="tab === 'gastos'" x-cloak>
                            <template x-if="datos.lista_gastos.length === 0">
                                <p class="text-center text-gray-400 text-sm py-4 italic">No hay registros de gastos para este día.</p>
                            </template>
                            <template x-if="datos.lista_gastos.length > 0">
                                <div class="space-y-2">
                                    <template x-for="gasto in datos.lista_gastos" :key="gasto.id_gastos">
                                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-100">
                                            <span class="text-gray-700 font-bold text-sm" x-text="gasto.descripcion || 'Gasto Operativo'"></span>
                                            <span class="text-red-500 font-black" x-text="'- ' + formatMoney(gasto.precio)"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            <div class="p-4 border-t bg-white flex justify-end z-10">
                <button @click="cerrarModal()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-sm transition-colors shadow-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modalDetalleDia', () => ({
        isOpen: false,
        loading: false,
        fechaTexto: '',
        tab: 'ingresos',
        datos: {
            ingresos: 0, gastos: 0, balance: 0,
            metodos: {
                efectivo: { monto: 0, pct: 0 }, tarjeta: { monto: 0, pct: 0 }, transferencia: { monto: 0, pct: 0 }
            },
            sucursal_nombre: '', lista_ingresos: [], lista_gastos: []
        },

        abrirModal(fechaDb, fechaFormat) {
            this.isOpen = true;
            this.fechaTexto = fechaFormat;
            this.loading = true;
            this.tab = 'ingresos'; // Reiniciar a la pestaña ingresos siempre

            // Hacemos la consulta al Backend (AJAX)
            fetch(`/corte-mensual/dia/${fechaDb}`)
                .then(res => res.json())
                .then(data => {
                    this.datos = data;
                    this.loading = false;
                })
                .catch(err => {
                    console.error("Error al cargar los datos:", err);
                    this.loading = false;
                });
        },

        cerrarModal() {
            this.isOpen = false;
        },

        formatMoney(amount) {
            let val = parseFloat(amount);
            if(isNaN(val)) return '$0.00';
            return '$' + val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    }));
});
</script>
@endsection