<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// ==========================================
// LANDING PAGE Y PROSPECTOS
// ==========================================
Route::get('/', [PublicController::class, 'index'])->name('welcome');
Route::post('/interesados', [PublicController::class, 'storeInteresado'])->name('interesados.store');

// ==========================================
// REDIRECCIÓN DE DASHBOARD POR ROL
// ==========================================
Route::get('/dashboard', function () {
    $rol = auth()->user()->rol;
    if ($rol === 'asesor') return redirect()->route('asesor.panel');
    if ($rol === 'docente') return redirect()->route('docente.panel');
    if ($rol === 'estudiante') return redirect()->route('estudiante.panel');
    abort(403, 'Rol no reconocido');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // RUTAS DEL ASESOR PEDAGÓGICO
    // ==========================================
    Route::prefix('asesor')->name('asesor.')->group(function () {
        Route::get('/panel', [AsesorController::class, 'panel'])->name('panel');
        Route::get('/estudiantes', [AsesorController::class, 'estudiantes'])->name('estudiantes');
        Route::get('/estudiantes/exportar/{grupo_id}', [AsesorController::class, 'exportarListado'])->name('exportar_listado');
        Route::get('/exportar-global', [AsesorController::class, 'exportarConsolidadoGlobal'])->name('exportar_global');
        
        Route::post('/estudiante', [AsesorController::class, 'storeEstudianteManual'])->name('store_estudiante');
        Route::put('/estudiante/{id}', [AsesorController::class, 'updateEstudiante'])->name('update_estudiante');
        Route::post('/estudiante/{id}/toggle', [AsesorController::class, 'toggleEstudiante'])->name('toggle_estudiante');
        Route::post('/estudiantes/importar', [AsesorController::class, 'importarEstudiantes'])->name('importar_estudiantes');
        
        Route::post('/carrera', [AsesorController::class, 'storeCarrera'])->name('store_carrera');
        Route::post('/carrera/{id}', [AsesorController::class, 'updateCarrera'])->name('update_carrera');
        
        Route::post('/modulo', [AsesorController::class, 'storeModulo'])->name('store_modulo');
        Route::post('/modulo/{id}', [AsesorController::class, 'updateModulo'])->name('update_modulo');
        Route::post('/grupo', [AsesorController::class, 'storeGrupo'])->name('store_grupo');
        
        Route::post('/docente', [AsesorController::class, 'storeDocente'])->name('store_docente');
        Route::post('/docente/{id}', [AsesorController::class, 'updateDocente'])->name('update_docente');
        Route::post('/docente/{id}/toggle', [AsesorController::class, 'toggleDocente'])->name('toggle_docente');
        
        Route::post('/asignacion', [AsesorController::class, 'storeAsignacion'])->name('store_asignacion');
        Route::post('/asignacion/{id}', [AsesorController::class, 'updateAsignacion'])->name('update_asignacion');
        Route::post('/asignacion/{id}/toggle', [AsesorController::class, 'toggleAsignacion'])->name('toggle_asignacion');
        
        Route::get('/reporte/{asignacion_id}', [AsesorController::class, 'reporteModulo'])->name('reporte');
    });

    // ==========================================
    // RUTAS DEL DOCENTE FORMADOR
    // ==========================================
    Route::prefix('docente')->name('docente.')->group(function () {
        Route::get('/panel', [DocenteController::class, 'panel'])->name('panel');
        // AQUÍ ESTÁ LA RUTA QUE FALTABA Y CAUSABA EL ERROR 500:
        Route::get('/espacio/{asignacion_id}', [DocenteController::class, 'espacio'])->name('espacio');
        
        Route::post('/procesar/{asignacion_id}', [DocenteController::class, 'procesarAvance'])->name('procesar');
        Route::post('/generar-ia/{asignacion_id}', [DocenteController::class, 'generarObservacionesIA'])->name('generar_ia');
        Route::get('/asistencia/{asignacion_id}', [DocenteController::class, 'asistencia'])->name('asistencia');
        Route::post('/asistencia/{asignacion_id}', [DocenteController::class, 'guardarAsistencia'])->name('guardar_asistencia');
        Route::get('/reporte/{asignacion_id}', [DocenteController::class, 'reporte'])->name('reporte');
        Route::get('/exportar/{asignacion_id}', [DocenteController::class, 'exportarReporte'])->name('exportar');
        Route::get('/reconocimientos/{asignacion_id}', [DocenteController::class, 'reconocimientos'])->name('reconocimientos');
    });

    // ==========================================
    // RUTAS DEL ESTUDIANTE
    // ==========================================
    Route::prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('/panel', [EstudianteController::class, 'panel'])->name('panel');
    });
});

require __DIR__.'/auth.php';