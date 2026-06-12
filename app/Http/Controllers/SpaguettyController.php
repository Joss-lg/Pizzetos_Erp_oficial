<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaguettyController extends Controller
{
    /**
     * Mostrar la lista de spaguetty.
     * Tablas corregidas a 'Spaguetty' y 'CategoriasProd'.
     */
    public function index()
    {
        $spaguettis = DB::table('Spaguetty')
            ->join('CategoriasProd', 'Spaguetty.id_cat', '=', 'CategoriasProd.id_cat')
            ->select(
                'Spaguetty.id_spag', 
                'Spaguetty.orden', 
                'Spaguetty.precio', 
                'CategoriasProd.descripcion as categoria'
            )
            ->get();

        return view('Spaguetty.index', compact('spaguettis'));
    }

    /**
     * Formulario para crear una nueva orden.
     */
    public function create()
    {
        $categorias = DB::table('CategoriasProd')->get();
        return view('Spaguetty.create', compact('categorias'));
    }

    /**
     * Guardar en la tabla 'Spaguetty'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('Spaguetty')->insert([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('spaguetty.index')->with('success', 'Spaguetty añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_spag'.
     */
    public function edit($id)
    {
        $spaguetty = DB::table('Spaguetty')->where('id_spag', $id)->first();
        
        if (!$spaguetty) {
            return redirect()->route('spaguetty.index')->with('error', 'Registro no encontrado.');
        }

        $categorias = DB::table('CategoriasProd')->get();

        return view('Spaguetty.edit', compact('spaguetty', 'categorias'));
    }

    /**
     * Actualizar registro en 'Spaguetty'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Spaguetty')->where('id_spag', $id)->update([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('spaguetty.index')->with('success', 'Spaguetty actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'Spaguetty'.
     */
    public function destroy($id)
    {
        DB::table('Spaguetty')->where('id_spag', $id)->delete();
        return redirect()->route('spaguetty.index')->with('success', 'Spaguetty eliminado correctamente.');
    }
}