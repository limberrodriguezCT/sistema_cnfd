<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carreras';

    protected $fillable = [
        'nombre',
        'tipo' // Agregamos el tipo para guardar si es Carrera o Curso
    ];

    public function modulos()
    {
        return $this->hasMany(Modulo::class, 'carrera_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'carrera_id');
    }
}