<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnticiposController extends Controller
{
    public function index(Request $request)
    {
        $filtroEstado = $request->input('estado', 'pendientes');

        $statusMap = [
            'pendientes' => 1,
            'completados' => 2,
            'cancelados' => 0
        ];

        $statusDb = $statusMap[$filtroEstado] ?? 1;

        $anticipos = DB::table('pespeciales')
            ->join('venta', 'pespeciales.id_venta', '=', 'venta.id_venta')
            ->leftJoin('clientes', 'pespeciales.id_clie', '=', 'clientes.id_clie')
            ->where('pespeciales.status', $statusDb)
            ->select(
                'pespeciales.*', 
                'venta.total', 
                'clientes.nombre', 
                'clientes.apellido'
            )
            ->orderBy('pespeciales.fecha_entrega', 'asc')
            ->get();

        return view('ventas.anticipos', compact('anticipos', 'filtroEstado'));
    }
}