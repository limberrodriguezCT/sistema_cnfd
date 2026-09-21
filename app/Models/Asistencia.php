<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    // Aquí autorizamos a Laravel para guardar la observación
    protected $fillable = [
        'user_id', 
        'grupo_id', 
        'fecha', 
        'estado', 
        'observacion'
    ];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }
}