<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PizzaController extends Controller
{
    /**
     * Mostrar la lista de pizzas con sus relaciones.
     * Se han ajustado los nombres de las tablas a: Pizzas, Especialidades, TamanosPizza y CategoriasProd.
     */
    public function index()
    {
        $pizzas = DB::table('Pizzas')
            ->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')
            ->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')
            ->join('CategoriasProd', 'Pizzas.id_cat', '=', 'CategoriasProd.id_cat')
            ->select(
                'Pizzas.id_pizza', 
                'Especialidades.nombre as especialidad', 
                'TamanosPizza.tamano as tamano', 
                'CategoriasProd.descripcion as categoria'
            )
            ->get();

        return view('pizzas.index', compact('pizzas'));
    }

    /**
     * Formulario para añadir una nueva pizza.
     */
    public function create()
    {
        $especialidades = DB::table('Especialidades')->get();
        $tamanos = DB::table('TamanosPizza')->get();
        $categorias = DB::table('CategoriasProd')->get();
        
        return view('pizzas.create', compact('especialidades', 'tamanos', 'categorias'));
    }

    /**
     * Guardar la nueva pizza en la tabla Pizzas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_esp' => 'required|integer', 
            'id_tamano' => 'required|integer', 
            'id_cat' => 'required|integer'
        ]);

        DB::table('Pizzas')->insert([
            'id_esp' => $request->id_esp, 
            'id_tamano' => $request->id_tamano, 
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('pizzas.index')->with('success', 'Pizza añadida correctamente.');
    }

    /**
     * Formulario para editar una pizza existente.
     */
    public function edit($id)
    {
        $pizza = DB::table('Pizzas')->where('id_pizza', $id)->first();
        
        if (!$pizza) {
            return redirect()->route('pizzas.index')->with('error', 'Pizza no encontrada.');
        }

        $especialidades = DB::table('Especialidades')->get();
        $tamanos = DB::table('TamanosPizza')->get();
        $categorias = DB::table('CategoriasProd')->get();

        return view('pizzas.edit', compact('pizza', 'especialidades', 'tamanos', 'categorias'));
    }

    /**
     * Actualizar los datos de la pizza.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_esp' => 'required|integer', 
            'id_tamano' => 'required|integer', 
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Pizzas')->where('id_pizza', $id)->update([
            'id_esp' => $request->id_esp,
            'id_tamano' => $request->id_tamano,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('pizzas.index')->with('success', 'Pizza actualizada correctamente.');
    }

    /**
     * Eliminar una pizza de la base de datos.
     */
    public function destroy($id)
    {
        DB::table('Pizzas')->where('id_pizza', $id)->delete();
        return redirect()->route('pizzas.index')->with('success', 'Pizza eliminada correctamente.');
    }
}