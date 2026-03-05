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
        $id_sucursal = Auth::user()->id_suc;
        
        $cajaAbierta = DB::table('Caja')
            ->leftJoin('Empleados', 'Caja.id_emp', '=', 'Empleados.id_emp')
            ->select('Caja.*', 'Empleados.nickName as cajero_nombre')
            ->where('Caja.status', 1)
            ->where('Caja.id_suc', $id_sucursal)
            ->first();

        if (!$cajaAbierta) {
            return view('Ventas.flujo_caja', ['cajaAbierta' => null]);
        }

        $stats = [
            'num_ventas' => 0, 
            'total_gastos' => 0, 
            'venta_total' => 0, 
            'efectivo' => 0, 
            'tarjeta' => 0, 
            'transferencia' => 0
        ];

        $gastos_detalle = DB::table('Gastos')->where('id_caja', $cajaAbierta->id_caja)->get();
        $stats['total_gastos'] = $gastos_detalle->sum('precio');

        $ventas_detalle = DB::table('Venta')->where('id_caja', $cajaAbierta->id_caja)->get();
        $stats['num_ventas'] = $ventas_detalle->count();
        $stats['venta_total'] = $ventas_detalle->sum('total');

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

        return view('Ventas.flujo_caja', compact('cajaAbierta', 'stats', 'ventas_detalle', 'gastos_detalle'));
    }

    /**
     * Muestra el historial de cajas cerradas de la sucursal para reimpresión.
     */
    public function historial()
    {
        $id_sucursal = Auth::user()->id_suc;

        $cajas = DB::table('Caja')
            ->leftJoin('Empleados', 'Caja.id_emp', '=', 'Empleados.id_emp')
            ->select('Caja.*', 'Empleados.nickName as cajero_nombre')
            ->where('Caja.id_suc', $id_sucursal)
            ->where('Caja.status', 0) // Solo cajas cerradas
            ->orderBy('Caja.fecha_cierre', 'desc')
            ->paginate(15);

        return view('Ventas.historial_cajas', compact('cajas'));
    }

    /**
     * Procesa la apertura de una nueva caja.
     */
    public function abrirCaja(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255'
        ]);

        DB::table('Caja')->insert([
            'id_suc' => Auth::user()->id_suc,
            'id_emp' => Auth::user()->id_emp,
            'fecha_apertura' => Carbon::now(),
            'monto_inicial' => $request->monto_inicial,
            'status' => 1,
            'observaciones_apertura' => $request->observaciones ?? ''
        ]);

        return redirect()->route('flujo.caja.index')->with('success', 'Caja abierta exitosamente.');
    }

    /**
     * Procesa el cierre de la caja actual.
     */
    public function cerrarCaja(Request $request, $id)
    {
        $request->validate([
            'monto_final' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|string'
        ]);

        DB::table('Caja')->where('id_caja', $id)->update([
            'fecha_cierre' => Carbon::now(),
            'monto_final' => $request->monto_final,
            'observaciones_cierre' => $request->observaciones_cierre,
            'status' => 0 
        ]);

        return redirect()->route('flujo.caja.index')
            ->with('success', 'Caja cerrada correctamente.')
            ->with('download_pdf', $id);
    }

    /**
     * Genera y transmite el PDF del reporte de cierre.
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
        ];

        $pdf = Pdf::loadView('Ventas.pdf_caja', compact('caja', 'stats'));
        return $pdf->stream('Reporte_Caja_'.$id.'.pdf');
    }
}