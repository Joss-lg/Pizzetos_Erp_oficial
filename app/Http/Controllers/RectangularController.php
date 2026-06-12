<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RectangularController extends Controller
{
    /**
     * Mostrar la lista de pizzas rectangulares.
     * Tablas corregidas: 'Rectangular', 'Especialidades' y 'CategoriasProd'.
     */
    public function index()
    {
        $rectangulares = DB::table('Rectangular')
            ->join('Especialidades', 'Rectangular.id_esp', '=', 'Especialidades.id_esp')
            ->join('CategoriasProd', 'Rectangular.id_cat', '=', 'CategoriasProd.id_cat')
            ->select(
                'Rectangular.id_rec', 
                'Especialidades.nombre as especialidad', 
                'Rectangular.precio', 
                'CategoriasProd.descripcion as categoria'
            )
            ->get();

        return view('Rectangular.index', compact('rectangulares'));
    }

    /**
     * Formulario para crear una nueva pizza rectangular.
     */
    public function create()
    {
        $especialidades = DB::table('Especialidades')->get();
        $categorias = DB::table('CategoriasProd')->get();
        
        return view('Rectangular.create', compact('especialidades', 'categorias'));
    }

    /**
     * Guardar en la tabla 'Rectangular'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_esp' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('Rectangular')->insert([
            'id_esp' => $request->id_esp,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('rectangular.index')->with('success', 'Pizza Rectangular añadida correctamente.');
    }

    /**
     * Editar registro usando 'id_rec'.
     */
    public function edit($id)
    {
        $rectangular = DB::table('Rectangular')->where('id_rec', $id)->first();
        
        if (!$rectangular) {
            return redirect()->route('rectangular.index')->with('error', 'Registro no encontrado.');
        }

        $especialidades = DB::table('Especialidades')->get();
        $categorias = DB::table('CategoriasProd')->get();

        return view('Rectangular.edit', compact('rectangular', 'especialidades', 'categorias'));
    }

    /**
     * Actualizar registro en 'Rectangular'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_esp' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Rectangular')->where('id_rec', $id)->update([
            'id_esp' => $request->id_esp,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('rectangular.index')->with('success', 'Pizza Rectangular actualizada correctamente.');
    }

    /**
     * Eliminar registro de 'Rectangular'.
     */
    public function destroy($id)
    {
        DB::table('Rectangular')->where('id_rec', $id)->delete();
        return redirect()->route('rectangular.index')->with('success', 'Pizza Rectangular eliminada correctamente.');
    }
}