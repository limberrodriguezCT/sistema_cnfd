<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interesado extends Model
{
    use HasFactory;

    protected $table = 'interesados';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'carrera_id',
        'modalidad',
        'anio_proyectado'
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
}