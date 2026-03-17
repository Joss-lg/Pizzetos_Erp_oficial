<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hoy = Carbon::today();

        // 1. Base de Ventas del día (Pagadas)
        $queryVentas = DB::table('Venta')
            ->whereDate('fecha_hora', $hoy)
            ->where('status', 1);

        $ventasHoy = (float)($queryVentas->sum('total') ?? 0);
        $numVentas = (int)($queryVentas->count());
        
        // 2. Desglose por Métodos: Leemos correctamente de la tabla 'Pago'
        $pagos = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->whereDate('Venta.fecha_hora', $hoy)
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

        // 3. Gastos del día (Usamos 'Gastos', 'fecha' y 'precio' según tu GastosController)
        try {
            $gastosHoy = (float)(DB::table('Gastos')->whereDate('fecha', $hoy)->sum('precio') ?? 0);
        } catch (\Exception $e) {
            $gastosHoy = 0;
        }

        // 4. Dinero Real en Caja (Lo que entró en efectivo menos lo que salió en gastos)
        $efectivoCaja = $efectivoVentas - $gastosHoy;

        $data = [
            'ventasHoy' => $ventasHoy,
            'numVentas' => $numVentas,
            'gastosHoy' => $gastosHoy,
            'efectivoCaja' => $efectivoCaja,
            'efectivoVentas' => $efectivoVentas,
            'tarjetasHoy' => $tarjetasHoy,
            'transferenciasHoy' => $transferenciasHoy,
        ];

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }
}