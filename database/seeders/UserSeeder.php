<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $anio = date('y');
        $password = "Inatec{$anio}*";

        User::create([
            'name' => 'Administrador Olof Palme',
            'email' => 'admin@tecnacional.edu.ni',
            'password' => Hash::make($password),
            'rol' => 'admin',
        ]);

        User::create([
            'name' => 'Asesor Pedagógico',
            'email' => 'asesor@tecnacional.edu.ni',
            'password' => Hash::make($password),
            'rol' => 'asesor',
        ]);

        $docentes = [
            ['name' => 'Limber Josué Rodríguez Navarro', 'email' => 'limber.rodriguez@tecnacional.edu.ni'],
            ['name' => 'Alicia Marisela Umaña', 'email' => 'alicia.umana@tecnacional.edu.ni'],
            ['name' => 'Johelky Maria Montenegro', 'email' => 'johelky.montenegro@tecnacional.edu.ni'],
            ['name' => 'Yatzary Yanela Estrada', 'email' => 'yatzary.estrada@tecnacional.edu.ni'],
            ['name' => 'Glorismars Antonio Carmona Gonzalez', 'email' => 'glorismars.carmona@tecnacional.edu.ni'],
            ['name' => 'Fany Adixsa Montoya', 'email' => 'fany.montoya@tecnacional.edu.ni'],
            ['name' => 'Mariliana Videa', 'email' => 'mariliana.videa@tecnacional.edu.ni'],
        ];

        foreach ($docentes as $docente) {
            User::create([
                'name' => $docente['name'],
                'email' => $docente['email'],
                'password' => Hash::make($password),
                'rol' => 'docente',
            ]);
        }
    }
}