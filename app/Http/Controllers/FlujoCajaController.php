<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FlujoCajaController extends Controller
{
    /**
     * Muestra el panel de control de caja actual o la vista de apertura.
     */
    public function index()
    {
        $id_sucursal = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES) PARA CAJA ÚNICA
        
        $cajaAbierta = DB::table('Caja')
            ->leftJoin('Empleados', 'Caja.id_emp', '=', 'Empleados.id_emp')
            ->select('Caja.*', 'Empleados.nickName as cajero_nombre')
            ->where('Caja.status', 1)
            ->where('Caja.id_suc', $id_sucursal)
            ->first();

        if (!$cajaAbierta) {
            return view('Ventas.flujo_caja', ['cajaAbierta' => null]);
        }

        // Estadísticas iniciales
        $stats = [
            'num_ventas' => 0, 
            'total_gastos' => 0, 
            'venta_total' => 0, 
            'efectivo' => 0,      // Solo ventas en efectivo
            'tarjeta' => 0,       // Solo ventas en tarjeta
            'transferencia' => 0,  // Solo ventas en transferencia
            'monto_esperado_cajon' => 0 // Fondo + Efectivo - Gastos
        ];

        // 1. Obtener Gastos de esta caja
        $gastos_detalle = DB::table('Gastos')->where('id_caja', $cajaAbierta->id_caja)->get();
        $stats['total_gastos'] = $gastos_detalle->sum('precio');

        // 2. Obtener Ventas de esta caja
        $ventas_detalle = DB::table('Venta')->where('id_caja', $cajaAbierta->id_caja)->get();
        $stats['num_ventas'] = $ventas_detalle->count();
        $stats['venta_total'] = $ventas_detalle->sum('total');

        // 3. Desglose preciso por método de pago
        $pagos = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->join('MetodosPago', 'Pago.id_metpago', '=', 'MetodosPago.id_metpago')
            ->where('Venta.id_caja', $cajaAbierta->id_caja)
            ->select('MetodosPago.metodo', DB::raw('SUM(Pago.monto) as total_monto'))
            ->groupBy('MetodosPago.metodo')
            ->pluck('total_monto', 'metodo');

        $stats['efectivo'] = $pagos['Efectivo'] ?? 0;
        $stats['tarjeta'] = $pagos['Tarjeta'] ?? 0;
        $stats['transferencia'] = $pagos['Transferencia'] ?? 0;

        // 4. Lógica de "Dinero en Cajón" (Fondo + Ventas Efectivo - Gastos)
        $stats['monto_esperado_cajon'] = ($cajaAbierta->monto_inicial + $stats['efectivo']) - $stats['total_gastos'];

        return view('Ventas.flujo_caja', compact('cajaAbierta', 'stats', 'ventas_detalle', 'gastos_detalle'));
    }

    /**
     * Muestra el historial de cajas cerradas.
     */
    public function historial()
    {
        $id_sucursal = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES)

        $cajas = DB::table('Caja')
            ->leftJoin('Empleados', 'Caja.id_emp', '=', 'Empleados.id_emp')
            ->select('Caja.*', 'Empleados.nickName as cajero_nombre')
            ->where('Caja.id_suc', $id_sucursal)
            ->where('Caja.status', 0)
            ->orderBy('Caja.fecha_cierre', 'desc')
            ->paginate(15);

        return view('Ventas.historial_cajas', compact('cajas'));
    }

    /**
     * Procesa la apertura de una nueva caja con el fondo inicial.
     */
    public function abrirCaja(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255'
        ]);

        $id_sucursal = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES)

        // Evitar doble apertura
        $existe = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->exists();
        if($existe) return redirect()->back()->with('error', 'Ya existe una caja abierta.');

        DB::table('Caja')->insert([
            'id_suc' => $id_sucursal,
            'id_emp' => Auth::user()->id_emp,
            'fecha_apertura' => Carbon::now(),
            'monto_inicial' => $request->monto_inicial,
            'status' => 1,
            'observaciones_apertura' => $request->observaciones ?? 'Apertura de turno'
        ]);

        return redirect()->route('flujo.caja.index')->with('success', 'Caja abierta con $' . number_format($request->monto_inicial, 2));
    }

    /**
     * Procesa el cierre de la caja calculando diferencias.
     */
    public function cerrarCaja(Request $request, $id)
    {
        $request->validate([
            'monto_final' => 'required|numeric|min:0', // Esto es el conteo físico del cajero
            'observaciones_cierre' => 'nullable|string'
        ]);

        DB::table('Caja')->where('id_caja', $id)->update([
            'fecha_cierre' => Carbon::now(),
            'monto_final' => $request->monto_final,
            'observaciones_cierre' => $request->observaciones_cierre,
            'status' => 0 
        ]);

        return redirect()->route('flujo.caja.index')
            ->with('success', 'Caja cerrada correctamente. Se ha generado el reporte.')
            ->with('download_pdf', $id);
    }

    /**
     * Genera el PDF detallando fondo, efectivo, tarjetas y gastos.
     */
    public function descargarPdf($id)
    {
        $caja = DB::table('Caja')
            ->leftJoin('Empleados', 'Caja.id_emp', '=', 'Empleados.id_emp')
            ->select('Caja.*', 'Empleados.nickName as cajero_nombre')
            ->where('id_caja', $id)->first();

        if (!$caja) abort(404);

        $gastos = DB::table('Gastos')->where('id_caja', $id)->sum('precio');
        $ventas = DB::table('Venta')->where('id_caja', $id)->get();
        
        $pagos = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->join('MetodosPago', 'Pago.id_metpago', '=', 'MetodosPago.id_metpago')
            ->where('Venta.id_caja', $id)
            ->select('MetodosPago.metodo', DB::raw('SUM(Pago.monto) as total_monto'))
            ->groupBy('MetodosPago.metodo')->pluck('total_monto', 'metodo');

        $stats = [
            'num_ventas' => $ventas->count(),
            'venta_total' => $ventas->sum('total'),
            'total_gastos' => $gastos,
            'efectivo' => $pagos['Efectivo'] ?? 0,
            'tarjeta' => $pagos['Tarjeta'] ?? 0,
            'transferencia' => $pagos['Transferencia'] ?? 0,
            'fondo_inicial' => $caja->monto_inicial,
            'monto_esperado' => ($caja->monto_inicial + ($pagos['Efectivo'] ?? 0)) - $gastos,
            'monto_real' => $caja->monto_final,
            'diferencia' => $caja->monto_final - (($caja->monto_inicial + ($pagos['Efectivo'] ?? 0)) - $gastos)
        ];

        $pdf = Pdf::loadView('Ventas.pdf_caja', compact('caja', 'stats'));
        return $pdf->stream('Corte_Caja_'.$caja->fecha_cierre.'.pdf');
    }
}