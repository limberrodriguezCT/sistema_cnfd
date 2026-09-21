@extends('layouts.app')

@section('title', 'Panel Docente - INATEC')
@section('page_title', 'Inicio - Mis Módulos')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h2 class="text-3xl font-black text-[#2a348e]">Mis Módulos Asignados</h2>
            <p class="text-gray-600 mt-2 text-lg">Seleccione el módulo en el que desea trabajar para gestionar reportes y seguimiento.</p>
        </div>
        <div class="w-full md:w-1/3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="buscador-modulos" onkeyup="filtrarModulos()" placeholder="Buscar módulo o grupo..." class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#2a348e] bg-white shadow-sm font-medium text-sm transition-all">
            </div>
        </div>
    </div>

    @php
        $hoy = \Carbon\Carbon::now()->toDateString();
        
        $enCurso = $asignacionesActivas->filter(function($asig) use ($hoy) {
            return $asig->fecha_inicio <= $hoy;
        })->sortBy('fecha_inicio');

        $proximos = $asignacionesActivas->filter(function($asig) use ($hoy) {
            return $asig->fecha_inicio > $hoy;
        })->sortBy('fecha_inicio');

        $asignacionesOrdenadas = $enCurso->concat($proximos);
        $asignacionesCerradas = $asignacionesCerradas->sortByDesc('fecha_fin');
    @endphp

    <h3 class="font-black text-gray-800 text-2xl mb-6 border-b pb-2 border-gray-200">Módulos en Curso y Próximos</h3>
    
    @if(count($asignacionesOrdenadas) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12 items-stretch" id="contenedor-modulos">
            @foreach($asignacionesOrdenadas as $asignacion)
                @php
                    $esProximo = $asignacion->fecha_inicio > $hoy;
                    $bgHeader = $esProximo ? 'from-gray-500 to-gray-600' : 'from-[#2a348e] to-indigo-700';
                    $badgeBg = $esProximo ? 'bg-amber-500' : 'bg-emerald-500';
                    $badgeText = $esProximo ? 'PRÓXIMO' : 'ACTIVO';
                @endphp
                
                <div class="modulo-card bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group h-full {{ $esProximo ? 'opacity-80' : '' }}" data-filtro="{{ strtolower($asignacion->modulo->nombre . ' ' . $asignacion->grupo->codigo_grupo . ' ' . ($asignacion->grupo->carrera->nombre ?? '')) }}">
                    
                    <div class="bg-gradient-to-r {{ $bgHeader }} p-6 text-white relative overflow-hidden flex flex-col h-60 shrink-0">
                        <div class="absolute -right-6 -top-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"></path></svg>
                        </div>
                        
                        <div class="flex justify-between items-start relative z-10 shrink-0">
                            <span class="bg-white/10 border border-white/20 text-white text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider backdrop-blur-sm">{{ strtoupper($asignacion->grupo->codigo_grupo) }}</span>
                            <span class="{{ $badgeBg }} text-white text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider shadow-sm">{{ $badgeText }}</span>
                        </div>
                        
                        <div class="relative z-10 flex-grow flex items-center py-2">
                            <h3 class="font-black text-xl lg:text-2xl leading-snug line-clamp-3" title="{{ $asignacion->modulo->nombre }}">
                                {{ $asignacion->modulo->nombre }}
                            </h3>
                        </div>
                        
                        <p class="text-white/80 text-sm font-bold flex flex-col gap-1 relative z-10 shrink-0">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $asignacion->modulo->semestre }}
                            </span>
                            @if($esProximo)
                            <span class="flex items-center gap-2 text-amber-200 text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Inicia el {{ \Carbon\Carbon::parse($asignacion->fecha_inicio)->format('d/m/Y') }}
                            </span>
                            @endif
                        </p>
                    </div>

                    <div class="p-6 flex-grow flex flex-col justify-between bg-white">
                        <div class="mb-8">
                            <div class="mb-6">
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Modalidad</span>
                                <span class="text-sm font-bold text-gray-700 bg-gray-100 px-4 py-2 rounded-lg border border-gray-200">{{ ucfirst($asignacion->grupo->modalidad) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Oferta Formativa</span>
                                <span class="text-sm font-bold text-[#2a348e] leading-snug block">
                                    {{ mb_convert_case(mb_strtolower($asignacion->grupo->carrera->nombre ?? 'N/A', 'UTF-8'), MB_CASE_TITLE, "UTF-8") }}
                                </span>
                            </div>
                        </div>
                        
                        @if($esProximo)
                            <button disabled class="block w-full text-center bg-gray-200 text-gray-500 font-black py-4 px-4 rounded-xl transition-all shadow-sm cursor-not-allowed">
                                Disponible Próximamente
                            </button>
                        @else
                            <a href="{{ route('docente.espacio', $asignacion->id) }}" class="block w-full text-center bg-[#2a348e] hover:bg-blue-800 text-white font-black py-4 px-4 rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Ingresar al Espacio
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-12 text-center mb-12">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="text-2xl font-bold text-gray-600">No hay módulos activos</h3>
            <p class="text-gray-500 font-medium mt-2 text-lg">Actualmente no tiene módulos formativos asignados en curso.</p>
        </div>
    @endif

    <div id="mensaje-vacio" class="hidden text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm mb-12">
        <p class="text-gray-500 font-bold text-lg">No se encontraron módulos que coincidan con su búsqueda.</p>
    </div>

    <h3 class="font-black text-gray-800 text-2xl mb-6 border-b pb-2 border-gray-200">Historial de Módulos Cerrados</h3>
    
    @if(count($asignacionesCerradas) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($asignacionesCerradas as $asig)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col h-full hover:shadow-md hover:border-gray-300 transition-all opacity-80 hover:opacity-100">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-black text-[#2a348e] bg-blue-50 px-2 py-1 rounded">{{ strtoupper($asig->grupo->codigo_grupo) }}</span>
                        <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded uppercase border border-gray-200">Finalizado</span>
                    </div>
                    <h4 class="font-bold text-sm text-gray-800 mb-2 leading-tight flex-grow">{{ $asig->modulo->nombre }}</h4>
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ mb_convert_case(mb_strtolower($asig->grupo->carrera->nombre ?? 'N/A', 'UTF-8'), MB_CASE_TITLE, "UTF-8") }}</p>
                    
                    <a href="{{ route('docente.espacio', $asig->id) }}" class="text-xs font-black text-[#2a348e] hover:text-blue-800 uppercase tracking-wide flex items-center gap-1 mt-auto pt-4 border-t border-gray-100">
                        Consultar Reportes <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 rounded-2xl border border-dashed border-gray-300 p-8 text-center">
            <p class="text-gray-500 italic text-sm font-medium">No tiene módulos finalizados en su historial.</p>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    function filtrarModulos() {
        let input = document.getElementById('buscador-modulos').value.toLowerCase();
        let cards = document.querySelectorAll('.modulo-card');
        let mostrados = 0;
        cards.forEach(card => {
            if (card.getAttribute('data-filtro').includes(input)) {
                card.style.display = 'flex'; 
                mostrados++;
            } else { 
                card.style.display = 'none'; 
            }
        });
        document.getElementById('mensaje-vacio').style.display = mostrados === 0 ? 'block' : 'none';
        
        document.getElementById('contenedor-modulos').style.display = mostrados === 0 ? 'none' : 'grid';
    }
</script>
@endsection