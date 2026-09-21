<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asistencia;
use App\Models\Asignacion;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocenteController extends Controller
{
    public function panel()
    {
        $asignaciones = Asignacion::with(['grupo.carrera', 'modulo'])
            ->where('docente_id', auth()->id())
            ->orderBy('fecha_inicio', 'desc')
            ->get();
            
        $asignacionesActivas = $asignaciones->where('estado', 'activo');
        $asignacionesCerradas = $asignaciones->where('estado', 'finalizado');

        return view('docente.panel', compact('asignacionesActivas', 'asignacionesCerradas'));
    }

    public function espacio($asignacion_id)
    {
        $asignacion = Asignacion::with(['grupo.carrera', 'modulo'])->findOrFail($asignacion_id);
        if ($asignacion->docente_id !== auth()->id()) abort(403, 'Acceso denegado.');

        $fechasConsolidado = Asistencia::where('grupo_id', $asignacion->grupo_id)
            ->select('fecha')->distinct()->orderBy('fecha', 'desc')->pluck('fecha')->toArray();
        
        $fechasSemaforo = [];
        $archivos = glob(storage_path("app/semaforos/asig_{$asignacion_id}_*.json"));
        if($archivos) {
            foreach ($archivos as $archivo) {
                if (preg_match('/asig_\d+_(.*)\.json/', $archivo, $matches)) { $fechasSemaforo[] = $matches[1]; }
            }
            rsort($fechasSemaforo);
        }

        return view('docente.espacio', compact('asignacion', 'fechasConsolidado', 'fechasSemaforo'));
    }

    public function procesarAvance(Request $request, $asignacion_id)
    {
        if (!$request->hasFile('archivo_excel')) return back()->withErrors(['error' => 'Seleccione un archivo.']);
        $asignacion = Asignacion::with(['grupo', 'modulo'])->findOrFail($asignacion_id);
        $grupoId = $asignacion->grupo_id;

        try {
            $file = $request->file('archivo_excel');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['xls', 'xlsx', 'csv'])) return back()->withErrors(['error' => 'Debe ser Excel o CSV.']);

            $path = $file->getRealPath();
            $spreadsheet = ($ext === 'csv') ? IOFactory::createReader('Csv')->setReadDataOnly(true)->load($path) : IOFactory::load($path);
            
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $headerRowIndex = -1; $tipoFormato = '';

            foreach ($rows as $index => $row) {
                $rowStr = strtolower(implode(' ', array_map('trim', $row)));
                if (str_contains($rowStr, 'nombre del participante')) { $headerRowIndex = $index; $tipoFormato = 'inatec'; break; } 
                elseif (str_contains($rowStr, 'correo') and str_contains($rowStr, 'nombre')) { $headerRowIndex = $index; $tipoFormato = 'moodle'; break; }
            }

            if ($headerRowIndex === -1) return back()->withErrors(['error' => 'Formato de Campus no válido.']);

            $headers = $rows[$headerRowIndex];
            $colNombre = -1; $colApellido = -1; $colCorreo = -1; $colTotal = -1;

            foreach ($headers as $idx => $h) {
                $hStr = strtolower(trim(preg_replace('/\xEF\xBB\xBF/', '', (string)$h)));
                if ($tipoFormato == 'moodle') {
                    if ($hStr === 'nombre') $colNombre = $idx;
                    if ($hStr === 'apellido(s)') $colApellido = $idx;
                } else {
                    if (str_contains($hStr, 'nombre del participante')) $colNombre = $idx;
                }
                if (str_contains($hStr, 'correo')) $colCorreo = $idx;
                if (str_contains($hStr, 'total del curso') or str_contains($hStr, 'avance')) $colTotal = $idx;
            }

            $actividadesCols = [];
            $ignorarMoodle = ['descargar', 'ciudad', 'departamento', 'institución', 'institucion', 'país', 'pais'];
            
            for ($i = $colCorreo + 1; $i < count($headers); $i++) {
                if ($colTotal !== -1 and $i >= $colTotal) break;
                $hStr = trim($headers[$i]);
                $hStrLower = strtolower($hStr);
                
                if (!empty($hStr)) {
                    $ignorar = false;
                    foreach ($ignorarMoodle as $ign) {
                        if (str_contains($hStrLower, $ign)) {
                            $ignorar = true; break;
                        }
                    }
                    if (!$ignorar) {
                        $actividadesCols[$i] = $hStr;
                    }
                }
            }

            $semaforoData = ['actividades' => array_values($actividadesCols), 'estudiantes' => []];
            $procesados = 0;

            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if ($tipoFormato == 'moodle') {
                    $nombreCompleto = trim(($row[$colNombre] ?? '') . ' ' . ($row[$colApellido] ?? ''));
                } else {
                    $nombreCompleto = trim($row[$colNombre] ?? '');
                }
                if (empty($nombreCompleto) or strtolower($nombreCompleto) === 'nombre del participante') continue;

                $email = !empty(trim($row[$colCorreo] ?? '')) ? strtolower(trim($row[$colCorreo])) : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nombreCompleto)) . '@pendiente.tecnacional.edu.ni';
                
                $estudiante = User::where('grupo_id', $grupoId)->where(function($q) use ($email, $nombreCompleto) {
                    $q->where('email', $email)->orWhere('name', 'LIKE', '%' . $nombreCompleto . '%');
                })->first();

                if ($estudiante) {
                    $avancesEstudiante = [];
                    $completadasCount = 0;

                    foreach ($actividadesCols as $colIdx => $actName) {
                        $valRaw = isset($row[$colIdx]) ? trim($row[$colIdx]) : '';
                        $valStr = strtolower($valRaw);

                        if ($valRaw === '' or $valRaw === '-' or $valStr === 'no realizado') {
                            $estadoAct = 'No Realizado';
                        } elseif ($valStr === 'completado' or $valStr === 'finalizado') {
                            $estadoAct = 'Completado';
                            $completadasCount++;
                        } else {
                            $valNum = floatval(str_replace(['%', ' ', ','], ['', '', '.'], $valRaw));
                            if ($valNum >= 60) {
                                $estadoAct = 'Completado';
                                $completadasCount++;
                            } elseif ($valNum > 0) {
                                $estadoAct = 'Revisar Retroalimentación';
                            } else {
                                $estadoAct = 'No Realizado';
                            }
                        }
                        $avancesEstudiante[$actName] = $estadoAct;
                    }

                    $semaforoData['estudiantes'][$estudiante->id] = $avancesEstudiante;

                    $estadoAsistencia = ($completadasCount > 0) ? 'Presente' : 'Ausente';
                    $observacionDefault = ($completadasCount > 0) ? 'Activo en actividades.' : 'Sin actividad registrada en plataforma.';

                    Asistencia::updateOrCreate(
                        ['user_id' => $estudiante->id, 'grupo_id' => $grupoId, 'fecha' => date('Y-m-d')],
                        ['estado' => $estadoAsistencia, 'observacion' => $observacionDefault]
                    );
                    $procesados++;
                }
            }

            if ($procesados === 0) return back()->withErrors(['error' => 'No hubo coincidencia con los correos del grupo.']);
            
            $dir = storage_path('app/semaforos');
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            $fechaHoy = date('Y-m-d');
            file_put_contents($dir . "/asig_{$asignacion_id}_{$fechaHoy}.json", json_encode($semaforoData, JSON_UNESCAPED_UNICODE));

            return back()->with('success', "Campus procesado exitosamente. Semáforo generado para {$procesados} protagonistas.");
        } catch (\Exception $e) { return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]); }
    }

    public function generarObservacionesIA($asignacion_id)
    {
        $asignacion = Asignacion::with(['grupo.estudiantes', 'modulo'])->findOrFail($asignacion_id);
        $fechaInicio = \Carbon\Carbon::parse($asignacion->fecha_inicio);
        $fechaFin = \Carbon\Carbon::parse($asignacion->fecha_fin);
        
        $duracionTotal = $fechaInicio->diffInDays($fechaFin);
        $diasTranscurridos = $fechaInicio->diffInDays(now());
        $porcentajeAvance = ($duracionTotal > 0 and $diasTranscurridos > 0) ? min(100, round(($diasTranscurridos / $duracionTotal) * 100)) : 0;
        
        $totalMatricula = $asignacion->grupo->estudiantes->count();
        $activos = 0; $inactivos = 0; $estudiantesInactivos = [];
        
        foreach($asignacion->grupo->estudiantes as $estudiante) {
            if ($estudiante->trashed()) continue;
            $ultima = Asistencia::where('user_id', $estudiante->id)->where('grupo_id', $asignacion->grupo_id)->latest('fecha')->first();
                
            if($ultima and $ultima->estado == 'Ausente') {
                $inactivos++;
                $estudiantesInactivos[] = [
                    'id' => $estudiante->id, 
                    'nombre' => $estudiante->name, 
                    'nota_bruta' => $ultima->observacion ?? 'No ha ingresado.'
                ];
            } else { $activos++; }
        }

        $apunteGeneral = $asignacion->observacion_general ?? 'Sin apuntes.';
        $datosJson = json_encode($estudiantesInactivos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $prompt = "Actúa como docente formador del INATEC redactando un informe formal y objetivo para el Asesor Pedagógico. " .
                  "Datos del grupo: Avance global: {$porcentajeAvance}%. Días de clases transcurridos: {$diasTranscurridos}. Matrícula total: {$totalMatricula}. " .
                  "Activos en formación: {$activos}. Activos sin actividades (Inactivos): {$inactivos}. " .
                  "Apuntes crudos de los inactivos: {$datosJson}. Apunte general crudo actual: '{$apunteGeneral}'. " .
                  "Instrucciones de redacción: " .
                  "1. La 'general' debe ser un solo párrafo formal reportando el estado del grupo (retención, avance, pausas, etc.). No uses saludos como 'Estimados protagonistas', redacta como informe institucional. " .
                  "2. Las 'individuales' deben reportar en tercera persona la situación del estudiante (ej. 'No ha ingresado. Se contactó vía WhatsApp...'). " .
                  "Devuelve ÚNICAMENTE un JSON válido con esta estructura exacta: {\"individuales\": {\"Nombre del estudiante\": \"Redacción formal\"}, \"general\": \"Redacción general del informe\"}. No incluyas etiquetas markdown.";

        $apiKey = trim(env('GEMINI_API_KEY'));
        
        if (empty($apiKey)) {
            return back()->withErrors(['error' => 'La clave de API no está configurada en el archivo .env.']);
        }

        try {
            $modelo = 'gemini-flash-latest';
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent";

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey
                ])
                ->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]], 
                'generationConfig' => [
                    'temperature' => 0.2
                ]
            ]);

            if($response->successful()) {
                $responseText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $responseText = str_replace(['```json', '```'], '', $responseText);
                
                $inicio = strpos($responseText, '{');
                $fin = strrpos($responseText, '}');
                
                if ($inicio !== false and $fin !== false) {
                    $responseText = substr($responseText, $inicio, $fin - $inicio + 1);
                }

                $respuestaIA = json_decode(trim($responseText), true);
                
                if(json_last_error() === JSON_ERROR_NONE and isset($respuestaIA['general'])) {
                    $asignacion->observacion_general = $respuestaIA['general'];
                    $asignacion->save();
                    
                    if(isset($respuestaIA['individuales']) and is_array($respuestaIA['individuales'])) {
                        foreach($estudiantesInactivos as $inactivo) {
                            $nombreBusqueda = mb_strtolower(trim($inactivo['nombre']), 'UTF-8');
                            $observacionGuardar = null;

                            foreach($respuestaIA['individuales'] as $keyNombre => $obs) {
                                if (mb_strtolower(trim($keyNombre), 'UTF-8') === $nombreBusqueda) {
                                    $observacionGuardar = $obs;
                                    break;
                                }
                            }

                            if($observacionGuardar) {
                                $asis = Asistencia::where('user_id', $inactivo['id'])
                                    ->where('grupo_id', $asignacion->grupo_id)
                                    ->latest('fecha')->first();
                                if($asis) { 
                                    $asis->observacion = $observacionGuardar; 
                                    $asis->save(); 
                                }
                            }
                        }
                    }
                    return back()->with('success', 'El reporte al asesor ha sido redactado por la IA exitosamente.');
                }
                return back()->withErrors(['error' => 'La IA devolvió un formato no legible. Intente nuevamente.']);
            }
            
            $errorMensaje = $response->json('error.message') ?? $response->body();
            return back()->withErrors(['error' => 'Error de API de Gemini: ' . $errorMensaje]);

        } catch (\Exception $e) { 
            return back()->withErrors(['error' => 'Excepción en la IA: ' . $e->getMessage()]); 
        }
    }

    public function asistencia($asignacion_id)
    {
        $asignacion = Asignacion::with(['grupo.estudiantes', 'modulo'])->findOrFail($asignacion_id);
        $estudiantes = $asignacion->grupo->estudiantes;
        return view('docente.asistencia', compact('asignacion', 'estudiantes'));
    }

    public function guardarAsistencia(Request $request, $asignacion_id)
    {
        $asignacion = Asignacion::findOrFail($asignacion_id);
        $request->validate(['fecha' => 'required|date', 'asistencias' => 'required|array']);

        if ($request->filled('observacion_general_cruda')) {
            $asignacion->observacion_general = $request->observacion_general_cruda;
            $asignacion->save();
        }

        foreach ($request->asistencias as $userId => $data) {
            Asistencia::updateOrCreate(
                ['user_id' => $userId, 'grupo_id' => $asignacion->grupo_id, 'fecha' => $request->fecha],
                ['estado' => $data['estado'], 'observacion' => $data['observacion'] ?? null]
            );
        }
        return back()->with('success', 'Asistencia manual guardada.');
    }

    public function exportarReporte(Request $request, $asignacion_id)
    {
        $tipo = $request->query('tipo', 'consolidado');
        $fechaCorte = $request->query('fecha_corte', date('Y-m-d'));
        
        $asignacion = Asignacion::with(['grupo.carrera', 'grupo.estudiantes' => function($q) { $q->withTrashed()->orderBy('name', 'asc'); }, 'modulo', 'docente'])->findOrFail($asignacion_id);
        $spreadsheet = new Spreadsheet();

        if ($tipo === 'semaforo') {
            $jsonPath = storage_path("app/semaforos/asig_{$asignacion_id}_{$fechaCorte}.json");
            if (!file_exists($jsonPath)) {
                return back()->withErrors(['error' => 'No hay un reporte de campus procesado en la fecha seleccionada.']);
            }
            
            $semaforoData = json_decode(file_get_contents($jsonPath), true);
            
            // FILTRO RETROACTIVO: Limpia columnas basura de reportes viejos para el Excel
            if(isset($semaforoData['actividades'])) {
                $actividadesLimpias = [];
                $ignorar = ['ciudad', 'departamento', 'institución', 'institucion', 'país', 'pais', 'descargar'];
                foreach($semaforoData['actividades'] as $act) {
                    $actLower = strtolower($act);
                    $saltar = false;
                    foreach($ignorar as $ign) {
                        if(str_contains($actLower, $ign)) { $saltar = true; break; }
                    }
                    if(!$saltar) { $actividadesLimpias[] = $act; }
                }
                $semaforoData['actividades'] = array_values($actividadesLimpias);
            }

            $actividades = $semaforoData['actividades'] ?? [];
            $datosEstudiantes = $semaforoData['estudiantes'] ?? [];

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Semaforo de Rendimiento');

            $totalCols = count($actividades) + 2;
            $ultimoColumnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
            $penultimoColumnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols - 1);

            $sheet->mergeCells("A1:{$ultimoColumnaIndex}1");
            $sheet->setCellValue('A1', 'Avances de Módulos ' . mb_strtoupper($asignacion->grupo->carrera->nombre ?? 'Tecnología Educativa', 'UTF-8'));
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A1:{$ultimoColumnaIndex}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2CC');

            $sheet->mergeCells("A2:{$ultimoColumnaIndex}2");
            $sheet->setCellValue('A2', 'Módulo: ' . mb_strtoupper($asignacion->modulo->nombre, 'UTF-8'));
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A2:{$ultimoColumnaIndex}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2CC');

            $sheet->setCellValue('A3', 'N°');
            $sheet->setCellValue('B3', 'Nombre Completo');
            
            $colIdx = 3;
            foreach ($actividades as $act) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$colStr}3", $act);
                $colIdx++;
            }
            $sheet->setCellValue("{$ultimoColumnaIndex}3", "Total de\navance del\nMódulo");

            $sheet->getStyle("A3:{$ultimoColumnaIndex}3")->getFont()->setBold(true);
            $sheet->getStyle("A3:{$ultimoColumnaIndex}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A3:{$ultimoColumnaIndex}3")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A3:{$ultimoColumnaIndex}3")->getAlignment()->setWrapText(true);
            
            $sheet->getStyle("A3:{$penultimoColumnaIndex}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2CC');
            $sheet->getStyle("{$ultimoColumnaIndex}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00B0F0');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(40);
            for ($c = 3; $c <= $totalCols; $c++) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colStr)->setWidth(16);
            }

            $rowNum = 4; 
            $index = 1;
            $totalActividades = count($actividades);
            $sumaPorcentajes = 0;
            $estudiantesContados = 0;

            foreach ($asignacion->grupo->estudiantes as $est) {
                if ($est->trashed()) continue;
                
                $sheet->setCellValue("A{$rowNum}", $index++);
                $sheet->setCellValue("B{$rowNum}", mb_strtoupper($est->name, 'UTF-8'));
                
                $completadasCount = 0;
                $datosEst = $datosEstudiantes[$est->id] ?? [];

                $colIdx = 3;
                foreach ($actividades as $act) {
                    $estado = $datosEst[$act] ?? 'No Realizado';
                    $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                    
                    if ($estado === 'Completado') {
                        $sheet->setCellValue("{$colStr}{$rowNum}", 'A');
                        $sheet->getStyle("{$colStr}{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4'); 
                        $sheet->getStyle("{$colStr}{$rowNum}")->getFont()->getColor()->setARGB('006100'); 
                        $completadasCount++;
                    } else {
                        $sheet->setCellValue("{$colStr}{$rowNum}", 'SE');
                        $sheet->getStyle("{$colStr}{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE699'); 
                        $sheet->getStyle("{$colStr}{$rowNum}")->getFont()->getColor()->setARGB('9C5700'); 
                    }
                    $sheet->getStyle("{$colStr}{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $colIdx++;
                }

                $porcentaje = $totalActividades > 0 ? ($completadasCount / $totalActividades) * 100 : 0;
                $sumaPorcentajes += $porcentaje;
                $estudiantesContados++;
                
                $sheet->setCellValue("{$ultimoColumnaIndex}{$rowNum}", number_format($porcentaje, 2) . '%');
                $sheet->getStyle("{$ultimoColumnaIndex}{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                if ($porcentaje == 100) {
                    $sheet->getStyle("{$ultimoColumnaIndex}{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00B050'); 
                    $sheet->getStyle("{$ultimoColumnaIndex}{$rowNum}")->getFont()->getColor()->setARGB('FFFFFF');
                } else {
                    $sheet->getStyle("{$ultimoColumnaIndex}{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000'); 
                    $sheet->getStyle("{$ultimoColumnaIndex}{$rowNum}")->getFont()->getColor()->setARGB('FFFFFF');
                }
                
                $rowNum++;
            }

            $sheet->mergeCells("B{$rowNum}:C{$rowNum}");
            $sheet->setCellValue("B{$rowNum}", 'Fecha de reporte: ' . \Carbon\Carbon::parse($fechaCorte)->format('d de F de Y'));
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue("{$penultimoColumnaIndex}{$rowNum}", 'TOTAL DE AVANCE:');
            $sheet->getStyle("{$penultimoColumnaIndex}{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("{$penultimoColumnaIndex}{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $promedioFinal = $estudiantesContados > 0 ? ($sumaPorcentajes / $estudiantesContados) : 0;
            $sheet->setCellValue("{$ultimoColumnaIndex}{$rowNum}", number_format($promedioFinal, 2) . '%');
            $sheet->getStyle("{$ultimoColumnaIndex}{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A1:{$ultimoColumnaIndex}{$rowNum}")->applyFromArray($styleArray);

            $filename = 'Avances_Modulo_' . $fechaCorte . '_' . mb_strtoupper($asignacion->grupo->codigo_grupo, 'UTF-8') . '.xlsx';

        } else {
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Informacion General');

            $sheet1->setCellValue('A1', 'INFORME DE AVANCE DE LOS PROCESOS FORMATIVOS DEL CNFDI');
            $sheet1->mergeCells('A1:M1');
            $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            $sheet1->setCellValue('A3', 'Carrera:'); 
            $sheet1->setCellValue('B3', 'Docente que acompaña la formación'); 
            $sheet1->setCellValue('C3', 'Módulo Formativo activo');
            $sheet1->setCellValue('D3', 'Avance Programatico'); 
            $sheet1->setCellValue('E3', 'Modalidad'); 
            $sheet1->setCellValue('F3', 'Meta');
            $sheet1->setCellValue('G3', 'Matricula Actual'); 
            $sheet1->setCellValue('H3', 'Activos en la formación'); 
            $sheet1->setCellValue('I3', 'Activos sin actividades');
            $sheet1->setCellValue('J3', 'Nunca (mas de 20 dias)'); 
            $sheet1->setCellValue('K3', 'Observaciones o incidencias generales'); 
            $sheet1->setCellValue('L3', 'Retiros'); 
            $sheet1->setCellValue('M3', 'Reparacion');
            $sheet1->getStyle('A3:M3')->getFont()->setBold(true); 
            $sheet1->getStyle('A3:M3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');

            $totalMatricula = 0; $activos = 0; $inactivos = 0; $nunca = 0; $retiros = 0;
            $fechaInicio = \Carbon\Carbon::parse($asignacion->fecha_inicio);
            $fechaCorteCarbon = \Carbon\Carbon::parse($fechaCorte);
            $diasTranscurridos = max(0, $fechaInicio->diffInDays($fechaCorteCarbon, false));
            $duracionTotal = $fechaInicio->diffInDays(\Carbon\Carbon::parse($asignacion->fecha_fin));
            $porcentajeAvance = ($duracionTotal > 0 and $diasTranscurridos > 0) ? min(100, round(($diasTranscurridos / $duracionTotal) * 100)) : 0;

            foreach ($asignacion->grupo->estudiantes as $est) {
                if ($est->trashed()) { $retiros++; continue; }
                $totalMatricula++;
                $ultima = Asistencia::where('user_id', $est->id)->where('grupo_id', $asignacion->grupo_id)->where('fecha', '<=', $fechaCorte)->latest('fecha')->first();
                
                if($ultima and $ultima->estado == 'Ausente') {
                    $inactivos++;
                    if($diasTranscurridos > 20 and str_contains(strtolower($ultima->observacion), 'sin actividad')) $nunca++;
                } else { $activos++; }
            }

            $sheet1->setCellValue('A4', mb_strtoupper($asignacion->grupo->carrera->nombre ?? 'N/A', 'UTF-8'));
            $sheet1->setCellValue('B4', mb_convert_case($asignacion->docente->name ?? 'N/A', MB_CASE_TITLE, "UTF-8"));
            $sheet1->setCellValue('C4', $asignacion->modulo->nombre);
            $sheet1->setCellValue('D4', $porcentajeAvance . '%');
            $sheet1->setCellValue('E4', strtolower($asignacion->grupo->modalidad));
            $sheet1->setCellValue('F4', $asignacion->grupo->meta);
            $sheet1->setCellValue('G4', $totalMatricula);
            $sheet1->setCellValue('H4', $activos);
            $sheet1->setCellValue('I4', $inactivos);
            $sheet1->setCellValue('J4', $nunca);
            $sheet1->setCellValue('K4', $asignacion->observacion_general ?? 'Sin observaciones.');
            $sheet1->setCellValue('L4', $retiros);
            $sheet1->setCellValue('M4', '0');
            foreach (range('A', 'M') as $col) { $sheet1->getColumnDimension($col)->setAutoSize(true); }

            $sheet2 = $spreadsheet->createSheet(); 
            $sheet2->setTitle('Nombre de Inactivos por carrera');
            
            $headers = [
                'Carrera', 
                'Código de Grupo', 
                'Nombre del Estudiante', 
                'Institucion de Procedencia (INATEC, MINED, UNIVERSIDAD O PUBLICO GENERAL)', 
                'Acciones de seguimiento individual'
            ];
            $sheet2->fromArray($headers, NULL, "A1");
            $sheet2->getStyle("A1:E1")->getFont()->setBold(true); 
            $sheet2->getStyle("A1:E1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('BDD7EE');

            $rowNum = 2;
            foreach ($asignacion->grupo->estudiantes as $est) {
                if ($est->trashed()) continue;
                
                $ultimaAsistencia = Asistencia::where('user_id', $est->id)->where('grupo_id', $asignacion->grupo_id)->where('fecha', '<=', $fechaCorte)->latest('fecha')->first();
                
                if (!$ultimaAsistencia || $ultimaAsistencia->estado !== 'Ausente') {
                    continue; 
                }

                $sheet2->setCellValue("A{$rowNum}", mb_strtoupper($asignacion->grupo->carrera->nombre ?? 'N/A', 'UTF-8'));
                $sheet2->setCellValue("B{$rowNum}", mb_strtoupper($asignacion->grupo->codigo_grupo, 'UTF-8'));
                $sheet2->setCellValue("C{$rowNum}", mb_strtoupper($est->name, 'UTF-8'));
                $sheet2->setCellValue("D{$rowNum}", mb_strtoupper($est->procedencia ?? 'INATEC', 'UTF-8'));
                $sheet2->setCellValue("E{$rowNum}", $ultimaAsistencia->observacion ?? 'Sin registro');
                $rowNum++;
            }
            foreach (range('A', 'E') as $col) { $sheet2->getColumnDimension($col)->setAutoSize(true); }
            
            $spreadsheet->setActiveSheetIndex(0);
            $filename = 'Consolidado_Oficial_' . $fechaCorte . '_' . mb_strtoupper($asignacion->grupo->codigo_grupo, 'UTF-8') . '.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function reporte(Request $request, $asignacion_id)
    {
        $asignacion = Asignacion::with(['grupo.estudiantes' => function($q) { $q->withTrashed()->orderBy('name', 'asc'); }, 'modulo'])->findOrFail($asignacion_id);
        if ($asignacion->docente_id !== auth()->id()) abort(403, 'Acceso denegado.');

        $fechasSemaforo = [];
        $archivos = glob(storage_path("app/semaforos/asig_{$asignacion_id}_*.json"));
        if($archivos) {
            foreach ($archivos as $archivo) {
                if (preg_match('/asig_\d+_(.*)\.json/', $archivo, $matches)) $fechasSemaforo[] = $matches[1];
            }
            rsort($fechasSemaforo);
        }

        $fechaCorte = $request->query('fecha_corte', count($fechasSemaforo) > 0 ? $fechasSemaforo[0] : null);
        
        $semaforoData = null;
        if ($fechaCorte && file_exists(storage_path("app/semaforos/asig_{$asignacion_id}_{$fechaCorte}.json"))) {
            $semaforoData = json_decode(file_get_contents(storage_path("app/semaforos/asig_{$asignacion_id}_{$fechaCorte}.json")), true);
            
            // FILTRO RETROACTIVO: Limpia columnas basura de reportes viejos para la vista web
            if(isset($semaforoData['actividades'])) {
                $actividadesLimpias = [];
                $ignorar = ['ciudad', 'departamento', 'institución', 'institucion', 'país', 'pais', 'descargar'];
                foreach($semaforoData['actividades'] as $act) {
                    $actLower = strtolower($act);
                    $saltar = false;
                    foreach($ignorar as $ign) {
                        if(str_contains($actLower, $ign)) { $saltar = true; break; }
                    }
                    if(!$saltar) { $actividadesLimpias[] = $act; }
                }
                $semaforoData['actividades'] = array_values($actividadesLimpias);
            }
        }

        $fechasConsolidado = Asistencia::where('grupo_id', $asignacion->grupo_id)->select('fecha')->distinct()->orderBy('fecha', 'desc')->pluck('fecha')->toArray();

        return view('docente.reporte', compact('asignacion', 'fechasSemaforo', 'fechaCorte', 'semaforoData', 'fechasConsolidado'));
    }

    public function reconocimientos($asignacion_id)
    {
        $asignacion = Asignacion::with(['grupo.estudiantes' => function($q) { $q->orderBy('name', 'asc'); }, 'modulo'])->findOrFail($asignacion_id);
        if ($asignacion->docente_id !== auth()->id()) abort(403, 'Acceso denegado.');
        return view('docente.reconocimientos', compact('asignacion'));
    }
}