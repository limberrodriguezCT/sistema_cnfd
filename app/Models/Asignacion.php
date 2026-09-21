<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asignacion extends Model
{
    use HasFactory, SoftDeletes; // <--- Aquí habilitamos el ocultar sin eliminar

    protected $table = 'asignaciones';

    protected $fillable = [
        'grupo_id',
        'modulo_id',
        'docente_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function docente()
    {
        // El withTrashed() evita que la vista falle si el docente fue ocultado
        return $this->belongsTo(User::class, 'docente_id')->withTrashed();
    }
}