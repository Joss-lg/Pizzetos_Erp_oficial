<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MagnoController extends Controller
{
    public function index()
    {
        // Se cambiaron los nombres de las tablas a Mayúsculas para coincidir con la BD
        $magnos = DB::table('Magno')
            ->join('Especialidades', 'Magno.id_especialidad', '=', 'Especialidades.id_esp')
            ->join('Refrescos', 'Magno.id_refresco', '=', 'Refrescos.id_refresco')
            ->select(
                'Magno.id_magno', 
                'Especialidades.nombre as especialidad', 
                'Refrescos.nombre as refresco', 
                'Magno.precio'
            )
            ->get();

        return view('Magno.index', compact('magnos'));
    }

    public function create()
    {
        $especialidades = DB::table('Especialidades')->get();
        $refrescos = DB::table('Refrescos')->get();
        
        return view('Magno.create', compact('especialidades', 'refrescos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_especialidad' => 'required|integer',
            'id_refresco' => 'required|integer',
            'precio' => 'required|numeric'
        ]);

        DB::table('Magno')->insert([
            'id_especialidad' => $request->id_especialidad,
            'id_refresco' => $request->id_refresco,
            'precio' => $request->precio
        ]);

        return redirect()->route('magno.index')->with('success', 'Producto Magno añadido correctamente.');
    }

    public function edit($id)
    {
        $magno = DB::table('Magno')->where('id_magno', $id)->first();
        $especialidades = DB::table('Especialidades')->get();
        $refrescos = DB::table('Refrescos')->get();

        return view('Magno.edit', compact('magno', 'especialidades', 'refrescos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_especialidad' => 'required|integer',
            'id_refresco' => 'required|integer',
            'precio' => 'required|numeric'
        ]);
        
        DB::table('Magno')->where('id_magno', $id)->update([
            'id_especialidad' => $request->id_especialidad,
            'id_refresco' => $request->id_refresco,
            'precio' => $request->precio
        ]);
        
        return redirect()->route('magno.index')->with('success', 'Producto Magno actualizado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('Magno')->where('id_magno', $id)->delete();
        return redirect()->route('magno.index')->with('success', 'Producto Magno eliminado correctamente.');
    }
}