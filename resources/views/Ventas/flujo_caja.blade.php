@extends('layouts.app')

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-6 right-6 z-50 bg-[#00b300] text-white px-5 py-3.5 rounded shadow-lg flex items-center gap-3">
    <div class="bg-white rounded-full p-0.5"><svg class="w-4 h-4 text-[#00b300]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></div>
    <span class="font-medium text-[15px] italic uppercase tracking-tighter">{{ session('success') }}</span>
</div>
@endif

@if(session('download_pdf'))
<script>
    window.addEventListener('DOMContentLoaded', function() {
        window.open("{{ route('flujo.caja.pdf', session('download_pdf')) }}", "_blank");
    });
</script>
@endif

<div class="w-full min-h-[70vh] font-sans space-y-8">

    @if(!$cajaAbierta)
        {{-- PANTALLA DE APERTURA --}}
        <div class="flex flex-col items-center justify-center mt-12 px-4 text-center">
            <div class="bg-amber-100 w-24 h-24 rounded-[2rem] flex items-center justify-center mb-6 shadow-sm">
                <svg class="w-12 h-12 text-amber-600" fill="currentColor" viewBox="0 0 512 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H448c17.7 0 32-14.3 32-32s-14.3-32-32-32H64z"/></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight mb-2 uppercase italic">Terminal de Cobro Inactiva</h2>
            <p class="text-slate-500 mb-10 max-w-sm font-bold italic text-sm">Es obligatorio establecer el fondo de garantía antes de procesar folios.</p>
            
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-10 w-full max-w-md">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="font-black text-slate-800 italic uppercase text-xs tracking-widest">Apertura de Turno</h3>
                    <a href="{{ route('flujo.caja.historial') }}" class="text-amber-600 hover:text-amber-700 text-[10px] font-black uppercase underline">Historial</a>
                </div>
                <form action="{{ route('flujo.caja.abrir') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="text-left">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Fondo de Inicio (Contado)</label>
                        <div class="relative">
                            <span class="absolute left-6 top-5 text-slate-300 font-black text-xl leading-none">$</span>
                            <input type="number" step="0.01" name="monto_inicial" required value="3000.00" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl pl-12 pr-6 py-6 focus:border-amber-400 text-3xl font-black transition-all outline-none italic tracking-tighter text-slate-700">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-slate-800 text-amber-400 font-black py-6 rounded-3xl shadow-lg hover:bg-slate-900 transition-all uppercase tracking-widest italic text-xs">Iniciar Operaciones</button>
                </form>
            </div>
        </div>
    @else
        {{-- PANTALLA DE CAJA ACTIVA --}}
        <div class="space-y-8">
            {{-- Encabezado --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 text-green-700 border border-green-200 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span> Turno en Curso
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Apertura: {{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->format('d.m.Y / h:i a') }}</span>
                    </div>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">Caja #{{ $cajaAbierta->id_caja }}</h2>
                    <p class="text-sm text-slate-400 font-bold uppercase italic tracking-widest">Responsable: <span class="text-amber-600">{{ $cajaAbierta->cajero_nombre ?? 'Administrador' }}</span></p>
                </div>
                
                <div class="bg-amber-400 px-8 py-5 rounded-[2.5rem] shadow-xl border-b-8 border-amber-600 flex flex-col items-center min-w-[200px]">
                    <span class="text-[10px] font-black uppercase text-amber-900 tracking-widest italic mb-1">Fondo de Reserva</span>
                    <p class="text-3xl font-black text-black italic tracking-tighter leading-none">${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
                </div>
            </div>

            {{-- Métricas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest block mb-3 italic">Venta Total Bruta</span>
                    <h3 class="text-3xl font-black text-slate-800 italic tracking-tighter">${{ number_format($stats['venta_total_bruta'], 2) }}</h3>
                </div>
                
                <div class="bg-slate-800 p-8 rounded-[3rem] shadow-xl text-white">
                    <span class="text-[10px] font-black uppercase text-amber-400 tracking-widest block mb-3 italic">Folios Hoy</span>
                    <h3 class="text-4xl font-black italic text-white tracking-tighter">{{ $stats['num_pedidos'] }}</h3>
                </div>

                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                    <span class="text-[10px] font-black uppercase text-red-400 tracking-widest block mb-3 italic">Egresos (Gastos)</span>
                    <h3 class="text-3xl font-black text-red-500 italic tracking-tighter">-${{ number_format($stats['total_gastos'], 2) }}</h3>
                </div>

                <div class="bg-green-600 p-8 rounded-[3rem] shadow-xl text-white border-b-8 border-green-800">
                    <span class="text-[10px] font-black uppercase text-green-200 tracking-widest block mb-3 italic">Efectivo Real</span>
                    <h3 class="text-4xl font-black italic tracking-tighter leading-none">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</h3>
                    <p class="text-[9px] mt-3 font-black uppercase text-green-200 italic tracking-widest">Neto (EF Ventas - Gastos)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Columna Central: Tabla --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="font-black text-slate-800 uppercase italic tracking-tighter text-xl leading-none">Auditoría de Ventas</h3>
                            <div class="h-1.5 w-16 bg-amber-400 rounded-full"></div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        <th class="px-8 py-5">Orden</th>
                                        <th class="px-8 py-5">Cliente / Servicio</th>
                                        <th class="px-8 py-5">Conciliación</th>
                                        <th class="px-8 py-5 text-right">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-sm italic">
                                    @foreach($ventas_detalle as $venta)
                                        <tr class="hover:bg-gray-50/80 transition-colors {{ $venta->status == 3 ? 'opacity-30' : '' }}">
                                            <td class="px-8 py-6 font-black text-slate-900 leading-none">#{{ $venta->id_venta }}</td>
                                            <td class="px-8 py-6 leading-none">
                                                <div class="flex flex-col">
                                                    <span class="text-slate-700 font-black uppercase tracking-tighter text-[13px]">
                                                        {{ $venta->nombre_cliente_formateado }}
                                                    </span>
                                                    <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('h:i a') }}</span>
                                                </div>
                                            </td>

                                            <td class="px-8 py-6 leading-none">
                                                <div class="flex flex-col gap-2">
                                                    @if($venta->metodos_pago)
                                                        @php 
                                                            $metArr = explode(', ', $venta->metodos_pago);
                                                            $detArr = explode(' + ', $venta->montos_detalle);
                                                        @endphp
                                                        <div class="flex flex-wrap gap-2 font-black text-[9px] uppercase italic">
                                                            @foreach($metArr as $idx => $m)
                                                                <div class="flex items-center rounded-lg overflow-hidden border {{ $m == 'Efectivo' ? 'border-green-200' : ($m == 'Tarjeta' ? 'border-blue-200' : 'border-purple-200') }}">
                                                                    <span class="px-2 py-1 {{ $m == 'Efectivo' ? 'bg-green-100 text-green-700' : ($m == 'Tarjeta' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                                                        {{ $m }}
                                                                    </span>
                                                                    <span class="px-2 py-1 bg-white text-slate-500 border-l border-inherit">{{ $detArr[$idx] ?? '' }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @if($venta->referencias && trim($venta->referencias) != '/' && trim($venta->referencias) != 'S/R')
                                                            <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mt-1">Ref: {{ $venta->referencias }}</p>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-8 py-6 text-right">
                                                <span class="text-lg font-black text-slate-900 tracking-tighter leading-none italic">${{ number_format($venta->total, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Sidebar Derecha --}}
                <div class="space-y-8">
                    {{-- Balances --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 uppercase italic tracking-tighter text-xs mb-6 tracking-widest leading-none">Cómputo por Método</h3>
                        <div class="space-y-3 font-black uppercase italic tracking-tighter text-sm">
                            <div class="flex justify-between p-5 bg-green-50 rounded-3xl border border-green-100 text-green-700">
                                <span>Efectivo</span><span>${{ number_format($stats['efectivo_ventas'], 2) }}</span>
                            </div>
                            <div class="flex justify-between p-5 bg-blue-50 rounded-3xl border border-blue-100 text-blue-700">
                                <span>Tarjetas</span><span>${{ number_format($stats['tarjeta'], 2) }}</span>
                            </div>
                            <div class="flex justify-between p-5 bg-purple-50 rounded-3xl border border-purple-100 text-purple-700">
                                <span>Transferencias</span><span>${{ number_format($stats['transferencia'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Gastos --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 uppercase italic tracking-tighter text-xs mb-6 tracking-widest leading-none">Egresos Reportados</h3>
                        <div class="space-y-3">
                            @forelse($gastos_detalle as $g)
                                <div class="p-5 bg-slate-50 rounded-3xl border border-slate-100">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest leading-none">{{ $g->responsable }}</p>
                                        <p class="font-black text-red-500 italic tracking-tighter leading-none">-${{ number_format($g->precio, 2) }}</p>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700 uppercase italic tracking-tighter leading-tight">{{ $g->descripcion }}</p>
                                </div>
                            @empty
                                <p class="text-[10px] text-center font-black text-slate-300 uppercase tracking-widest italic py-4">Sin egresos registrados</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Panel Arqueo Final --}}
                    <div x-data="{ modal: false, contado: '', esperado: {{ $stats['efectivo_real_en_sobre'] }} }">
                        <div class="bg-slate-800 rounded-[3rem] p-10 shadow-2xl border-t-8 border-amber-400">
                            <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter text-center mb-8">Arqueo Final</h3>
                            
                            <form id="formCerrar" action="{{ route('flujo.caja.cerrar', $cajaAbierta->id_caja) }}" method="POST" class="space-y-6">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center mb-4 italic">Conteo Físico Real</label>
                                    <div class="relative">
                                        <span class="absolute left-6 top-5 text-amber-400 font-black text-2xl leading-none italic">$</span>
                                        <input type="number" step="0.01" name="monto_final" x-model="contado" required class="w-full bg-white/5 border-2 border-white/10 rounded-3xl pl-12 pr-6 py-6 focus:border-amber-400 text-white text-4xl font-black outline-none italic tracking-tighter transition-all">
                                    </div>
                                </div>

                                <div class="bg-white/5 rounded-3xl p-6 space-y-4 border border-white/5 font-black uppercase italic tracking-tighter text-xs">
                                    <div class="flex justify-between items-center"><span class="text-slate-500">Balance Sistema:</span><span class="text-white">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</span></div>
                                    <div class="flex justify-between items-center pt-4 border-t border-white/10 text-base">
                                        <span class="text-amber-400 tracking-widest text-[10px]">Diferencia:</span>
                                        <span :class="(contado - esperado) > 0 ? 'text-green-400' : ((contado - esperado) < 0 ? 'text-red-400' : 'text-white')" x-text="contado === '' ? '$0.00' : '$' + (contado - esperado).toFixed(2)"></span>
                                    </div>
                                </div>

                                <textarea name="observaciones_cierre" rows="2" class="w-full bg-white/5 border-2 border-white/10 rounded-2xl p-4 focus:border-amber-400 text-white text-xs font-black uppercase italic tracking-widest outline-none resize-none" placeholder="Notas del arqueo..."></textarea>

                                <button type="button" @click="if(contado !== '') { modal = true } else { alert('Ingresa el monto contado físico.') }" class="w-full bg-amber-400 text-slate-900 font-black py-6 rounded-3xl shadow-xl hover:bg-amber-500 transition-all uppercase tracking-widest italic text-xs leading-none">Cerrar Terminal</button>
                                
                                <div class="text-center">
                                    <a href="{{ route('flujo.caja.pdf', $cajaAbierta->id_caja) }}" target="_blank" class="text-amber-400/60 hover:text-amber-400 text-[9px] font-black uppercase underline italic tracking-widest">Vista Previa Reporte</a>
                                </div>

                                {{-- Modal --}}
                                <div x-show="modal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-xl">
                                    <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-sm p-12 text-center">
                                        <div class="w-20 h-20 bg-red-50 rounded-3xl mx-auto mb-6 flex items-center justify-center border-b-4 border-red-100">
                                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-900 italic uppercase tracking-tighter mb-4 leading-none">¿Ejecutar Cierre?</h3>
                                        <p class="text-slate-400 text-xs font-bold uppercase italic tracking-widest mb-10 leading-relaxed text-balance">Esta operación es irreversible. Se bloquearán los folios y el fondo inicial de ${{ number_format($cajaAbierta->monto_inicial, 2) }} saldrá del balance.</p>
                                        <div class="flex gap-4">
                                            <button @click="modal = false" type="button" class="flex-1 bg-slate-100 text-slate-400 font-black py-5 rounded-3xl text-[10px] uppercase tracking-widest italic leading-none">Cancelar</button>
                                            <button type="button" @click="document.getElementById('formCerrar').submit()" class="flex-1 bg-red-600 text-white font-black py-5 rounded-3xl text-[10px] uppercase tracking-widest italic shadow-xl shadow-red-200 leading-none">Confirmar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection