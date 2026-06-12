<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HamburguesasController extends Controller
{
    /**
     * Mostrar la lista de hamburguesas.
     * Tablas corregidas: 'Hamburguesas' y 'CategoriasProd'.
     */
    public function index()
    {
        $hamburguesas = DB::table('Hamburguesas')
            ->join('CategoriasProd', 'Hamburguesas.id_cat', '=', 'CategoriasProd.id_cat')
            ->select('Hamburguesas.id_hamb', 'Hamburguesas.paquete', 'Hamburguesas.precio', 'CategoriasProd.descripcion as categoria')
            ->get();

        return view('Hamburguesas.index', compact('hamburguesas'));
    }

    /**
     * Formulario para crear una nueva hamburguesa.
     */
    public function create()
    {
        $categorias = DB::table('CategoriasProd')->get();
        return view('Hamburguesas.create', compact('categorias'));
    }

    /**
     * Guardar en la tabla 'Hamburguesas'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paquete' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('Hamburguesas')->insert([
            'paquete' => $request->paquete,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('hamburguesas.index')->with('success', 'Hamburguesa añadida correctamente.');
    }

    /**
     * Editar registro usando 'id_hamb'.
     */
    public function edit($id)
    {
        $hamburguesa = DB::table('Hamburguesas')->where('id_hamb', $id)->first();
        $categorias = DB::table('CategoriasProd')->get();

        if (!$hamburguesa) {
            return redirect()->route('hamburguesas.index')->with('error', 'Hamburguesa no encontrada.');
        }

        return view('Hamburguesas.edit', compact('hamburguesa', 'categorias'));
    }

    /**
     * Actualizar registro en 'Hamburguesas'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'paquete' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Hamburguesas')->where('id_hamb', $id)->update([
            'paquete' => $request->paquete,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('hamburguesas.index')->with('success', 'Hamburguesa actualizada correctamente.');
    }

    /**
     * Eliminar registro de 'Hamburguesas'.
     */
    public function destroy($id)
    {
        DB::table('Hamburguesas')->where('id_hamb', $id)->delete();
        return redirect()->route('hamburguesas.index')->with('success', 'Hamburguesa eliminada correctamente.');
    }
}