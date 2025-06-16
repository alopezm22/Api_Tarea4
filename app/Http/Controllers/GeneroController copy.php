<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genero;
use Illuminate\Support\Facades\Validator;

class GeneroController extends Controller
{
    /**
     * Mostrar todos los géneros del usuario autenticado.
     *
     * @authenticated
     * @header Authorization Bearer {token}
     */
    public function index(Request $request)
    {
        $generos = Genero::where('user_Identificador', $request->user()->id)->get();
        return response()->json($generos);
    }

    /**
     * Crear un nuevo género asociado al usuario autenticado.
     *
     * @authenticated
     * @header Authorization Bearer {token}
     * @bodyParam Titulo string required
     * @bodyParam Icono integer optional
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Titulo' => 'required|string|max:255',
            'Icono' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 422);
        }

        $genero = Genero::create([
            'Titulo' => $request->input('Titulo'),
            'Icono' => $request->input('Icono'),
            'user_Identificador' => $request->user()->id,
        ]);

        return response()->json($genero, 201);
    }

    /**
     * Eliminar un género si pertenece al usuario autenticado.
     *
     * @authenticated
     * @header Authorization Bearer {token}
     */
    public function destroy(Request $request, $id)
    {
        $genero = Genero::where('id', $id)
                        ->where('user_Identificador', $request->user()->id)
                        ->first();

        if (!$genero) {
            return response()->json(['mensaje' => 'Género no encontrado'], 404);
        }

        $genero->delete();

        return response()->json(['mensaje' => 'Género eliminado correctamente']);
    }
}
