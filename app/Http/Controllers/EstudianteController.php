<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asignacion;
use App\Models\User;
use Carbon\Carbon;

class EstudianteController extends Controller
{
    public function panel()
    {
        $estudiante = auth()->user();
        
        // Cargar las relaciones del estudiante (si tiene un grupo asignado)
        $estudiante->load('grupo.carrera');

        // Buscar todas las asignaciones (módulos) vinculadas a su grupo
        $asignaciones = [];
        if ($estudiante->grupo_id) {
            $asignaciones = Asignacion::with(['modulo', 'docente'])
                ->where('grupo_id', $estudiante->grupo_id)
                ->orderBy('fecha_inicio', 'asc')
                ->get();
        }

        return view('estudiante.panel', compact('estudiante', 'asignaciones'));
    }
}