<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\Interesado;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        // Por defecto mostramos la proyección del próximo año, o el actual si estamos a inicios
        $anioProyectado = $request->query('anio', date('Y') + 1);

        // Traemos las carreras junto con sus módulos ordenados por semestre
        $carreras = Carrera::with(['modulos' => function($q) {
            $q->orderBy('semestre', 'asc');
        }])->get();

        return view('welcome', compact('carreras', 'anioProyectado'));
    }

    public function storeInteresado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'carrera_id' => 'required|exists:carreras,id',
            'modalidad' => 'required|string|in:Presencial,Virtual',
            'anio_proyectado' => 'required|integer'
        ]);

        Interesado::create($request->all());

        return back()->with('success', '¡Gracias por su interés! Un asesor académico se pondrá en contacto pronto.');
    }
}