<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $id_sucursal = 1; 

        // 1. BUSCAMOS LA CAJA ABIERTA
        $cajaAbierta = DB::table('Caja')
            ->where('status', 1)
            ->where('id_suc', $id_sucursal)
            ->first();

        // 2. SI LA CAJA ESTÁ CERRADA: MANDAMOS PUROS CEROS
        if (!$cajaAbierta) {
            $data = [
                'ventasHoy' => 0,
                'numVentas' => 0,
                'gastosHoy' => 0,
                'efectivoCaja' => 0,
                'efectivoVentas' => 0,
                'tarjetasHoy' => 0,
                'transferenciasHoy' => 0,
                'cajaAbierta' => false // Bandera para la vista
            ];

            if ($request->ajax()) {
                return response()->json($data);
            }

            return view('dashboard', $data);
        }

        // ==============================================================
        // 3. SI HAY CAJA ABIERTA: FILTRAMOS TODO POR $id_caja
        // ==============================================================
        $id_caja = $cajaAbierta->id_caja;

        // A. Base de Ventas de la CAJA (Pagadas)
        $queryVentas = DB::table('Venta')
            ->where('id_caja', $id_caja)
            ->where('status', 1);

        $ventasHoy = (float)($queryVentas->sum('total') ?? 0);
        $numVentas = (int)($queryVentas->count());
        
        // B. Desglose por Métodos: Leemos de la tabla 'Pago' unidos por 'id_caja'
        $pagos = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->where('Venta.id_caja', $id_caja)
            ->where('Venta.status', 1) // Solo ventas concretadas
            ->select('Pago.id_metpago', DB::raw('SUM(Pago.monto) as total_monto'))
            ->groupBy('Pago.id_metpago')
            ->get();

        $efectivoVentas = 0;
        $tarjetasHoy = 0;
        $transferenciasHoy = 0;

        foreach ($pagos as $pago) {
            if ($pago->id_metpago == 1) $tarjetasHoy = (float)$pago->total_monto;       // 1 = Tarjeta
            if ($pago->id_metpago == 2) $efectivoVentas = (float)$pago->total_monto;    // 2 = Efectivo
            if ($pago->id_metpago == 3) $transferenciasHoy = (float)$pago->total_monto; // 3 = Transferencia
        }

        // C. Gastos de la CAJA
        try {
            $gastosHoy = (float)(DB::table('Gastos')
                ->where('id_caja', $id_caja) 
                ->sum('precio') ?? 0);
        } catch (\Exception $e) {
            // Fallback por si la tabla gastos aún no tiene la columna id_caja
            $gastosHoy = (float)(DB::table('Gastos')->whereDate('fecha', Carbon::today())->sum('precio') ?? 0);
        }

        // D. Dinero Real Generado (Ventas en efectivo - Gastos)
        // Ya no tomamos en cuenta el monto inicial aquí.
        $efectivoCaja = $efectivoVentas - $gastosHoy;

        $data = [
            'ventasHoy' => $ventasHoy,
            'numVentas' => $numVentas,
            'gastosHoy' => $gastosHoy,
            'efectivoCaja' => $efectivoCaja,
            'efectivoVentas' => $efectivoVentas,
            'tarjetasHoy' => $tarjetasHoy,
            'transferenciasHoy' => $transferenciasHoy,
            'cajaAbierta' => true
        ];

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }
}