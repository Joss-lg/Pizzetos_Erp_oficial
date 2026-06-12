<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CorteController extends Controller
{
    public function index(Request $request)
    {
        $mesSeleccionado = $request->input('mes', Carbon::now()->format('Y-m'));
        $fecha = Carbon::createFromFormat('Y-m', $mesSeleccionado);
        $year = $fecha->year;
        $month = $fecha->month;
        
        $sucursalId = Auth::user()->id_suc;

        // TOTALES DEL MES
        $totalGastos = DB::table('Gastos')->where('id_suc', $sucursalId)->whereYear('fecha', $year)->whereMonth('fecha', $month)->sum('precio');

        $pagos = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->where('Venta.id_suc', $sucursalId)->where('Venta.status', '!=', 0)
            ->whereYear('Venta.fecha_hora', $year)->whereMonth('Venta.fecha_hora', $month)
            ->select('Pago.id_metpago', DB::raw('SUM(Pago.monto) as total_monto'))
            ->groupBy('Pago.id_metpago')->get();

        $ingresosEfectivo = 0; $ingresosTarjeta = 0; $ingresosTransferencia = 0;
        foreach ($pagos as $pago) {
            if ($pago->id_metpago == 1) $ingresosEfectivo = $pago->total_monto;
            if ($pago->id_metpago == 2) $ingresosTarjeta = $pago->total_monto;
            if ($pago->id_metpago == 3) $ingresosTransferencia = $pago->total_monto;
        }

        $totalIngresos = $ingresosEfectivo + $ingresosTarjeta + $ingresosTransferencia;
        $balanceNeto = $totalIngresos - $totalGastos;

        $pctEfectivo = $totalIngresos > 0 ? round(($ingresosEfectivo / $totalIngresos) * 100) : 0;
        $pctTarjeta = $totalIngresos > 0 ? round(($ingresosTarjeta / $totalIngresos) * 100) : 0;
        $pctTransferencia = $totalIngresos > 0 ? round(($ingresosTransferencia / $totalIngresos) * 100) : 0;

        // DESGLOSE DIARIO
        $diasEnMes = $fecha->daysInMonth;
        $desgloseDiario = [];

        for ($i = 1; $i <= $diasEnMes; $i++) {
            $desgloseDiario[$i] = [
                'dia' => $i,
                'fecha_format' => $i . ' de ' . $fecha->translatedFormat('F'),
                'fecha_db' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT), // <-- PARA EL BOTÓN OJO
                'efectivo' => 0, 'tarjeta' => 0, 'transferencia' => 0, 'gastos' => 0,
            ];
        }

        $pagosDiarios = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->where('Venta.id_suc', $sucursalId)->where('Venta.status', '!=', 0)
            ->whereYear('Venta.fecha_hora', $year)->whereMonth('Venta.fecha_hora', $month)
            ->select(DB::raw('DAY(Venta.fecha_hora) as dia'), 'Pago.id_metpago', DB::raw('SUM(Pago.monto) as total_monto'))
            ->groupBy('dia', 'Pago.id_metpago')->get();

        foreach ($pagosDiarios as $pd) {
            if ($pd->id_metpago == 1) $desgloseDiario[$pd->dia]['efectivo'] = $pd->total_monto;
            if ($pd->id_metpago == 2) $desgloseDiario[$pd->dia]['tarjeta'] = $pd->total_monto;
            if ($pd->id_metpago == 3) $desgloseDiario[$pd->dia]['transferencia'] = $pd->total_monto;
        }

        $gastosDiarios = DB::table('Gastos')
            ->where('id_suc', $sucursalId)->whereYear('fecha', $year)->whereMonth('fecha', $month)
            ->select(DB::raw('DAY(fecha) as dia'), DB::raw('SUM(precio) as total_gastos'))
            ->groupBy('dia')->get();

        foreach ($gastosDiarios as $gd) {
            $desgloseDiario[$gd->dia]['gastos'] = $gd->total_gastos;
        }

        return view('Corte.index', compact('mesSeleccionado','totalIngresos','totalGastos','balanceNeto','ingresosEfectivo','ingresosTarjeta','ingresosTransferencia','pctEfectivo','pctTarjeta','pctTransferencia','desgloseDiario'));
    }

    // --- Detalle
    public function getDetalleDia($fecha)
    {
        $sucursalId = Auth::user()->id_suc;
        $date = Carbon::parse($fecha)->format('Y-m-d');

        // Gastos del día
        $gastos = DB::table('Gastos')->where('id_suc', $sucursalId)->whereDate('fecha', $date)->get();
        $totalGastos = $gastos->sum('precio');

        // Ingresos del día
        $pagos = DB::table('Pago')
            ->join('Venta', 'Pago.id_venta', '=', 'Venta.id_venta')
            ->where('Venta.id_suc', $sucursalId)->where('Venta.status', '!=', 0)
            ->whereDate('Venta.fecha_hora', $date)
            ->select('Pago.id_metpago', 'Pago.monto')
            ->get();

        $efectivo = $pagos->where('id_metpago', 1)->sum('monto');
        $tarjeta = $pagos->where('id_metpago', 2)->sum('monto');
        $transferencia = $pagos->where('id_metpago', 3)->sum('monto');
        $totalIngresos = $efectivo + $tarjeta + $transferencia;

        $pctEfectivo = $totalIngresos > 0 ? round(($efectivo / $totalIngresos) * 100) : 0;
        $pctTarjeta = $totalIngresos > 0 ? round(($tarjeta / $totalIngresos) * 100) : 0;
        $pctTransferencia = $totalIngresos > 0 ? round(($transferencia / $totalIngresos) * 100) : 0;

        // Listas para las pestañas inferiores
        $ventas = DB::table('Venta')->where('id_suc', $sucursalId)->where('status', '!=', 0)->whereDate('fecha_hora', $date)->select('id_venta', 'total', 'fecha_hora')->get();
        $sucursal = DB::table('Sucursal')->where('id_suc', $sucursalId)->first();

        return response()->json([
            'ingresos' => $totalIngresos,
            'gastos' => $totalGastos,
            'balance' => $totalIngresos - $totalGastos,
            'metodos' => [
                'efectivo' => ['monto' => $efectivo, 'pct' => $pctEfectivo],
                'tarjeta' => ['monto' => $tarjeta, 'pct' => $pctTarjeta],
                'transferencia' => ['monto' => $transferencia, 'pct' => $pctTransferencia],
            ],
            'sucursal_nombre' => $sucursal ? $sucursal->nombre : 'Sucursal Principal',
            'lista_ingresos' => $ventas,
            'lista_gastos' => $gastos
        ]);
    }
}