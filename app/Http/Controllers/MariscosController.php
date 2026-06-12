<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MariscosController extends Controller
{
    /**
     * Mostrar la lista de pizzas de mariscos.
     * Tablas corregidas: 'PizzasMariscos', 'CategoriasProd' y 'TamanosPizza'.
     */
    public function index()
    {
        $mariscos = DB::table('PizzasMariscos')
            ->join('CategoriasProd', 'PizzasMariscos.id_cat', '=', 'CategoriasProd.id_cat')
            ->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')
            ->select(
                'PizzasMariscos.id_maris', 
                'PizzasMariscos.nombre', 
                'PizzasMariscos.descripcion', 
                'TamanosPizza.tamano', 
                'CategoriasProd.descripcion as categoria'
            )
            ->get();

        return view('Mariscos.index', compact('mariscos'));
    }

    /**
     * Formulario para crear una nueva pizza de mariscos.
     */
    public function create()
    {
        $categorias = DB::table('CategoriasProd')->get();
        $tamanos = DB::table('TamanosPizza')->get();
        
        return view('Mariscos.create', compact('categorias', 'tamanos'));
    }

    /**
     * Guardar en la tabla 'PizzasMariscos'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'id_tamañop' => 'required|integer',
            'id_cat' => 'required|integer'
        ]);

        DB::table('PizzasMariscos')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_tamañop' => $request->id_tamañop,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('mariscos.index')->with('success', 'Pizza de Mariscos añadida correctamente.');
    }

    /**
     * Editar registro usando 'id_maris'.
     */
    public function edit($id)
    {
        $marisco = DB::table('PizzasMariscos')->where('id_maris', $id)->first();
        
        if (!$marisco) {
            return redirect()->route('mariscos.index')->with('error', 'Registro no encontrado.');
        }

        $categorias = DB::table('CategoriasProd')->get();
        $tamanos = DB::table('TamanosPizza')->get();

        return view('Mariscos.edit', compact('marisco', 'categorias', 'tamanos'));
    }

    /**
     * Actualizar registro en 'PizzasMariscos'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'id_tamañop' => 'required|integer',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('PizzasMariscos')->where('id_maris', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_tamañop' => $request->id_tamañop,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('mariscos.index')->with('success', 'Pizza de Mariscos actualizada correctamente.');
    }

    /**
     * Eliminar registro de 'PizzasMariscos'.
     */
    public function destroy($id)
    {
        DB::table('PizzasMariscos')->where('id_maris', $id)->delete();
        return redirect()->route('mariscos.index')->with('success', 'Pizza de Mariscos eliminada correctamente.');
    }
}