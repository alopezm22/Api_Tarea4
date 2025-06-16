<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;

class PeliculaController extends Controller
{
    /**
     * Lista todas las películas del usuario autenticado (a través del género).
     *
     * @authenticated
     * @header Authorization Bearer {token}
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $generoId = $request->query('genero_id');

        $peliculas = Pelicula::whereHas('category', function ($query) use ($userId) {
            $query->where('user_Identificador', $userId);
        });

        if ($generoId) {
            $peliculas->where('Genero_Identificador', $generoId);
        }

        return response()->json($peliculas->get());
    }

    /**
     * Crea una nueva película para el usuario autenticado (asociada a un género).
     *
     * @authenticated
     * @header Authorization Bearer {token}
     * @bodyParam Titulo string required
     * @bodyParam Descripcion string optional
     * @bodyParam Clasificacion integer required
     * @bodyParam Imagen integer optional
     * @bodyParam Genero_Identificador integer required
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Titulo'               => 'required|string|max:255',
            'Descripcion'          => 'nullable|string',
            'Clasificacion'        => 'required|integer',
            'Imagen'               => 'nullable|integer',
            'Genero_Identificador' => 'required|integer|exists:generos,id',
        ]);

        // Validar que el género pertenezca al usuario autenticado
        $genero = $request->user()->categories()->find($validated['Genero_Identificador']);
        if (!$genero) {
            return response()->json(['message' => 'Género no válido para este usuario'], 403);
        }

        $pelicula = Pelicula::create($validated);
        return response()->json($pelicula, 201);
    }

    /**
     * Muestra una película específica del usuario autenticado.
     *
     * @authenticated
     * @header Authorization Bearer {token}
     */
    public function show(Request $request, $id)
    {
        $pelicula = Pelicula::whereHas('category', function ($query) use ($request) {
            $query->where('user_Identificador', $request->user()->id);
        })->find($id);

        if (!$pelicula) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }

        return response()->json($pelicula);
    }

    /**
     * Elimina una película del usuario autenticado.
     *
     * @authenticated
     * @header Authorization Bearer {token}
     */
    public function destroy(Request $request, $id)
    {
        $pelicula = Pelicula::whereHas('category', function ($query) use ($request) {
            $query->where('user_Identificador', $request->user()->id);
        })->find($id);

        if (!$pelicula) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }

        $pelicula->delete();

        return response()->json(['message' => 'Película eliminada']);
    }
}
