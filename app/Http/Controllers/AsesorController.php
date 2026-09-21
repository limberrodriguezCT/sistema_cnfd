<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Carrera;
use App\Models\Modulo;
use App\Models\Grupo;
use App\Models\Asignacion;
use App\Models\Asistencia;
use App\Models\Interesado;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class AsesorController extends Controller
{
    public function panel(Request $request)
    {
        $fechaCorte = Carbon::now()->subDays(3)->toDateString();
        User::where('rol', 'docente')
            ->whereNotNull('contrato_fin')
            ->whereDate('contrato_fin', '<=', $fechaCorte)
            ->whereNull('deleted_at')
            ->delete();

        $anioActivo = $request->input('anio', session('anio_activo', date('Y')));
        session(['anio_activo' => $anioActivo]);
        
        $aniosDisponibles = Grupo::select('anio_academico')->distinct()->pluck('anio_academico')->toArray();
        if (!in_array(date('Y'), $aniosDisponibles)) $aniosDisponibles[] = date('Y');
        rsort($aniosDisponibles);

        $carreras = Carrera::with(['modulos', 'grupos' => function($q) use ($anioActivo) {
            $q->where('anio_academico', $anioActivo);
        }])->get();

        $modulos = Modulo::with('carrera')->get();
        $grupos = Grupo::with('carrera')->where('anio_academico', $anioActivo)->get();
        $docentes = User::where('rol', 'docente')->withTrashed()->get();
        
        $asignaciones = Asignacion::with(['grupo', 'modulo', 'docente'])
            ->whereHas('grupo', function($q) use ($anioActivo) { $q->where('anio_academico', $anioActivo); })
            ->withTrashed()->get();

        $interesados = Interesado::with('carrera')->where('anio_proyectado', $anioActivo)->orderBy('created_at', 'desc')->get();

        $labelsCarreras = []; $dataEstudiantesCarrera = []; $totalMatriculaGeneral = 0;
        foreach($carreras as $c) {
            $labelsCarreras[] = $c->nombre;
            $estudiantesEnCarrera = 0;
            foreach($c->grupos as $g) { $estudiantesEnCarrera += $g->estudiantes()->whereNull('deleted_at')->count(); }
            $dataEstudiantesCarrera[] = $estudiantesEnCarrera;
            $totalMatriculaGeneral += $estudiantesEnCarrera;
        }

        $modulosActivos = $asignaciones->where('estado', 'activo')->whereNull('deleted_at')->count();
        $modulosFinalizados = $asignaciones->where('estado', 'finalizado')->whereNull('deleted_at')->count();

        $fechasConsolidado = []; $fechasSemaforo = [];
        $fechasSemaforoGlobal = [];
        
        $archivosGlobales = glob(storage_path("app/semaforos/asig_*_*.json"));
        if ($archivosGlobales) {
            foreach ($archivosGlobales as $archivo) {
                if (preg_match('/asig_\d+_(.*)\.json/', $archivo, $matches)) {
                    $fechasSemaforoGlobal[] = $matches[1];
                }
            }
        }
        $fechasSemaforoGlobal = array_unique($fechasSemaforoGlobal);
        rsort($fechasSemaforoGlobal);

        foreach($asignaciones as $asig) {
            $fechasConsolidado[$asig->id] = Asistencia::where('grupo_id', $asig->grupo_id)->select('fecha')->distinct()->orderBy('fecha', 'desc')->pluck('fecha')->toArray();
            $fS = [];
            $archivos = glob(storage_path("app/semaforos/asig_{$asig->id}_*.json"));
            if($archivos) {
                foreach ($archivos as $archivo) { if (preg_match('/asig_\d+_(.*)\.json/', $archivo, $matches)) $fS[] = $matches[1]; }
                rsort($fS);
            }
            $fechasSemaforo[$asig->id] = $fS;
        }

        return view('asesor.panel', compact(
            'carreras', 'modulos', 'grupos', 'docentes', 'asignaciones', 'interesados',
            'fechasConsolidado', 'fechasSemaforo', 'fechasSemaforoGlobal', 'labelsCarreras', 'dataEstudiantesCarrera', 
            'totalMatriculaGeneral', 'modulosActivos', 'modulosFinalizados', 'anioActivo', 'aniosDisponibles'
        ));
    }

    public function estudiantes(Request $request) {
        $anioActivo = session('anio_activo', date('Y'));
        $grupos = Grupo::with('carrera')->where('anio_academico', $anioActivo)->get();
        $grupoActivo = session('last_grupo_id') ?? $request->grupo_id; 
        $estudiantes = collect();
        if ($grupoActivo) { $estudiantes = User::where('rol', 'estudiante')->where('grupo_id', $grupoActivo)->withTrashed()->orderBy('name', 'asc')->get(); }
        return view('asesor.estudiantes', compact('grupos', 'grupoActivo', 'estudiantes'));
    }

    public function storeEstudianteManual(Request $request) {
        $request->validate(['name' => 'required|string|max:255', 'email' => 'required|email|unique:users', 'password' => 'required|string|min:6', 'grupo_id' => 'required|exists:grupos,id', 'tipo_procedencia' => 'required|string']);
        $procedencia = "PÚBLICO GENERAL";
        if ($request->tipo_procedencia === 'INATEC') { $procedencia = "INATEC - " . ($request->inatec_departamento ?? 'SD') . " - " . ($request->inatec_centro ?? 'SC'); } 
        elseif ($request->tipo_procedencia === 'MINED') { $procedencia = "MINED - " . ($request->mined_departamento ?? 'SD') . " - " . ($request->mined_colegio ?? 'SC'); } 
        elseif ($request->tipo_procedencia === 'SETEC') { $procedencia = "SETEC - " . ($request->setec_departamento ?? 'SD') . " - " . ($request->setec_universidad ?? 'SU'); } 
        elseif ($request->tipo_procedencia === 'OTRAS INSTITUCIONES') { $procedencia = "OTRAS INSTITUCIONES - " . ($request->otra_institucion ?? 'SI'); }

        User::create(['name' => mb_convert_case($request->name, MB_CASE_TITLE, "UTF-8"), 'email' => strtolower($request->email), 'password' => Hash::make($request->password), 'rol' => 'estudiante', 'grupo_id' => $request->grupo_id, 'procedencia' => $procedencia]);
        return back()->with('success', 'Estudiante registrado con éxito.')->with('last_grupo_id', $request->grupo_id);
    }

    public function updateEstudiante(Request $request, $id) {
        $estudiante = User::withTrashed()->findOrFail($id);
        $request->validate(['name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email,' . $id, 'tipo_procedencia' => 'required|string']);
        $estudiante->name = mb_convert_case($request->name, MB_CASE_TITLE, "UTF-8");
        $estudiante->email = strtolower($request->email);
        if ($request->filled('password')) $estudiante->password = Hash::make($request->password);

        if ($request->tipo_procedencia === 'INATEC') { $estudiante->procedencia = "INATEC - " . ($request->inatec_departamento ?? 'SD') . " - " . ($request->inatec_centro ?? 'SC'); } 
        elseif ($request->tipo_procedencia === 'MINED') { $estudiante->procedencia = "MINED - " . ($request->mined_departamento ?? 'SD') . " - " . ($request->mined_colegio ?? 'SC'); } 
        elseif ($request->tipo_procedencia === 'SETEC') { $estudiante->procedencia = "SETEC - " . ($request->setec_departamento ?? 'SD') . " - " . ($request->setec_universidad ?? 'SU'); } 
        elseif ($request->tipo_procedencia === 'OTRAS INSTITUCIONES') { $estudiante->procedencia = "OTRAS INSTITUCIONES - " . ($request->otra_institucion ?? 'SI'); } 
        else { $estudiante->procedencia = "PÚBLICO GENERAL"; }

        $estudiante->save();
        return back()->with('success', 'Datos actualizados.')->with('last_grupo_id', $estudiante->grupo_id);
    }

    public function toggleEstudiante($id) {
        $estudiante = User::withTrashed()->findOrFail($id); $grupo_id = $estudiante->grupo_id;
        if ($estudiante->trashed()) { $estudiante->restore(); return back()->with('success', 'Estudiante reactivado.')->with('last_grupo_id', $grupo_id); } 
        else { $estudiante->delete(); return back()->with('success', 'Estudiante retirado.')->with('last_grupo_id', $grupo_id); }
    }

    public function importarEstudiantes(Request $request) {
        $request->validate(['archivo_excel' => 'required|mimes:xlsx,xls', 'grupo_id' => 'required|exists:grupos,id']);
        try {
            $spreadsheet = IOFactory::load($request->file('archivo_excel')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $headerRowIndex = -1; foreach ($rows as $index => $row) { if (in_array('NOMBRE DEL PARTICIPANTE', $row)) { $headerRowIndex = $index; break; } }
            if ($headerRowIndex === -1) return back()->withErrors(['error' => 'No se encontró la columna "NOMBRE DEL PARTICIPANTE".']);

            $headers = $rows[$headerRowIndex];
            $colNombre = array_search('NOMBRE DEL PARTICIPANTE', $headers); $colCorreo = array_search('CORREO ELECTRÓNICO', $headers);
            $colCedula = array_search('Cédula', $headers); $colTelefono = array_search('TELEFONO', $headers); $colLugar = array_search('LUGAR DE TRABAJO', $headers);

            $nuevos = 0; $passwordText = "Inatec" . date('y') . "*";
            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $nombre = isset($rows[$i][$colNombre]) ? trim($rows[$i][$colNombre]) : '';
                if (empty($nombre) || strtolower($nombre) === 'nombre del participante') continue;
                $email = !empty(trim($rows[$i][$colCorreo] ?? '')) ? strtolower(trim($rows[$i][$colCorreo])) : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nombre)) . '@pendiente.tecnacional.edu.ni';
                
                User::withTrashed()->updateOrCreate(['email' => $email], [
                    'name' => mb_convert_case($nombre, MB_CASE_TITLE, "UTF-8"), 'password' => Hash::make($passwordText), 'rol' => 'estudiante',
                    'cedula' => $rows[$i][$colCedula] ?? null, 'telefono' => $rows[$i][$colTelefono] ?? null, 'procedencia' => $rows[$i][$colLugar] ?? 'INATEC',
                    'grupo_id' => $request->grupo_id, 'deleted_at' => null
                ]);
                $nuevos++;
            }
            return back()->with('success', "Se importaron/actualizaron {$nuevos} estudiantes.")->with('last_grupo_id', $request->grupo_id);
        } catch (\Exception $e) { return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]); }
    }

    public function exportarListado($grupo_id) {
        $grupo = Grupo::with('carrera')->findOrFail($grupo_id);
        $estudiantes = User::where('rol', 'estudiante')->where('grupo_id', $grupo_id)->withTrashed()->orderBy('name', 'asc')->get();

        $spreadsheet = new Spreadsheet(); $sheet = $spreadsheet->getActiveSheet(); $sheet->setTitle('Listado Oficial');
        $sheet->setCellValue('D1', 'INSTITUTO NACIONAL TÉCNICO Y TECNOLÓGICO'); $sheet->setCellValue('D2', 'CENTRO TECNOLOGICO OLOF PALME, ESTELI - INATEC.'); $sheet->setCellValue('D3', 'LISTADO ESTUDIANTE/PROTAGONISTA POR GRUPO');
        $sheet->getStyle('D1:D3')->getFont()->setBold(true)->setSize(11); $sheet->getStyle('D1:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A5', 'Oferta:'); $sheet->setCellValue('B5', date('Y'));
        $sheet->setCellValue('A6', 'Sector:'); $sheet->setCellValue('B6', 'Comercio y Servicio');
        $sheet->setCellValue('A7', 'Evento:'); $sheet->setCellValue('B7', mb_strtoupper($grupo->carrera->nombre ?? 'N/A', 'UTF-8'));
        $sheet->setCellValue('A8', 'Estructura Formativa:'); $sheet->setCellValue('B8', 'Virtual / Presencial');
        $sheet->setCellValue('G5', 'Grupo:'); $sheet->setCellValue('H5', mb_strtoupper($grupo->codigo_grupo, 'UTF-8'));
        $sheet->setCellValue('G6', 'Año a cursar:'); $sheet->setCellValue('H6', '1');
        $sheet->getStyle('A5:A8')->getFont()->setBold(true); $sheet->getStyle('G5:G6')->getFont()->setBold(true);

        $headers = ['No', 'N° ÚNICO DE PERSONA', 'NOMBRE DEL PARTICIPANTE', 'Cédula', 'MAT. CASO ESPECIAL', 'ESTADO', 'TELEFONO', 'TIPO DE BECA', 'LUGAR DE TRABAJO / PROCEDENCIA', 'CORREO ELECTRÓNICO'];
        $sheet->fromArray($headers, NULL, 'A11');
        $sheet->getStyle('A11:J11')->getFont()->setBold(true); $sheet->getStyle('A11:J11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A11:J11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $rowNum = 12; $index = 1;
        foreach ($estudiantes as $est) {
            $estado = $est->trashed() ? 'Retirado' : 'Activo';
            $rowData = [$index++, strtoupper(substr(md5($est->id), 0, 8)), $est->name, $est->cedula ?? '-', '', $estado, $est->telefono ?? '-', 'BECA NACIONAL', $est->procedencia, $est->email];
            $sheet->fromArray($rowData, NULL, "A{$rowNum}");
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$rowNum}:F{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rowNum++;
        }
        foreach (range('B', 'J') as $col) { $sheet->getColumnDimension($col)->setAutoSize(true); }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Listado_Oficial_' . strtoupper($grupo->codigo_grupo) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet); $writer->save('php://output'); exit;
    }

    public function exportarConsolidadoGlobal(Request $request) {
        $anioActivo = session('anio_activo', date('Y'));
        $fechaCorte = $request->query('fecha_corte');
        $spreadsheet = new Spreadsheet(); 
        
        $sheet1 = $spreadsheet->getActiveSheet(); 
        $sheet1->setTitle('Informacion General');

        $estiloBorde = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        
        $estiloCentro = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $sheet1->mergeCells('A1:M1');
        $sheet1->setCellValue('A1', 'INFORME DE AVANCE DE LOS PROCESOS FORMATIVOS DEL CNFDI - AÑO ' . $anioActivo);
        $sheet1->mergeCells('A2:M2');
        $sheet1->setCellValue('A2', 'SEDE: CENTRO TECNOLOGICO OLOF PALME ESTELI');

        $sheet1->getStyle('A1:M2')->applyFromArray($estiloCentro);
        $sheet1->getStyle('A1:M2')->getFont()->setBold(true)->setSize(12);
        $sheet1->getStyle('A1:M2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFBDD7EE');

        $headers1 = [
            'Carrera / Grupo',
            'Docente que acompaña la formación',
            'Módulo Formativo activo',
            'Avance Programatico',
            'Modalidad',
            'Meta',
            'Matricula Actual',
            'Activos en la formación',
            'Activos sin actividades',
            'Nunca (mas de 20 dias)',
            'Observaciones o incidencias generales',
            'Retiros',
            'Reparacion'
        ];
        $sheet1->fromArray($headers1, NULL, 'A3');
        
        $sheet1->getStyle('A3:M3')->applyFromArray($estiloCentro);
        $sheet1->getStyle('A3:M3')->getFont()->setBold(true);
        $sheet1->getStyle('A3:M3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC6E0B4');
        $sheet1->getStyle('A1:M3')->applyFromArray($estiloBorde);

        $sheet1->getColumnDimension('A')->setWidth(25);
        $sheet1->getColumnDimension('B')->setWidth(30);
        $sheet1->getColumnDimension('C')->setWidth(35);
        $sheet1->getColumnDimension('D')->setWidth(18);
        $sheet1->getColumnDimension('E')->setWidth(15);
        $sheet1->getColumnDimension('F')->setWidth(10);
        $sheet1->getColumnDimension('G')->setWidth(15);
        $sheet1->getColumnDimension('H')->setWidth(18);
        $sheet1->getColumnDimension('I')->setWidth(18);
        $sheet1->getColumnDimension('J')->setWidth(18);
        $sheet1->getColumnDimension('K')->setWidth(40);
        $sheet1->getColumnDimension('L')->setWidth(10);
        $sheet1->getColumnDimension('M')->setWidth(12);

        $rowNum1 = 4;
        $carreras = Carrera::with(['grupos' => function($q) use ($anioActivo) {
            $q->where('anio_academico', $anioActivo);
        }, 'grupos.asignaciones.modulo', 'grupos.asignaciones.docente'])->get();

        foreach ($carreras as $carrera) {
            if ($carrera->grupos->isEmpty()) continue;

            $sheet1->mergeCells("A{$rowNum1}:M{$rowNum1}");
            $sheet1->setCellValue("A{$rowNum1}", mb_strtoupper($carrera->nombre, 'UTF-8'));
            $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getFont()->setBold(true);
            $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF4B084');
            $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->applyFromArray($estiloBorde);
            $sheet1->getStyle("A{$rowNum1}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $rowNum1++;

            foreach ($carrera->grupos as $grupo) {
                $totalEstudiantes = User::where('rol', 'estudiante')->where('grupo_id', $grupo->id)->withTrashed()->count();
                $retiros = User::where('rol', 'estudiante')->where('grupo_id', $grupo->id)->onlyTrashed()->count();
                $activos = $totalEstudiantes - $retiros;
                $codigoGrupoVal = mb_strtoupper($grupo->codigo_grupo, 'UTF-8');

                if ($grupo->asignaciones->count() > 0) {
                    foreach ($grupo->asignaciones as $index => $asig) {
                        
                        $obsGeneral = !empty($asig->observacion_general) ? $asig->observacion_general : '';
                        $sinActividades = 0;
                        $nunca = 0;
                        $reparacion = 0;

                        $archivos = [];
                        if ($fechaCorte) {
                            $path = storage_path("app/semaforos/asig_{$asig->id}_{$fechaCorte}.json");
                            if (file_exists($path)) {
                                $archivos = [$path];
                            }
                        } else {
                            $rutas = glob(storage_path("app/semaforos/asig_{$asig->id}_*.json"));
                            if (!empty($rutas)) {
                                rsort($rutas);
                                $archivos = [$rutas[0]];
                            }
                        }

                        if (!empty($archivos)) {
                            $jsonSemaforo = json_decode(file_get_contents($archivos[0]), true);
                            if ($jsonSemaforo && isset($jsonSemaforo['totales'])) {
                                $sinActividades = $jsonSemaforo['totales']['sin_actividades'] ?? 0;
                                $nunca = $jsonSemaforo['totales']['nunca'] ?? 0;
                                $reparacion = $jsonSemaforo['totales']['reparacion'] ?? 0;
                            }
                        }

                        $sheet1->setCellValue("A{$rowNum1}", $index === 0 ? $codigoGrupoVal : '');
                        $sheet1->setCellValue("B{$rowNum1}", mb_convert_case($asig->docente->name ?? 'Sin asignar', MB_CASE_TITLE, "UTF-8"));
                        $sheet1->setCellValue("C{$rowNum1}", $asig->modulo->nombre ?? '');
                        $sheet1->setCellValue("D{$rowNum1}", 'En progreso'); 
                        $sheet1->setCellValue("E{$rowNum1}", $index === 0 ? strtolower($grupo->modalidad) : '');
                        $sheet1->setCellValue("F{$rowNum1}", $index === 0 ? $grupo->meta : '');
                        $sheet1->setCellValue("G{$rowNum1}", $index === 0 ? $totalEstudiantes : '');
                        $sheet1->setCellValue("H{$rowNum1}", $index === 0 ? $activos : '');
                        $sheet1->setCellValue("I{$rowNum1}", $sinActividades > 0 ? $sinActividades : ''); 
                        $sheet1->setCellValue("J{$rowNum1}", $nunca > 0 ? $nunca : ''); 
                        $sheet1->setCellValue("K{$rowNum1}", $obsGeneral); 
                        $sheet1->setCellValue("L{$rowNum1}", $index === 0 ? $retiros : ''); 
                        $sheet1->setCellValue("M{$rowNum1}", $reparacion > 0 ? $reparacion : ''); 

                        $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->applyFromArray($estiloBorde);
                        $sheet1->getStyle("A{$rowNum1}:J{$rowNum1}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet1->getStyle("L{$rowNum1}:M{$rowNum1}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet1->getStyle("K{$rowNum1}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $rowNum1++;
                    }
                } else {
                    $sheet1->setCellValue("A{$rowNum1}", $codigoGrupoVal);
                    $sheet1->setCellValue("B{$rowNum1}", 'Sin asignaciones');
                    $sheet1->setCellValue("E{$rowNum1}", strtolower($grupo->modalidad));
                    $sheet1->setCellValue("F{$rowNum1}", $grupo->meta);
                    $sheet1->setCellValue("G{$rowNum1}", $totalEstudiantes);
                    $sheet1->setCellValue("H{$rowNum1}", $activos);
                    $sheet1->setCellValue("L{$rowNum1}", $retiros);
                    $sheet1->setCellValue("M{$rowNum1}", 0);

                    $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->applyFromArray($estiloBorde);
                    $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $rowNum1++;
                }
            }
        }

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Nombre de Inactivos por carrera');

        $headers2 = [
            'Carrera',
            'Código de Grupo',
            'Nombre del Estudiante',
            'Institucion de Procedencia (INATEC, MINED, UNIVERSIDAD O PUBLICO GENERAL)',
            'Acciones de segumiento individual'
        ];
        $sheet2->fromArray($headers2, NULL, 'A1');

        $estiloEncabezadoS2 = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFBDD7EE']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet2->getStyle('A1:E1')->applyFromArray($estiloEncabezadoS2);
        $sheet2->getRowDimension(1)->setRowHeight(40);

        $sheet2->getColumnDimension('A')->setWidth(40);
        $sheet2->getColumnDimension('B')->setWidth(25);
        $sheet2->getColumnDimension('C')->setWidth(45);
        $sheet2->getColumnDimension('D')->setWidth(50);
        $sheet2->getColumnDimension('E')->setWidth(75);

        $rowS2 = 2;
        foreach ($carreras as $carrera) {
            foreach ($carrera->grupos as $grupo) {
                
                $inactivosIdentifiers = [];
                foreach ($grupo->asignaciones as $asig) {
                    if ($asig->estado == 'activo') {
                        $json = null;
                        if ($fechaCorte) {
                            $path = storage_path("app/semaforos/asig_{$asig->id}_{$fechaCorte}.json");
                            if (file_exists($path)) {
                                $json = json_decode(file_get_contents($path), true);
                            }
                        } else {
                            $rutas = glob(storage_path("app/semaforos/asig_{$asig->id}_*.json"));
                            if (!empty($rutas)) {
                                rsort($rutas);
                                $json = json_decode(file_get_contents($rutas[0]), true);
                            }
                        }

                        if ($json && isset($json['estudiantes']) && is_array($json['estudiantes'])) {
                            foreach ($json['estudiantes'] as $e) {
                                $color = strtolower($e['color'] ?? '');
                                if (in_array($color, ['amarillo', 'rojo', 'yellow', 'red'])) {
                                    if (!empty($e['correo'])) $inactivosIdentifiers[] = strtolower(trim($e['correo']));
                                    if (!empty($e['email'])) $inactivosIdentifiers[] = strtolower(trim($e['email']));
                                    if (!empty($e['nombre'])) $inactivosIdentifiers[] = mb_strtolower(trim($e['nombre']), 'UTF-8');
                                }
                            }
                        }
                    }
                }

                $estudiantes = User::where('rol', 'estudiante')
                    ->where('grupo_id', $grupo->id)
                    ->withTrashed()
                    ->orderBy('name', 'asc')
                    ->get();
                
                foreach ($estudiantes as $est) {
                    
                    $isInactive = false;
                    $identificadoresEstudiante = [
                        strtolower(trim($est->email)),
                        mb_strtolower(trim($est->name), 'UTF-8')
                    ];
                    
                    foreach($identificadoresEstudiante as $idnt) {
                        if(in_array($idnt, $inactivosIdentifiers)) {
                            $isInactive = true;
                            break;
                        }
                    }

                    $ultimaAsistencia = Asistencia::where('user_id', $est->id)
                        ->where('grupo_id', $grupo->id)
                        ->whereNotNull('observacion')
                        ->where('observacion', '!=', '')
                        ->orderBy('fecha', 'desc')
                        ->first();
                        
                    if ($ultimaAsistencia) {
                        $isInactive = true; 
                    }

                    if (!$isInactive && !$est->trashed()) {
                        continue; 
                    }

                    $accionSeguimiento = $ultimaAsistencia ? $ultimaAsistencia->observacion : 'Pendiente de registrar seguimiento por el docente.';
                    if ($est->trashed()) {
                        $accionSeguimiento = 'Retirado del proceso formativo. ' . $accionSeguimiento;
                    }

                    $sheet2->setCellValue("A{$rowS2}", mb_convert_case($carrera->nombre, MB_CASE_TITLE, "UTF-8"));
                    $sheet2->setCellValue("B{$rowS2}", mb_strtoupper($grupo->codigo_grupo, 'UTF-8'));
                    $sheet2->setCellValue("C{$rowS2}", mb_strtoupper($est->name, 'UTF-8'));
                    $sheet2->setCellValue("D{$rowS2}", mb_strtoupper($est->procedencia, 'UTF-8'));
                    $sheet2->setCellValue("E{$rowS2}", $accionSeguimiento); 
                    
                    $sheet2->getStyle("A{$rowS2}:E{$rowS2}")->applyFromArray($estiloBorde);
                    $sheet2->getStyle("A{$rowS2}:D{$rowS2}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet2->getStyle("E{$rowS2}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
                    $rowS2++;
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Informe_Avance_CNFDI_'.$anioActivo.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet); 
        $writer->save('php://output'); 
        exit;
    }

    public function storeCarrera(Request $request) { 
        $request->validate(['nombre' => 'required|string|max:255', 'tipo' => 'required|string|in:Carrera,Curso']); 
        Carrera::create(['nombre' => $request->nombre, 'tipo' => $request->tipo]); 
        return back()->with('success', 'Oferta Formativa Guardada'); 
    }

    public function updateCarrera(Request $request, $id) { 
        $request->validate(['nombre' => 'required|string|max:255', 'tipo' => 'required|string|in:Carrera,Curso']); 
        Carrera::findOrFail($id)->update(['nombre' => $request->nombre, 'tipo' => $request->tipo]); 
        return back()->with('success', 'Oferta Formativa Actualizada'); 
    }
    
    public function storeModulo(Request $request) { 
        $request->validate([
            'nombre' => 'required|string|max:255',
            'semestre' => 'required|string|in:I Semestre,II Semestre',
            'carrera_id' => 'required|exists:carreras,id',
            'tipo_modulo' => 'required|string|in:Técnicos,Transversales,Optativos'
        ]);
        Modulo::create($request->all()); 
        return back()->with('success', 'Módulo Creado'); 
    }

    public function updateModulo(Request $request, $id) { 
        $request->validate([
            'nombre' => 'required|string|max:255',
            'semestre' => 'required|string|in:I Semestre,II Semestre',
            'carrera_id' => 'required|exists:carreras,id',
            'tipo_modulo' => 'required|string|in:Técnicos,Transversales,Optativos'
        ]);
        Modulo::findOrFail($id)->update($request->all()); 
        return back()->with('success', 'Módulo Actualizado'); 
    }
    
    public function storeGrupo(Request $request) { 
        $request->validate([
            'codigo_grupo' => 'required|string|max:255|unique:grupos', 
            'modalidad' => 'required|string|max:255', 
            'meta' => 'required|integer|min:1', 
            'carrera_id' => 'required|exists:carreras,id',
            'anio_academico' => 'required|integer'
        ]);

        $datos = $request->all();
        $datos['codigo_grupo'] = mb_strtoupper($request->codigo_grupo, 'UTF-8');

        Grupo::create($datos); 
        return back()->with('success', 'Grupo creado exitosamente.'); 
    }
    
    public function storeDocente(Request $request) { 
        $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email', 'password' => 'required|string|min:6',
            'tipo_contrato' => 'required|string', 'contrato_inicio' => 'required|date', 'contrato_fin' => 'required|date|after_or_equal:contrato_inicio'
        ]);

        $docenteExistente = User::where('email', strtolower($request->email))->withTrashed()->first();
        if ($docenteExistente) {
            if ($docenteExistente->trashed()) {
                return back()->with('alerta_recuperacion_id', $docenteExistente->id)->with('alerta_recuperacion_nombre', $docenteExistente->name);
            } else { return back()->withErrors(['email' => 'El correo ya está en uso por un docente activo.']); }
        }

        User::create([
            'name' => mb_convert_case($request->name, MB_CASE_TITLE, "UTF-8"), 'email' => strtolower($request->email), 
            'password' => Hash::make($request->password), 'rol' => 'docente', 'tipo_contrato' => $request->tipo_contrato,
            'contrato_inicio' => $request->contrato_inicio, 'contrato_fin' => $request->contrato_fin
        ]); 
        return back()->with('success', 'Docente Formador Registrado'); 
    }
    
    public function updateDocente(Request $request, $id) { 
        $docente = User::withTrashed()->findOrFail($id); 
        $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email,' . $id,
            'tipo_contrato' => 'required|string', 'contrato_inicio' => 'required|date', 'contrato_fin' => 'required|date|after_or_equal:contrato_inicio'
        ]);
        $docente->name = mb_convert_case($request->name, MB_CASE_TITLE, "UTF-8"); 
        $docente->email = strtolower($request->email); 
        $docente->tipo_contrato = $request->tipo_contrato;
        $docente->contrato_inicio = $request->contrato_inicio;
        $docente->contrato_fin = $request->contrato_fin;
        if ($request->filled('password')) $docente->password = Hash::make($request->password); 
        $docente->save(); 
        return back()->with('success', 'Datos y contrato del docente actualizados'); 
    }

    public function toggleDocente($id) {
        $docente = User::withTrashed()->findOrFail($id);
        if ($docente->trashed()) { $docente->restore(); return back()->with('success', 'Docente reactivado exitosamente. Por favor actualice sus fechas de contrato.'); } 
        else { $docente->delete(); return back()->with('success', 'Docente ocultado del panel.'); }
    }

    public function storeAsignacion(Request $request) { Asignacion::create($request->all()); return back()->with('success', 'Asignación Creada'); }
    public function updateAsignacion(Request $request, $id) { Asignacion::withTrashed()->findOrFail($id)->update(['fecha_inicio' => $request->fecha_inicio, 'fecha_fin' => $request->fecha_fin, 'estado' => $request->estado]); return back()->with('success', 'Asignación Actualizada'); }
    public function toggleAsignacion($id) {
        $asignacion = Asignacion::withTrashed()->findOrFail($id);
        if ($asignacion->trashed()) { $asignacion->restore(); return back()->with('success', 'Asignación reactivada.'); } 
        else { $asignacion->delete(); return back()->with('success', 'Asignación ocultada.'); }
    }

    public function reporteModulo(Request $request, $asignacion_id) { 
        $asignacion = Asignacion::with(['grupo.estudiantes' => function($q) { $q->withTrashed()->orderBy('name', 'asc'); }, 'modulo', 'docente'])->findOrFail($asignacion_id); 
        $fechasSemaforo = []; $archivos = glob(storage_path("app/semaforos/asig_{$asignacion_id}_*.json")); 
        if($archivos) { foreach ($archivos as $archivo) { if (preg_match('/asig_\d+_(.*)\.json/', $archivo, $matches)) $fechasSemaforo[] = $matches[1]; } rsort($fechasSemaforo); } 
        $fechaCorte = $request->query('fecha_corte', count($fechasSemaforo) > 0 ? $fechasSemaforo[0] : null); 
        $semaforoData = null; if ($fechaCorte && file_exists(storage_path("app/semaforos/asig_{$asignacion_id}_{$fechaCorte}.json"))) { $semaforoData = json_decode(file_get_contents(storage_path("app/semaforos/asig_{$asignacion_id}_{$fechaCorte}.json")), true); } 
        $fechasConsolidado = Asistencia::where('grupo_id', $asignacion->grupo_id)->select('fecha')->distinct()->orderBy('fecha', 'desc')->pluck('fecha')->toArray(); 
        return view('asesor.reporte', compact('asignacion', 'fechasSemaforo', 'fechaCorte', 'semaforoData', 'fechasConsolidado')); 
    }
}