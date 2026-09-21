<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = ['codigo_grupo', 'modalidad', 'meta', 'carrera_id', 'anio_academico'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function estudiantes()
    {
        return $this->hasMany(User::class, 'grupo_id')->where('rol', 'estudiante');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }
}