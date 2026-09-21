@extends('layouts.app')

@section('title', 'Semáforo de Rendimiento')
@section('page_title', 'Semáforo de Rendimiento')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 md:p-8 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#2a348e]">Semáforo de Rendimiento</h2>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-sm font-bold text-gray-500">Viendo reporte procesado el:</span>
                <form action="{{ route('docente.reporte', $asignacion->id) }}" method="GET" class="inline-block" id="formFecha">
                    <select name="fecha_corte" onchange="document.getElementById('formFecha').submit()" class="text-sm border-gray-300 rounded-lg focus:ring-[#2a348e] focus:border-[#2a348e] font-bold text-[#2a348e] bg-white py-1.5 pl-3 pr-8 shadow-sm">
                        @forelse($fechasSemaforo as $fecha)
                            <option value="{{ $fecha }}" {{ $fechaCorte == $fecha ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</option>
                        @empty
                            <option value="">Sin reportes previos</option>
                        @endforelse
                    </select>
                </form>
            </div>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            @if($semaforoData)
            <div class="bg-white px-5 py-2.5 rounded-xl border border-gray-200 shadow-sm text-center min-w-[140px]">
                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">Promedio Global</span>
                @php
                    $totalGlobal = 0; $estActivos = 0;
                    $acts = $semaforoData['actividades'] ?? [];
                    $totActs = count($acts);
                    if($totActs > 0) {
                        foreach($asignacion->grupo->estudiantes as $e) {
                            if($e->trashed()) continue;
                            $datosE = $semaforoData['estudiantes'][$e->id] ?? null;
                            if($datosE) {
                                $comps = 0;
                                foreach($acts as $a) { if(($datosE[$a] ?? '') == 'Completado') $comps++; }
                                $totalGlobal += ($comps / $totActs) * 100;
                                $estActivos++;
                            }
                        }
                    }
                    $promedioGlobal = $estActivos > 0 ? round($totalGlobal / $estActivos) : 0;
                @endphp
                <span class="text-2xl font-black text-[#2a348e]">{{ $promedioGlobal }}%</span>
            </div>
            
            <a href="{{ route('docente.exportar', ['asignacion_id' => $asignacion->id, 'tipo' => 'semaforo', 'fecha_corte' => $fechaCorte]) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3.5 rounded-xl font-bold transition-all flex items-center gap-2 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Exportar Excel
            </a>
            @endif
        </div>
    </div>

    @if($semaforoData)
    <div class="p-5 border-b border-gray-100 bg-white">
        <div class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="buscadorSemaforo" onkeyup="filtrarTabla()" placeholder="Buscar por nombre del protagonista..." class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#2a348e] focus:border-[#2a348e] font-medium text-sm transition-all bg-gray-50/50 shadow-inner">
        </div>
    </div>

    <div class="overflow-x-auto custom-scrollbar relative">
        <table class="w-full text-left border-collapse whitespace-nowrap" id="tablaSemaforo">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-[11px] uppercase tracking-wider font-black">
                    <!-- Cabecera de N° y Protagonista combinada -->
                    <th class="p-4 sticky left-0 bg-gray-50 z-20 w-80 shadow-[1px_0_0_0_#e5e7eb]">
                        <div class="flex items-center gap-3">
                            <span class="w-6 text-center text-gray-400">N°</span>
                            <span>Protagonista</span>
                        </div>
                    </th>
                    <th class="p-4 text-center min-w-[120px]">Progreso</th>
                    <th class="p-4 text-center min-w-[100px]">Acción</th>
                    @foreach($semaforoData['actividades'] as $actividad)
                        <th class="p-4 max-w-[200px]" title="{{ $actividad }}">
                            <div class="truncate">{{ $actividad }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @php $contadorN = 1; @endphp
                @foreach($asignacion->grupo->estudiantes as $estudiante)
                    @if($estudiante->trashed()) @continue @endif
                    
                    @php
                        $datosE = $semaforoData['estudiantes'][$estudiante->id] ?? null;
                        if(!$datosE) continue;
                        
                        $actividades = $semaforoData['actividades'] ?? [];
                        $totalAct = count($actividades);
                        $completadas = 0;
                        
                        $textoWhatsapp = "*Reporte de Avance - Campus Virtual*%0A%0A";
                        $textoWhatsapp .= "🧑‍🎓 *Protagonista:* " . mb_strtoupper($estudiante->name, 'UTF-8') . "%0A";
                        $textoWhatsapp .= "📚 *Módulo:* " . $asignacion->modulo->nombre . "%0A%0A";
                        $textoWhatsapp .= "*Detalle de Actividades:*%0A";

                        foreach($actividades as $act) {
                            $estado = $datosE[$act] ?? 'No Realizado';
                            if($estado == 'Completado') {
                                $completadas++;
                                $textoWhatsapp .= "✅ " . trim($act) . ": Completado%0A";
                            } elseif($estado == 'Revisar Retroalimentación') {
                                $textoWhatsapp .= "⚠️ " . trim($act) . ": Revisar Retroalimentación%0A";
                            } else {
                                $textoWhatsapp .= "❌ " . trim($act) . ": Pendiente%0A";
                            }
                        }
                        
                        $porcentaje = $totalAct > 0 ? round(($completadas / $totalAct) * 100) : 0;
                        $colorBarra = $porcentaje >= 60 ? 'bg-emerald-500' : ($porcentaje > 0 ? 'bg-amber-400' : 'bg-red-500');
                        $colorTexto = $porcentaje >= 60 ? 'text-emerald-700' : ($porcentaje > 0 ? 'text-amber-600' : 'text-red-600');
                        
                        $textoWhatsapp .= "%0A📊 *Avance Total del Módulo:* " . $porcentaje . "%%0A%0A";
                        $textoWhatsapp .= "Le instamos a completar las actividades pendientes para asegurar el éxito en su formación técnica.";
                    @endphp

                    <tr class="hover:bg-blue-50/40 transition-colors fila-estudiante">
                        <td class="p-4 sticky left-0 bg-white group-hover:bg-blue-50/40 z-10 shadow-[1px_0_0_0_#e5e7eb]">
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-center text-xs font-black text-gray-400">{{ $contadorN++ }}</span>
                                <span class="font-bold text-gray-800 text-sm nombre-estudiante">{{ mb_strtoupper($estudiante->name, 'UTF-8') }}</span>
                            </div>
                        </td>
                        
                        <td class="p-4 align-middle">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black w-9 text-right {{ $colorTexto }}">{{ $porcentaje }}%</span>
                                <div class="w-24 bg-gray-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                                    <div class="{{ $colorBarra }} h-2.5 rounded-full transition-all duration-700" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        </td>

                        <td class="p-4 align-middle text-center">
                            <button onclick="window.open('https://api.whatsapp.com/send?text={{ $textoWhatsapp }}', '_blank')" class="inline-flex items-center justify-center text-emerald-600 hover:text-white hover:bg-emerald-500 border border-emerald-500 bg-emerald-50 p-2 rounded-lg transition-all focus:ring-2 focus:ring-emerald-200" title="Notificar por WhatsApp al protagonista">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </button>
                        </td>

                        @foreach($actividades as $act)
                            @php $estado = $datosE[$act] ?? 'No Realizado'; @endphp
                            <td class="p-4 align-middle">
                                @if($estado == 'Completado')
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-black border border-emerald-100 uppercase tracking-wide">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Completado
                                    </div>
                                @elseif($estado == 'Revisar Retroalimentación')
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-50 text-amber-700 text-[11px] font-black border border-amber-100 uppercase tracking-wide">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Revisar
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-red-50 text-red-600 text-[11px] font-black border border-red-50 uppercase tracking-wide">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Pendiente
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div id="mensajeVacioSemaforo" class="hidden text-center py-16 bg-gray-50 border-t border-gray-100">
            <p class="text-gray-500 font-bold text-lg">No se encontraron protagonistas con ese nombre.</p>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-top: 1px solid #f1f5f9;}
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 2px solid #f8fafc;}
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        tr:hover .sticky { background-color: #eff6ff !important; }
    </style>
    @else
        <div class="p-16 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-2xl font-bold text-gray-600">Aún no hay reportes de Semáforo</h3>
            <p class="text-gray-500 font-medium mt-2 text-lg">Vaya a la pestaña "Gestión Moodle" y suba el archivo de avance para generar el semáforo de esta semana.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function filtrarTabla() {
        let input = document.getElementById('buscadorSemaforo');
        if(!input) return;
        let filter = input.value.toLowerCase();
        let tbody = document.querySelector('#tablaSemaforo tbody');
        let trs = tbody.getElementsByClassName('fila-estudiante');
        let mostrados = 0;

        for (let i = 0; i < trs.length; i++) {
            let td = trs[i].getElementsByClassName('nombre-estudiante')[0];
            if (td) {
                let txtValue = td.textContent || td.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    trs[i].style.display = "";
                    mostrados++;
                } else {
                    trs[i].style.display = "none";
                }
            }       
        }
        
        let msj = document.getElementById('mensajeVacioSemaforo');
        let thead = document.querySelector('#tablaSemaforo thead');
        if(mostrados === 0) {
            msj.style.display = 'block';
            thead.style.display = 'none';
        } else {
            msj.style.display = 'none';
            thead.style.display = 'table-header-group';
        }
    }
</script>
@endsection