<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlitasController extends Controller
{
    /**
     * Mostrar la lista de alitas.
     * Tablas corregidas: 'Alitas' y 'CategoriasProd'.
     */
    public function index()
    {
        $alitas = DB::table('Alitas')
            ->join('CategoriasProd', 'Alitas.id_cat', '=', 'CategoriasProd.id_cat')
            ->select('Alitas.id_alis', 'Alitas.orden', 'Alitas.precio', 'CategoriasProd.descripcion as categoria')
            ->get();

        return view('Alitas.index', compact('alitas'));
    }

    /**
     * Formulario para crear una nueva orden.
     */
    public function create()
    {
        $categorias = DB::table('CategoriasProd')->get();
        return view('Alitas.create', compact('categorias'));
    }

    /**
     * Guardar en la tabla 'Alitas'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('Alitas')->insert([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('alitas.index')->with('success', 'Alitas añadidas correctamente.');
    }

    /**
     * Editar registro usando 'id_alis'.
     */
    public function edit($id)
    {
        $alita = DB::table('Alitas')->where('id_alis', $id)->first();
        $categorias = DB::table('CategoriasProd')->get();

        if (!$alita) {
            return redirect()->route('alitas.index')->with('error', 'Registro no encontrado.');
        }

        return view('Alitas.edit', compact('alita', 'categorias'));
    }

    /**
     * Actualizar registro en 'Alitas'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Alitas')->where('id_alis', $id)->update([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('alitas.index')->with('success', 'Alitas actualizadas correctamente.');
    }

    /**
     * Eliminar registro de 'Alitas'.
     */
    public function destroy($id)
    {
        DB::table('Alitas')->where('id_alis', $id)->delete();
        return redirect()->route('alitas.index')->with('success', 'Alitas eliminadas correctamente.');
    }
}