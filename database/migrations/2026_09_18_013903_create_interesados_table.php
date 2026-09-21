<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interesados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo');
            $table->string('telefono');
            $table->foreignId('carrera_id')->constrained('carreras')->onDelete('cascade');
            $table->string('modalidad');
            $table->integer('anio_proyectado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interesados');
    }
};