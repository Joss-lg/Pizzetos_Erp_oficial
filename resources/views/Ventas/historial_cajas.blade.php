@extends('layouts.app')

@section('content')
<div class="w-full p-8" x-data="historialCajasApp()">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">Historial de Cortes</h2>
            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mt-2">Registro de turnos y arqueos finalizados</p>
        </div>
        <a href="{{ route('flujo.caja.index') }}" class="bg-slate-800 hover:bg-black text-white px-6 py-3 rounded-2xl text-xs font-black uppercase italic tracking-tighter transition-all shadow-lg active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver a Caja Actual
        </a>
    </div>

    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Folio de Caja</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Responsable</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Apertura / Cierre</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Monto Final</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase italic tracking-widest text-center">Reporte</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                @foreach($cajas as $c)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    {{-- FOLIO VIRTUAL DE CAJA --}}
                    <td class="px-8 py-6">
                        <span class="font-black text-slate-900 text-lg italic tracking-tighter">
                            {{ $c->folio_virtual }}
                        </span>
                    </td>

                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-black text-[10px]">
                                {{ substr($c->cajero_nombre, 0, 1) }}
                            </div>
                            <span class="font-bold text-slate-700 uppercase italic text-xs">{{ $c->cajero_nombre }}</span>
                        </div>
                    </td>

                    <td class="px-8 py-6 leading-tight">
                        <div class="text-[10px] font-black text-slate-400 uppercase italic mb-1">Inició: <span class="text-slate-600">{{ \Carbon\Carbon::parse($c->fecha_apertura)->format('d/m/y H:i') }}</span></div>
                        <div class="text-[10px] font-black text-slate-400 uppercase italic">Cerró: <span class="text-slate-600">{{ \Carbon\Carbon::parse($c->fecha_cierre)->format('d/m/y H:i') }}</span></div>
                    </td>

                    <td class="px-8 py-6 font-black text-2xl text-slate-800 tracking-tighter italic">
                        ${{ number_format($c->monto_final, 2) }}
                    </td>

                    <td class="px-8 py-6 text-center">
                        {{-- BOTÓN PDF CON POPUP --}}
                        <button @click="imprimirCortePop({{ $c->id_caja }})" 
                           class="inline-flex items-center gap-2 bg-white border-2 border-slate-100 hover:border-amber-400 hover:text-amber-600 text-slate-400 px-5 py-2.5 rounded-2xl shadow-sm text-[10px] font-black uppercase italic transition-all group-hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Ver Reporte
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($cajas->isEmpty())
            <div class="py-20 text-center">
                <p class="text-slate-400 font-black uppercase italic tracking-widest text-sm">No hay cierres registrados aún</p>
            </div>
        @endif
    </div>
    
    <div class="mt-8 pizzetos-pagination">
        {{ $cajas->links() }} 
    </div>
</div>

<script>
    function historialCajasApp() {
        return {
            imprimirCortePop(id) {
                const width = 850;
                const height = 900;
                const left = (window.screen.width / 2) - (width / 2);
                const top = (window.screen.height / 2) - (height / 2);
                
                // CORRECCIÓN: Se agrega /venta/ para coincidir con la ruta definida en web.php
                window.open(
                    "{{ url('/venta/flujo-caja/pdf') }}/" + id, 
                    'ReporteCaja', 
                    `width=${width},height=${height},left=${left},top=${top},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`
                );
            }
        }
    }
</script>

<style>
    /* Personalización de la paginación de Laravel */
    .pizzetos-pagination nav div div p { font-weight: 900 !important; font-style: italic !important; text-transform: uppercase !important; font-size: 10px !important; }
    .pizzetos-pagination nav { border-radius: 20px !important; overflow: hidden !important; }
</style>
@endsection