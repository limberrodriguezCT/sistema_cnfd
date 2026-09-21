<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrera;
use App\Models\Grupo;
use App\Models\Modulo;

class AcademicoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear las Carreras
        $tete = Carrera::create([
            'nombre' => 'Técnico Especialista en Tecnología Educativa',
            'codigo' => 'TETE'
        ]);

        $tedetfp = Carrera::create([
            'nombre' => 'Técnico Especialista en Docencia de Educación Técnica y Formación Profesional',
            'codigo' => 'TEDETFP'
        ]);

        // 2. Crear los Grupos para Tecnología Educativa
        Grupo::create([
            'carrera_id' => $tete->id,
            'codigo_grupo' => 'TED-0495-02-2026',
            'modalidad' => 'Virtual',
            'meta' => 50
        ]);

        Grupo::create([
            'carrera_id' => $tete->id,
            'codigo_grupo' => 'TED-0496-02-2026',
            'modalidad' => 'Virtual',
            'meta' => 50
        ]);

        // 3. Crear los Grupos para Docencia de Educación Técnica (Extraídos del informe CNFDI)
        Grupo::create([
            'carrera_id' => $tedetfp->id,
            'codigo_grupo' => 'TED-0491-02-2026',
            'modalidad' => 'Virtual',
            'meta' => 50
        ]);

        Grupo::create([
            'carrera_id' => $tedetfp->id,
            'codigo_grupo' => 'TED-0492-02-2026',
            'modalidad' => 'Virtual',
            'meta' => 40
        ]);

        Grupo::create([
            'carrera_id' => $tedetfp->id,
            'codigo_grupo' => 'TED-0596-02-2026',
            'modalidad' => 'Presencial',
            'meta' => 25
        ]);

        Grupo::create([
            'carrera_id' => $tedetfp->id,
            'codigo_grupo' => 'TED-0493-02-2026',
            'modalidad' => 'Presencial',
            'meta' => 15
        ]);

        // 4. Módulos de Tecnología Educativa
        $modulos_tete = [
            ['nombre' => 'Diseño de recursos educativos digitales', 'semestre' => 'I Semestre 2026', 'orden' => 1],
            ['nombre' => 'Creación de recursos educativos digitales', 'semestre' => 'I Semestre 2026', 'orden' => 2],
            ['nombre' => 'Integración de tecnologías emergentes en recursos educativos', 'semestre' => 'II Semestre 2026', 'orden' => 3],
            ['nombre' => 'Integración de recursos educativos en el proceso formativo', 'semestre' => 'II Semestre 2026', 'orden' => 4],
        ];

        foreach ($modulos_tete as $mod) {
            Modulo::create([
                'carrera_id' => $tete->id,
                'nombre' => $mod['nombre'],
                'semestre' => $mod['semestre'],
                'orden' => $mod['orden']
            ]);
        }

        // 5. Módulos de Docencia de Educación Técnica (Extraídos del informe CNFDI)
        $modulos_tedetfp = [
            ['nombre' => 'Creatividad y emprendimiento', 'semestre' => 'I Semestre 2026', 'orden' => 1],
            ['nombre' => 'English A2 Waystage', 'semestre' => 'I Semestre 2026', 'orden' => 2],
            ['nombre' => 'Promoción de valores', 'semestre' => 'II Semestre 2026', 'orden' => 3],
            ['nombre' => 'Identidad historica y socicultural de Nicaragua', 'semestre' => 'II Semestre 2026', 'orden' => 4],
        ];

        foreach ($modulos_tedetfp as $mod) {
            Modulo::create([
                'carrera_id' => $tedetfp->id,
                'nombre' => $mod['nombre'],
                'semestre' => $mod['semestre'],
                'orden' => $mod['orden']
            ]);
        }
    }
}