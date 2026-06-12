<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostillasController extends Controller
{
    /**
     * Mostrar la lista de costillas.
     * Tablas corregidas a 'Costillas' y 'CategoriasProd'.
     */
    public function index()
    {
        $costillas = DB::table('Costillas')
            ->join('CategoriasProd', 'Costillas.id_cat', '=', 'CategoriasProd.id_cat')
            ->select('Costillas.id_cos', 'Costillas.orden', 'Costillas.precio', 'CategoriasProd.descripcion as categoria')
            ->get();

        return view('Costillas.index', compact('costillas'));
    }

    /**
     * Formulario para crear una nueva orden.
     */
    public function create()
    {
        $categorias = DB::table('CategoriasProd')->get();
        return view('Costillas.create', compact('categorias'));
    }

    /**
     * Guardar en la tabla 'Costillas'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('Costillas')->insert([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('costillas.index')->with('success', 'Costillas añadidas correctamente.');
    }

    /**
     * Editar registro usando 'id_cos'.
     */
    public function edit($id)
    {
        $costilla = DB::table('Costillas')->where('id_cos', $id)->first();
        $categorias = DB::table('CategoriasProd')->get();

        if (!$costilla) {
            return redirect()->route('costillas.index')->with('error', 'Registro no encontrado.');
        }

        return view('Costillas.edit', compact('costilla', 'categorias'));
    }

    /**
     * Actualizar registro en 'Costillas'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Costillas')->where('id_cos', $id)->update([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('costillas.index')->with('success', 'Costillas actualizadas correctamente.');
    }

    /**
     * Eliminar registro de 'Costillas'.
     */
    public function destroy($id)
    {
        DB::table('Costillas')->where('id_cos', $id)->delete();
        return redirect()->route('costillas.index')->with('success', 'Costillas eliminadas correctamente.');
    }
}