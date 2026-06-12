<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GastosController extends Controller
{
    public function index()
    {
        $id_suc = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES) PARA CAJA ÚNICA
        
        $cajaAbierta = DB::table('Caja')
            ->where('status', 1)
            ->where('id_suc', $id_suc)
            ->first();

        $gastos = collect();
        
        if ($cajaAbierta) {
            // Ya NO hacemos el JOIN, solo traemos los gastos normales
            // El nombre del usuario ya vendrá "escondido" dentro de la descripción
            $gastos = DB::table('Gastos')
                ->where('id_caja', $cajaAbierta->id_caja)
                ->orderBy('fecha', 'desc')
                ->get();
        }

        return view('Gastos.index', compact('cajaAbierta', 'gastos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0.1'
        ]);

        $id_suc = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES) PARA CAJA ÚNICA
        $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_suc)->first();

        if (!$cajaAbierta) {
            return back()->with('error', 'Debes abrir la caja en Flujo de Caja antes de registrar gastos.');
        }

        // --- INICIO DEL TRUCO: OBTENER NOMBRE DEL CAJERO ---
        $nombreCajero = Auth::check() ? (Auth::user()->nickName ?? 'Usuario') : 'Sistema';
        $descripcionFinal = "Registró: " . $nombreCajero . " | " . $request->descripcion;
        // --- FIN DEL TRUCO ---

        DB::table('Gastos')->insert([
            'id_suc' => $id_suc,
            'descripcion' => $descripcionFinal, // Guardamos todo en la misma columna
            'precio' => $request->precio,
            'fecha' => Carbon::now(),
            'evaluado' => 0,
            'id_caja' => $cajaAbierta->id_caja
        ]);

        return back()->with('success', 'Gasto registrado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('Gastos')->where('id_gastos', $id)->delete();
        return back()->with('success', 'Gasto eliminado.');
    }
}