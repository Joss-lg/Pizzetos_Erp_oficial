<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalesController extends Controller
{
    public function index()
    {
        $sucursales = DB::table('Sucursales')->get();
        return view('recursos.sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('recursos.sucursales.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string'
        ]);

        // Eliminamos created_at y updated_at porque no existen en tu BD
        DB::table('Sucursales')->insert([
            'nombre' => $request->nombre,
            'ubicacion' => $request->ubicacion,
            'status' => 1
        ]);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal añadida correctamente.');
    }

    public function edit($id)
    {
        // Buscamos por id_suc en la tabla Sucursales
        $sucursal = DB::table('Sucursales')->where('id_suc', $id)->first();
        
        if (!$sucursal) abort(404);

        return view('recursos.sucursales.edit', compact('sucursal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string'
        ]);
        
        DB::table('Sucursales')->where('id_suc', $id)->update([
            'nombre' => $request->nombre,
            'ubicacion' => $request->ubicacion
        ]);
        
        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy($id)
    {
        DB::table('Sucursales')->where('id_suc', $id)->update(['status' => 0]);
        return redirect()->route('sucursales.index')->with('success', 'Sucursal desactivada correctamente.');
    }
}