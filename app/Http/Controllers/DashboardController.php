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
            // Top pizzas all-time (shown even with caja closed)
            $topPizzasCerrada = DB::table('DetalleVenta')
                ->join('Venta', 'DetalleVenta.id_venta', '=', 'Venta.id_venta')
                ->join('Pizzas', 'DetalleVenta.id_pizza', '=', 'Pizzas.id_pizza')
                ->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')
                ->where('Venta.id_suc', $id_sucursal)
                ->where('Venta.status', 1)
                ->whereNotNull('DetalleVenta.id_pizza')
                ->select('Especialidades.nombre', DB::raw('SUM(DetalleVenta.cantidad) as total'))
                ->groupBy('Especialidades.id_esp', 'Especialidades.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $data = [
                'ventasHoy' => 0,
                'numVentas' => 0,
                'gastosHoy' => 0,
                'efectivoCaja' => 0,
                'efectivoVentas' => 0,
                'tarjetasHoy' => 0,
                'transferenciasHoy' => 0,
                'cajaAbierta' => false,
                'topPizzas' => $topPizzasCerrada,
                'topProductos' => collect(),
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

        // E. Top pizzas all-time (por especialidad)
        $topPizzas = DB::table('DetalleVenta')
            ->join('Venta', 'DetalleVenta.id_venta', '=', 'Venta.id_venta')
            ->join('Pizzas', 'DetalleVenta.id_pizza', '=', 'Pizzas.id_pizza')
            ->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')
            ->where('Venta.id_suc', $id_sucursal)
            ->where('Venta.status', 1)
            ->whereNotNull('DetalleVenta.id_pizza')
            ->select('Especialidades.nombre', DB::raw('SUM(DetalleVenta.cantidad) as total'))
            ->groupBy('Especialidades.id_esp', 'Especialidades.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // F. Top productos all-time (todas las categorías)
        $productQueries = [
            DB::table('DetalleVenta as dv')
                ->join('Venta as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('Pizzas as p', 'dv.id_pizza', '=', 'p.id_pizza')
                ->join('Especialidades as e', 'p.id_esp', '=', 'e.id_esp')
                ->where('v.id_suc', $id_sucursal)->where('v.status', 1)->whereNotNull('dv.id_pizza')
                ->select('e.nombre', DB::raw('SUM(dv.cantidad) as total'), DB::raw('"Pizza" as categoria'))
                ->groupBy('e.id_esp', 'e.nombre'),

            DB::table('DetalleVenta as dv')
                ->join('Venta as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('Hamburguesas as h', 'dv.id_hamb', '=', 'h.id_hamb')
                ->where('v.id_suc', $id_sucursal)->where('v.status', 1)->whereNotNull('dv.id_hamb')
                ->select('h.paquete as nombre', DB::raw('SUM(dv.cantidad) as total'), DB::raw('"Hamburguesa" as categoria'))
                ->groupBy('h.id_hamb', 'h.paquete'),

            DB::table('DetalleVenta as dv')
                ->join('Venta as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('Alitas as a', 'dv.id_alis', '=', 'a.id_alis')
                ->where('v.id_suc', $id_sucursal)->where('v.status', 1)->whereNotNull('dv.id_alis')
                ->select('a.orden as nombre', DB::raw('SUM(dv.cantidad) as total'), DB::raw('"Alitas" as categoria'))
                ->groupBy('a.id_alis', 'a.orden'),

            DB::table('DetalleVenta as dv')
                ->join('Venta as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('Costillas as c', 'dv.id_cos', '=', 'c.id_cos')
                ->where('v.id_suc', $id_sucursal)->where('v.status', 1)->whereNotNull('dv.id_cos')
                ->select('c.orden as nombre', DB::raw('SUM(dv.cantidad) as total'), DB::raw('"Costillas" as categoria'))
                ->groupBy('c.id_cos', 'c.orden'),

            DB::table('DetalleVenta as dv')
                ->join('Venta as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('Spaguetty as s', 'dv.id_spag', '=', 's.id_spag')
                ->where('v.id_suc', $id_sucursal)->where('v.status', 1)->whereNotNull('dv.id_spag')
                ->select('s.orden as nombre', DB::raw('SUM(dv.cantidad) as total'), DB::raw('"Spaguetti" as categoria'))
                ->groupBy('s.id_spag', 's.orden'),

            DB::table('DetalleVenta as dv')
                ->join('Venta as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('OrdenDePapas as p', 'dv.id_papa', '=', 'p.id_papa')
                ->where('v.id_suc', $id_sucursal)->where('v.status', 1)->whereNotNull('dv.id_papa')
                ->select('p.orden as nombre', DB::raw('SUM(dv.cantidad) as total'), DB::raw('"Papas" as categoria'))
                ->groupBy('p.id_papa', 'p.orden'),
        ];

        $topProductos = collect();
        foreach ($productQueries as $q) {
            $topProductos = $topProductos->merge($q->get());
        }
        $topProductos = $topProductos->sortByDesc('total')->take(8)->values();

        $data = [
            'ventasHoy' => $ventasHoy,
            'numVentas' => $numVentas,
            'gastosHoy' => $gastosHoy,
            'efectivoCaja' => $efectivoCaja,
            'efectivoVentas' => $efectivoVentas,
            'tarjetasHoy' => $tarjetasHoy,
            'transferenciasHoy' => $transferenciasHoy,
            'cajaAbierta' => true,
            'topPizzas' => $topPizzas,
            'topProductos' => $topProductos,
        ];

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }
}