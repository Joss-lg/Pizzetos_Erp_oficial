@extends('layouts.app')

@section('content')
<style>
    /* CSS ESTÁNDAR PARA FORZAR EL DISEÑO EN EL SERVIDOR */
    .pizzetos-card {
        background: #ffffff;
        border-radius: 50px !important; /* Bordes súper redondeados */
        border: 1px solid #f1f5f9;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        padding: 2.5rem;
        margin-bottom: 1.5rem;
    }
    .pizzetos-card-dark {
        background: #1e293b !important; /* Slate muy oscuro */
        border-radius: 50px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        padding: 3rem;
        color: white;
    }
    .pizzetos-input {
        background: rgba(255,255,255,0.05);
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: 30px;
        padding: 1.5rem;
        color: white;
        font-weight: 900;
        font-style: italic;
        width: 100%;
        outline: none;
    }
    .pizzetos-btn {
        background: #fbbf24 !important;
        border-radius: 30px !important;
        padding: 1.5rem;
        font-weight: 900;
        text-transform: uppercase;
        font-style: italic;
        color: #000;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        width: 100%;
    }
    .pizzetos-btn:hover { transform: scale(1.03); background: #f59e0b !important; }
    .kpi-title { font-size: 11px; font-weight: 900; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; font-style: italic; }
    .kpi-value { font-size: 2.5rem; font-weight: 900; font-style: italic; letter-spacing: -1px; }
</style>

<div class="w-full space-y-10 pb-12">

    @if(!$cajaAbierta)
        {{-- VISTA APERTURA --}}
        <div class="flex flex-col items-center justify-center py-20">
            <div class="pizzetos-card" style="max-width: 450px; text-align: center;">
                <h2 class="text-3xl font-black italic uppercase text-slate-800 mb-6">Apertura de Turno</h2>
                <form action="{{ route('flujo.caja.abrir') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 2rem;">
                        <label class="kpi-title block mb-2">Fondo Inicial</label>
                        <input type="number" step="0.01" name="monto_inicial" value="3000.00" class="pizzetos-input" style="color: #1e293b; background: #f8fafc; border: 2px solid #e2e8f0; text-align: center; font-size: 2rem;">
                    </div>
                    <button type="submit" class="pizzetos-btn">Iniciar Operaciones</button>
                </form>
            </div>
        </div>
    @else
        {{-- HEADER DASHBOARD --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 px-4">
            <div>
                <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-[10px] font-black uppercase italic tracking-widest border border-green-200">Sistema en Línea</span>
                <h1 class="text-6xl font-black italic uppercase tracking-tighter text-slate-900 mt-4">Caja #{{ $cajaAbierta->id_caja }}</h1>
                <p class="text-slate-400 font-bold uppercase italic tracking-widest">Responsable: <span class="text-amber-500">{{ $cajaAbierta->cajero_nombre }}</span></p>
            </div>
            
            <div class="pizzetos-card" style="background: #fbbf24; border: none; min-width: 280px; text-align: center; padding: 1.5rem 2rem; border-bottom: 8px solid #d97706;">
                <span class="text-[10px] font-black uppercase text-amber-900 italic tracking-widest">Fondo de Reserva</span>
                <div class="text-4xl font-black italic text-black tracking-tighter">${{ number_format($cajaAbierta->monto_inicial, 2) }}</div>
            </div>
        </div>

        {{-- RESUMEN KPI --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-4">
            <div class="pizzetos-card">
                <span class="kpi-title">Venta Bruta</span>
                <div class="kpi-value text-slate-900">${{ number_format($stats['venta_total_bruta'], 2) }}</div>
            </div>
            <div class="pizzetos-card" style="background: #1e293b; border: none;">
                <span class="kpi-title" style="color: #fbbf24;">Folios Emitidos</span>
                <div class="kpi-value text-white">{{ $stats['num_pedidos'] }}</div>
            </div>
            <div class="pizzetos-card">
                <span class="kpi-title text-red-400">Gastos Reportados</span>
                <div class="kpi-value text-red-500">-${{ number_format($stats['total_gastos'], 2) }}</div>
            </div>
            <div class="pizzetos-card" style="background: #10b981; border: none; border-bottom: 8px solid #047857;">
                <span class="kpi-title" style="color: #ecfdf5;">Efectivo Real</span>
                <div class="kpi-value text-white">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</div>
                <p class="text-[8px] font-black uppercase text-white opacity-60 mt-2">Neto (Ventas EF - Gastos)</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 px-4">
            {{-- TABLA DETALLE --}}
            <div class="lg:col-span-2">
                <div class="pizzetos-card" style="padding: 0; overflow: hidden;">
                    <div style="padding: 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="text-xl font-black italic uppercase text-slate-800">Auditoría de Operaciones</h3>
                        <div style="width: 60px; height: 6px; background: #fbbf24; border-radius: 10px;"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50">
                                <tr class="text-[9px] font-black text-slate-400 uppercase italic">
                                    <th class="px-8 py-5">Folio</th>
                                    <th class="px-8 py-5">Cliente / Servicio</th>
                                    <th class="px-8 py-5">Pagos</th>
                                    <th class="px-8 py-5 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($ventas_detalle as $venta)
                                    <tr class="italic {{ $venta->status == 3 ? 'opacity-30' : '' }}">
                                        <td class="px-8 py-6 font-black text-slate-900">#{{ $venta->id_venta }}</td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <span class="text-slate-800 font-black uppercase text-sm tracking-tighter">{{ $venta->nombre_cliente_formateado }}</span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('h:i a') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(explode(', ', $venta->metodos_pago ?? '') as $m)
                                                    <span class="px-2 py-1 rounded-md text-[8px] font-black uppercase border {{ $m == 'Efectivo' ? 'bg-green-50 text-green-600 border-green-200' : ($m == 'Tarjeta' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-purple-50 text-purple-600 border-purple-200') }}">
                                                        {{ $m }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right font-black text-xl text-slate-900 tracking-tighter">
                                            ${{ number_format($venta->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PANEL ARQUEO --}}
            <div class="space-y-6">
                <div class="pizzetos-card" style="padding: 2rem;">
                    <h3 class="kpi-title mb-6 block text-center">Resumen de Métodos</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between font-black italic text-sm">
                            <span class="text-slate-400">EFECTIVO:</span><span class="text-slate-900">${{ number_format($stats['efectivo_ventas'], 2) }}</span>
                        </div>
                        <div class="flex justify-between font-black italic text-sm">
                            <span class="text-slate-400">TARJETAS:</span><span class="text-slate-900">${{ number_format($stats['tarjeta'], 2) }}</span>
                        </div>
                        <div class="flex justify-between font-black italic text-sm">
                            <span class="text-slate-400">TRANSF.:</span><span class="text-slate-900">${{ number_format($stats['transferencia'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <div x-data="{ modal: false, contado: '', esperado: {{ $stats['efectivo_real_en_sobre'] }} }">
                    <div class="pizzetos-card-dark" style="border-top: 10px solid #fbbf24;">
                        <h3 class="text-3xl font-black italic uppercase text-center mb-8">Arqueo Final</h3>
                        <form id="formCerrar" action="{{ route('flujo.caja.cerrar', $cajaAbierta->id_caja) }}" method="POST">
                            @csrf
                            <div class="text-center mb-8">
                                <label class="kpi-title block mb-4" style="color: #94a3b8;">Conteo Físico Real</label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 20px; top: 20px; color: #fbbf24; font-size: 2rem; font-weight: 900;">$</span>
                                    <input type="number" step="0.01" name="monto_final" x-model="contado" required class="pizzetos-input" style="font-size: 3rem; text-align: center; padding-left: 3rem;">
                                </div>
                            </div>

                            <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 20px; margin-bottom: 2rem;">
                                <div class="flex justify-between text-[10px] font-black uppercase italic mb-2">
                                    <span style="color: #64748b;">En Sistema:</span>
                                    <span class="text-white">${{ number_format($stats['efectivo_real_en_sobre'], 2) }}</span>
                                </div>
                                <div class="flex justify-between font-black italic border-t border-white/10 pt-3">
                                    <span style="color: #fbbf24; font-size: 11px;">DIFERENCIA:</span>
                                    <span style="font-size: 1.2rem;" :class="(contado - esperado) > 0 ? 'text-green-400' : ((contado - esperado) < 0 ? 'text-red-400' : 'text-white')" x-text="contado === '' ? '$0.00' : '$' + (contado - esperado).toFixed(2)"></span>
                                </div>
                            </div>

                            <button type="button" @click="if(contado !== '') { modal = true } else { alert('Ingresa el monto físico.') }" class="pizzetos-btn">Finalizar Turno</button>
                            <div style="text-align: center; margin-top: 1.5rem;">
                                <a href="{{ route('flujo.caja.pdf', $cajaAbierta->id_caja) }}" target="_blank" style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 900; text-transform: uppercase; text-decoration: underline;">Vista Previa PDF</a>
                            </div>

                            {{-- MODAL --}}
                            <div x-show="modal" x-cloak style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1rem;">
                                <div class="pizzetos-card" style="max-width: 400px; text-align: center;">
                                    <h3 class="text-2xl font-black italic uppercase text-slate-900 mb-4">¿Confirmar?</h3>
                                    <p class="text-slate-400 font-bold italic text-sm mb-8 uppercase">El fondo de reserva de ${{ number_format($cajaAbierta->monto_inicial, 2) }} debe ser retirado ahora.</p>
                                    <div class="flex gap-4">
                                        <button @click="modal = false" type="button" class="pizzetos-btn" style="background: #e2e8f0 !important; font-size: 10px;">Cancelar</button>
                                        <button type="button" @click="document.getElementById('formCerrar').submit()" class="pizzetos-btn" style="background: #ef4444 !important; color: white !important; font-size: 10px;">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection