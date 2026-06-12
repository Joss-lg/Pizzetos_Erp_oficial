<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriasController extends Controller
{
    /**
     * Mostrar la lista de categorías.
     * Tabla corregida: 'CategoriasProd'.
     */
    public function index()
    {
        $categorias = DB::table('CategoriasProd')->get();
        return view('Categorias.index', compact('categorias'));
    }

    /**
     * Formulario para crear una nueva categoría.
     */
    public function create()
    {
        return view('Categorias.create');
    }

    /**
     * Guardar en la tabla 'CategoriasProd'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255'
        ]);

        DB::table('CategoriasProd')->insert([
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría añadida correctamente.');
    }

    /**
     * Editar registro usando 'id_cat'.
     */
    public function edit($id)
    {
        $categoria = DB::table('CategoriasProd')->where('id_cat', $id)->first();
        
        if (!$categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        return view('Categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar registro en 'CategoriasProd'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255'
        ]);
        
        DB::table('CategoriasProd')->where('id_cat', $id)->update([
            'descripcion' => $request->descripcion
        ]);
        
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminar registro de 'CategoriasProd'.
     */
    public function destroy($id)
    {
        DB::table('CategoriasProd')->where('id_cat', $id)->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}