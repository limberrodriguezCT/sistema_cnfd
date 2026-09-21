@extends('layouts.app')

@section('title', 'Auditoría Semáforo - CNFD')
@section('page_title', 'Monitoreo de Rendimiento')

@section('content')
    @php
        $actividades = $semaforoData['actividades'] ?? [];
        $estudiantesData = $semaforoData['estudiantes'] ?? [];
        $totalActividades = count($actividades);
        $sumaPorcentajes = 0; $estudiantesActivos = 0;

        foreach($asignacion->grupo->estudiantes as $est) {
            if(!$est->trashed()) {
                $datosEst = $estudiantesData[$est->id] ?? [];
                $completadas = 0;
                foreach($actividades as $act) { if(isset($datosEst[$act]) && $datosEst[$act] === 'Completado') $completadas++; }
                $porc = $totalActividades > 0 ? round(($completadas / $totalActividades) * 100) : 0;
                $sumaPorcentajes += $porc; $estudiantesActivos++;
            }
        }
        $promedioGrupo = $estudiantesActivos > 0 ? round($sumaPorcentajes / $estudiantesActivos) : 0;
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 border-l-4 border-l-purple-600 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800">Auditoría de Historial y Semáforo</h2>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-gray-600 font-medium">Módulo: <span class="text-purple-700">{{ $asignacion->modulo->nombre }}</span> | Viendo reporte del:</p>
                <form action="{{ route('asesor.reporte', $asignacion->id) }}" method="GET" class="inline-block">
                    <select name="fecha_corte" onchange="this.form.submit()" class="text-sm p-1 border rounded font-bold text-purple-700 bg-purple-50 focus:ring-purple-500 outline-none">
                        @if(count($fechasSemaforo) > 0)
                            @foreach($fechasSemaforo as $f)
                                <option value="{{ $f }}" {{ $f == $fechaCorte ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($f)->format('d/m/Y') }}</option>
                            @endforeach
                        @else
                            <option value="">Sin reportes</option>
                        @endif
                    </select>
                </form>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-purple-50 border border-purple-200 px-4 py-2 rounded-lg text-center hidden md:block">
                <p class="text-xs font-bold text-purple-600 uppercase">Promedio del Grupo</p>
                <p class="text-2xl font-black {{ $promedioGrupo < 60 ? 'text-red-600' : 'text-green-600' }}">{{ $promedioGrupo }}%</p>
            </div>
            <a href="{{ route('asesor.panel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                Volver al Panel
            </a>
        </div>
    </div>

    @if(!$semaforoData)
        <div class="text-center py-20 bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <h3 class="text-xl font-bold text-gray-600">No hay datos de Semáforo</h3>
            <p class="text-gray-500 mt-2 font-medium">El docente no ha procesado ningún archivo Moodle aún.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto relative">
                <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="sticky left-0 bg-gray-50 z-20 px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider shadow-[1px_0_0_0_#e5e7eb]">Protagonista</th>
                            @foreach($actividades as $act)
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;" title="{{ $act }}">{{ $act }}</th>
                            @endforeach
                            <th class="px-6 py-4 text-center text-xs font-black text-purple-900 bg-purple-100 uppercase tracking-wider">% Avance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($asignacion->grupo->estudiantes as $est)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="sticky left-0 bg-white z-10 px-6 py-4 font-semibold shadow-[1px_0_0_0_#e5e7eb] {{ $est->trashed() ? 'text-red-500 line-through' : 'text-gray-800' }}">
                                    {{ $est->name }}
                                </td>
                                @php 
                                    $datosEst = $estudiantesData[$est->id] ?? []; 
                                    $completadas = 0;
                                @endphp
                                @foreach($actividades as $act)
                                    @php $estado = $datosEst[$act] ?? 'No Realizado'; @endphp
                                    <td class="px-6 py-4 text-center">
                                        @if($estado == 'Completado')
                                            @php $completadas++; @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Completado</span>
                                        @elseif($estado == 'Revisar Retroalimentación')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Revisar Retro</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">No Realizado</span>
                                        @endif
                                    </td>
                                @endforeach
                                @php $porc = $totalActividades > 0 ? round(($completadas / $totalActividades) * 100) : 0; @endphp
                                <td class="px-6 py-4 text-center font-black text-sm {{ $porc < 60 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $porc }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection