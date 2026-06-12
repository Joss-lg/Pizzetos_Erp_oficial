<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PapasController extends Controller
{
    /**
     * Mostrar la lista de papas.
     * Tablas corregidas: 'OrdenDePapas' y 'CategoriasProd'.
     */
    public function index()
    {
        $papas = DB::table('OrdenDePapas')
            ->join('CategoriasProd', 'OrdenDePapas.id_cat', '=', 'CategoriasProd.id_cat')
            ->select('OrdenDePapas.id_papa', 'OrdenDePapas.orden', 'OrdenDePapas.precio', 'CategoriasProd.descripcion as categoria')
            ->get();

        return view('Papas.index', compact('papas'));
    }

    /**
     * Formulario para crear una nueva orden de papas.
     */
    public function create()
    {
        $categorias = DB::table('CategoriasProd')->get();
        return view('Papas.create', compact('categorias'));
    }

    /**
     * Guardar en la tabla 'OrdenDePapas'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('OrdenDePapas')->insert([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('papas.index')->with('success', 'Papas añadidas correctamente.');
    }

    /**
     * Editar registro usando 'id_papa'.
     */
    public function edit($id)
    {
        $papa = DB::table('OrdenDePapas')->where('id_papa', $id)->first();
        $categorias = DB::table('CategoriasProd')->get();

        if (!$papa) {
            return redirect()->route('papas.index')->with('error', 'Registro no encontrado.');
        }

        return view('Papas.edit', compact('papa', 'categorias'));
    }

    /**
     * Actualizar registro en 'OrdenDePapas'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('OrdenDePapas')->where('id_papa', $id)->update([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('papas.index')->with('success', 'Papas actualizadas correctamente.');
    }

    /**
     * Eliminar registro de 'OrdenDePapas'.
     */
    public function destroy($id)
    {
        DB::table('OrdenDePapas')->where('id_papa', $id)->delete();
        return redirect()->route('papas.index')->with('success', 'Papas eliminadas correctamente.');
    }
}