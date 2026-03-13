<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalesController extends Controller
{
    public function index()
    {
        // Consultamos la tabla singular 'Sucursal' (Ya confirmamos que así se llama)
        $sucursales = DB::table('Sucursal')->get();
        
        // CORRECCIÓN: La carpeta física en tu proyecto es 'Sucursales' con S mayúscula
        return view('Sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('Sucursales.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20'
        ]);

        DB::table('Sucursal')->insert([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono
        ]);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal añadida correctamente.');
    }

    public function edit($id)
    {
        $sucursal = DB::table('Sucursal')->where('id_suc', $id)->first();
        if (!$sucursal) abort(404);

        return view('Sucursales.edit', compact('sucursal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20'
        ]);
        
        DB::table('Sucursal')->where('id_suc', $id)->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono
        ]);
        
        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy($id)
    {
        DB::table('Sucursal')->where('id_suc', $id)->delete();
        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada correctamente.');
    }
}