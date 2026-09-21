@extends('layouts.app')

@section('title', 'Gestión Académica - INATEC')
@section('page_title', 'Inicio')

@section('sub_menu_asesor')
    <button onclick="changeTab('tab-estructura')" id="btn-estructura" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-all font-medium text-left sidebar-btn active bg-blue-600 text-white shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        Ofertas y Módulos
    </button>
    <button onclick="changeTab('tab-docentes')" id="btn-docentes" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-all font-medium text-left sidebar-btn text-slate-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        Docentes Activos
    </button>
    <button onclick="changeTab('tab-asignaciones')" id="btn-asignaciones" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-all font-medium text-left sidebar-btn text-slate-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        Asignación de Módulos
    </button>
    <button onclick="changeTab('tab-interesados')" id="btn-interesados" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-all font-medium text-left sidebar-btn text-slate-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        Interesados Web
    </button>
    <button onclick="changeTab('tab-reportes')" id="btn-reportes" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-all font-medium text-left sidebar-btn text-slate-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Reportes de Avance
    </button>
@endsection

@section('content')
    <div class="flex justify-end mb-6">
        <form action="{{ route('asesor.panel') }}" method="GET" class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Año Académico</label>
            <select name="anio" onchange="this.form.submit()" class="text-sm font-black p-1 border-none focus:ring-0 text-blue-800 bg-transparent cursor-pointer">
                @foreach($aniosDisponibles as $a)
                    <option value="{{ $a }}" {{ $anioActivo == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- PESTAÑA OFERTA Y MÓDULOS -->
    <div id="tab-estructura" class="tab-content hidden space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-900 to-indigo-800 rounded-2xl shadow-lg p-6 text-white flex flex-col justify-center items-center text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                </div>
                <h3 class="text-blue-200 font-bold uppercase tracking-wider text-sm mb-2 z-10">Estudiantes Activos {{ $anioActivo }}</h3>
                <p class="text-6xl font-black z-10">{{ $totalMatriculaGeneral }}</p>
                <p class="text-blue-300 text-xs mt-2 font-medium z-10">Total en el centro</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:col-span-1">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider text-center">Población por Oferta</h3>
                <div class="relative h-48 w-full">
                    <canvas id="chartCarreras"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:col-span-1">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider text-center">Estado de Módulos</h3>
                <div class="relative h-48 w-full flex justify-center">
                    <canvas id="chartModulos"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2 text-lg">Crear Oferta</h3>
                <form action="{{ route('asesor.store_carrera') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tipo de Formación</label>
                        <select name="tipo" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            <option value="Carrera">Carrera Técnica</option>
                            <option value="Curso">Curso de Capacitación</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre de la Oferta</label>
                        <input type="text" name="nombre" required placeholder="Ej. Técnico en Tecnología..." class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md">Guardar</button>
                </form>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2 text-lg">Crear Grupo</h3>
                <form action="{{ route('asesor.store_grupo') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Oferta Formativa</label>
                        <select name="carrera_id" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            @if(count($carreras) > 0)
                                @foreach($carreras as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }} ({{ $c->tipo }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Código de Grupo</label>
                        <input type="text" name="codigo_grupo" required placeholder="TED-0496-02-2026" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 uppercase focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Modalidad</label>
                        <select name="modalidad" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            <option value="Presencial">Presencial</option>
                            <option value="Virtual">Virtual</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Meta</label>
                            <input type="number" name="meta" required value="40" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-blue-500 uppercase mb-2">Año</label>
                            <input type="number" name="anio_academico" required value="{{ date('Y') }}" class="w-full text-sm font-black text-blue-800 p-3 border border-blue-300 rounded-xl bg-blue-50 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md">Guardar</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2 text-lg">Crear Módulo</h3>
                <form action="{{ route('asesor.store_modulo') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Oferta Formativa</label>
                        <select name="carrera_id" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            @if(count($carreras) > 0)
                                @foreach($carreras as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tipo de Módulo</label>
                        <select name="tipo_modulo" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            <option value="Técnicos">Técnicos</option>
                            <option value="Transversales">Transversales</option>
                            <option value="Optativos">Optativos</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nombre del Módulo</label>
                        <input type="text" name="nombre" required placeholder="Ej. Desarrollo de Recursos..." class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Semestre</label>
                        <select name="semestre" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            <option value="I Semestre">I Semestre</option>
                            <option value="II Semestre">II Semestre</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md">Guardar</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mt-8">
            <h3 class="font-black text-gray-800 p-6 border-b bg-gray-50 text-xl">Oferta Activa - Año {{ $anioActivo }}</h3>
            <div class="p-8">
                @if(count($carreras) > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        @php
                            $colores = [
                                ['border' => 'border-blue-200', 'bg' => 'bg-blue-50', 'text' => 'text-blue-900', 'badge' => 'bg-blue-100 text-blue-800'],
                                ['border' => 'border-emerald-200', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-900', 'badge' => 'bg-emerald-100 text-emerald-800'],
                                ['border' => 'border-purple-200', 'bg' => 'bg-purple-50', 'text' => 'text-purple-900', 'badge' => 'bg-purple-100 text-purple-800'],
                                ['border' => 'border-amber-200', 'bg' => 'bg-amber-50', 'text' => 'text-amber-900', 'badge' => 'bg-amber-100 text-amber-800'],
                                ['border' => 'border-rose-200', 'bg' => 'bg-rose-50', 'text' => 'text-rose-900', 'badge' => 'bg-rose-100 text-rose-800'],
                            ];
                        @endphp
                        
                        @foreach($carreras as $index => $carrera)
                            @php $color = $colores[$index % count($colores)]; @endphp
                            <div class="border {{ $color['border'] }} rounded-xl p-6 bg-white shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                                <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-3">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-black text-lg {{ $color['text'] }}">{{ $carrera->nombre }}</h4>
                                        <button onclick="editarCarrera({{ $carrera->id }}, '{{ addslashes($carrera->nombre) }}', '{{ $carrera->tipo }}')" class="text-gray-400 hover:{{ $color['text'] }} transition-colors" title="Editar Oferta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button onclick="confirmarEliminarCarrera({{ $carrera->id }}, '{{ addslashes($carrera->nombre) }}')" class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar Oferta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <span class="text-[10px] font-black uppercase px-2 py-1 rounded-md {{ $color['badge'] }} shrink-0">{{ $carrera->tipo ?? 'CARRERA' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-6 flex-grow">
                                    <div>
                                        <h5 class="text-xs font-bold text-gray-400 uppercase mb-3">Grupos Activos</h5>
                                        <ul class="text-sm space-y-2">
                                            @if(count($carrera->grupos) > 0)
                                                @foreach($carrera->grupos as $grupo)
                                                    <li class="font-bold bg-gray-100 px-2 py-1 rounded text-gray-700 mb-1 flex justify-between items-center group">
                                                        <span>{{ strtoupper($grupo->codigo_grupo) }}</span>
                                                        <div class="flex gap-2">
                                                            <button type="button" onclick="confirmarEdicionGrupo({{ $grupo->id }}, '{{ $grupo->codigo_grupo }}', '{{ $grupo->modalidad }}', {{ $grupo->meta }}, {{ $grupo->anio_academico }}, {{ $carrera->id }})" class="text-gray-400 hover:text-amber-500 opacity-50 group-hover:opacity-100 transition-opacity" title="Editar Grupo">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            </button>
                                                            <button type="button" onclick="confirmarEliminarGrupo({{ $grupo->id }}, '{{ $grupo->codigo_grupo }}')" class="text-gray-400 hover:text-red-500 opacity-50 group-hover:opacity-100 transition-opacity" title="Eliminar Grupo">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-gray-400 italic text-xs">Sin grupos</li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="flex flex-col h-full">
                                        <h5 class="text-xs font-bold text-gray-400 uppercase mb-3">Módulos</h5>
                                        @if(count($carrera->modulos) > 0)
                                            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                                @php
                                                    $modulosAgrupados = $carrera->modulos->sortBy(function($m) {
                                                        $semestres = ['I Semestre' => 1, 'II Semestre' => 2];
                                                        $tipos = ['Transversales' => 1, 'Técnicos' => 2, 'Optativos' => 3];
                                                        $s = $semestres[$m->semestre] ?? 99;
                                                        $t = $tipos[$m->tipo_modulo] ?? 99;
                                                        return sprintf('%02d-%02d-%s', $s, $t, $m->nombre);
                                                    })->groupBy('semestre')->sortBy(function($item, $key) {
                                                        $semestres = ['I Semestre' => 1, 'II Semestre' => 2];
                                                        return $semestres[$key] ?? 99;
                                                    });
                                                @endphp

                                                @foreach($modulosAgrupados as $semestre => $modulosSemestre)
                                                    <div>
                                                        <h6 class="text-[10px] font-black {{ $color['text'] }} uppercase tracking-widest mb-2 border-b {{ $color['border'] }} pb-1">{{ $semestre }}</h6>
                                                        <ul class="text-sm space-y-2">
                                                            @foreach($modulosSemestre as $modulo)
                                                                <li class="{{ $color['bg'] }} p-2 rounded-lg border border-gray-200 font-semibold text-xs text-gray-700 flex justify-between items-center group">
                                                                    <div class="flex flex-col gap-1">
                                                                        <span>{{ $modulo->nombre }}</span>
                                                                        @php
                                                                            $badgeColor = 'bg-gray-200 text-gray-700';
                                                                            if($modulo->tipo_modulo == 'Técnicos') $badgeColor = 'bg-blue-100 text-blue-700';
                                                                            if($modulo->tipo_modulo == 'Transversales') $badgeColor = 'bg-emerald-100 text-emerald-700';
                                                                            if($modulo->tipo_modulo == 'Optativos') $badgeColor = 'bg-amber-100 text-amber-700';
                                                                        @endphp
                                                                        <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded w-fit {{ $badgeColor }}">{{ $modulo->tipo_modulo ?? 'Técnicos' }}</span>
                                                                    </div>
                                                                    <div class="flex gap-2">
                                                                        <button onclick="editarModulo({{ $modulo->id }}, '{{ addslashes($modulo->nombre) }}', '{{ addslashes($modulo->semestre) }}', {{ $modulo->carrera_id }}, '{{ $modulo->tipo_modulo }}')" class="text-gray-400 hover:text-blue-600 opacity-50 group-hover:opacity-100 transition-opacity" title="Editar Módulo">
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                        </button>
                                                                        <button onclick="confirmarEliminarModulo({{ $modulo->id }}, '{{ addslashes($modulo->nombre) }}')" class="text-gray-400 hover:text-red-500 opacity-50 group-hover:opacity-100 transition-opacity" title="Eliminar Módulo">
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                        </button>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-400 italic text-xs">Sin módulos registrados.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="col-span-full text-center text-gray-500 py-12 border-2 border-dashed rounded-xl bg-gray-50">Aún no hay ofertas formativas registradas en este año académico.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- PESTAÑA DOCENTES -->
    <div id="tab-docentes" class="tab-content hidden space-y-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-black text-gray-800 mb-6 border-b pb-3 text-xl">Registrar Nuevo Docente</h3>
            <form action="{{ route('asesor.store_docente') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre Completo</label>
                        <input type="text" name="name" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Correo Institucional</label>
                        <input type="email" name="email" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Contraseña</label>
                        <input type="password" name="password" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tipo de Contrato</label>
                        <select name="tipo_contrato" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                            <option value="Determinado">Determinado (Fijo Anual)</option>
                            <option value="Servicio Profesional">Servicio Profesional (Por Módulo)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Inicio de Contrato</label>
                        <input type="date" name="contrato_inicio" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Fin de Contrato</label>
                        <input type="date" name="contrato_fin" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="flex justify-end border-t pt-4">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-10 rounded-xl shadow-md transition-colors">Guardar Docente</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-black text-gray-800 text-xl">Docentes Activos</h3>
                <input type="text" id="busc-docentes" onkeyup="filtrarTabla('busc-docentes', 'tabla-docentes')" placeholder="Buscar docente..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-full md:w-64 shadow-sm focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm border-collapse" id="tabla-docentes">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 uppercase text-xs tracking-wider">
                            <th class="py-3 px-4 font-bold">Docente / Correo</th>
                            <th class="py-3 px-4 font-bold">Contrato / Vigencia</th>
                            <th class="py-3 px-4 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($docentes) > 0)
                            @foreach($docentes as $doc)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors filtro-fila {{ $doc->trashed() ? 'opacity-50' : '' }}">
                                    <td class="py-4 px-4">
                                        <p class="font-bold text-gray-800 {{ $doc->trashed() ? 'line-through text-red-500' : '' }}">{{ $doc->name }}</p>
                                        <p class="text-gray-500 text-xs mt-1">{{ $doc->email }}</p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded text-xs uppercase">{{ $doc->tipo_contrato ?? 'N/A' }}</span>
                                        <p class="text-gray-600 text-xs font-medium mt-2">Fin: <span class="font-bold">{{ $doc->contrato_fin ? \Carbon\Carbon::parse($doc->contrato_fin)->format('d/m/Y') : 'N/A' }}</span></p>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex flex-col gap-1 items-center justify-center">
                                            <button onclick="editarDocente({{ $doc->id }}, '{{ addslashes($doc->name) }}', '{{ addslashes($doc->email) }}', '{{ $doc->tipo_contrato }}', '{{ $doc->contrato_inicio }}', '{{ $doc->contrato_fin }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold py-1.5 px-4 rounded-lg text-xs transition-colors w-24">Editar</button>
                                            <form action="{{ route('asesor.toggle_docente', $doc->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="{{ $doc->trashed() ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} hover:opacity-80 font-bold py-1.5 px-4 rounded-lg text-xs transition-colors w-24">
                                                    {{ $doc->trashed() ? 'Activar' : 'Ocultar' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="3" class="py-10 text-center text-gray-500 border-2 border-dashed rounded-xl">Sin docentes en el sistema</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PESTAÑA ASIGNACIONES -->
    <div id="tab-asignaciones" class="tab-content hidden space-y-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-black text-gray-800 mb-6 border-b pb-3 text-xl">Asignar Módulo a Docente ({{ $anioActivo }})</h3>
            <form action="{{ route('asesor.store_asignacion') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Docente Formador</label>
                        <select name="docente_id" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-purple-500">
                            <option value="">Seleccione Docente</option>
                            @if(count($docentes) > 0)
                                @foreach($docentes as $doc)
                                    @if(!$doc->trashed())
                                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Grupo Destino</label>
                        <select name="grupo_id" id="grupo_select" onchange="filtrarModulosPorGrupo()" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-purple-500">
                            <option value="">Seleccione Grupo</option>
                            @if(count($grupos) > 0)
                                @foreach($grupos as $g)
                                    <option value="{{ $g->id }}" data-carrera="{{ $g->carrera_id }}">{{ strtoupper($g->codigo_grupo) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Módulo Formativo</label>
                        <select name="modulo_id" id="modulo_select" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-purple-500 disabled:opacity-50" disabled>
                            <option value="">Seleccione Módulo</option>
                            @if(count($modulos) > 0)
                                @foreach($modulos as $m)
                                    <option value="{{ $m->id }}" data-carrera="{{ $m->carrera_id }}" class="hidden modulo-option">{{ $m->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Fecha Fin</label>
                        <input type="date" name="fecha_fin" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="flex justify-end border-t pt-4">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-10 rounded-xl shadow-md transition-colors">Guardar Asignación</button>
                </div>
            </form>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-black text-gray-800 text-xl">Módulos Asignados ({{ $anioActivo }})</h3>
                <input type="text" id="busc-asignaciones" onkeyup="filtrarTabla('busc-asignaciones', 'tabla-asignaciones')" placeholder="Buscar módulo, grupo, o docente..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-full md:w-64 shadow-sm focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm border-collapse" id="tabla-asignaciones">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 uppercase text-xs tracking-wider">
                            <th class="py-3 px-4 font-bold">Docente</th>
                            <th class="py-3 px-4 font-bold">Grupo</th>
                            <th class="py-3 px-4 font-bold">Módulo</th>
                            <th class="py-3 px-4 font-bold text-center">Estado</th>
                            <th class="py-3 px-4 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($asignaciones) > 0)
                            @foreach($asignaciones as $asig)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors filtro-fila {{ $asig->trashed() ? 'opacity-50' : '' }}">
                                    <td class="py-4 px-4 font-bold text-gray-800 {{ $asig->trashed() ? 'line-through text-red-500' : '' }}">{{ $asig->docente->name ?? 'N/A' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="bg-purple-100 text-purple-800 font-bold px-2 py-1 rounded">{{ strtoupper($asig->grupo->codigo_grupo ?? 'N/A') }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600">{{ $asig->modulo->nombre ?? 'N/A' }}</td>
                                    <td class="py-4 px-4 text-center">
                                        @if($asig->estado == 'activo')
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase">Activo</span>
                                        @else
                                            <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-black uppercase">Cerrado</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center flex justify-center gap-2">
                                        <button onclick="editarAsignacion({{ $asig->id }}, '{{ $asig->fecha_inicio }}', '{{ $asig->fecha_fin }}', '{{ $asig->estado }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold py-1 px-3 rounded-lg text-xs transition-colors">Editar</button>
                                        <form action="{{ route('asesor.toggle_asignacion', $asig->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="{{ $asig->trashed() ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} hover:opacity-80 font-bold py-1 px-3 rounded-lg text-xs transition-colors">
                                                {{ $asig->trashed() ? 'Activar' : 'Ocultar' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="5" class="py-10 text-center text-gray-500 border-2 border-dashed rounded-xl">Sin asignaciones registradas en este año</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PESTAÑA DE INTERESADOS WEB -->
    <div id="tab-interesados" class="tab-content hidden space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b bg-emerald-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-auto">
                    <h3 class="font-black text-emerald-900 text-xl">Interesados Web ({{ $anioActivo }})</h3>
                    <p class="text-sm text-emerald-700">Listado de docentes y futuros protagonistas registrados desde la página web principal.</p>
                </div>
                <div class="flex items-center justify-end gap-3 w-full md:w-auto">
                    <input type="text" id="busc-prospectos" onkeyup="filtrarTabla('busc-prospectos', 'tabla-prospectos')" placeholder="Buscar por nombre, correo..." class="px-3 py-2.5 border border-emerald-200 rounded-lg text-sm w-full md:w-64 shadow-sm focus:ring-2 focus:ring-emerald-500 bg-white">
                    <a href="{{ route('asesor.exportar_interesados') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg font-bold transition-all shadow-sm flex items-center gap-2 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Exportar Excel
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm border-collapse" id="tabla-prospectos">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100 uppercase text-[10px] tracking-widest font-black">
                            <th class="py-4 px-4">Fecha de Registro</th>
                            <th class="py-4 px-4">Datos del Interesado</th>
                            <th class="py-4 px-4">Modalidad Solicitada</th>
                            <th class="py-4 px-4">Oferta Formativa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($interesados) > 0)
                            @foreach($interesados as $prospecto)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors filtro-fila">
                                    <td class="py-4 px-4 font-bold text-gray-500 whitespace-nowrap align-middle">{{ $prospecto->created_at->format('d/m/Y h:i A') }}</td>
                                    <td class="py-4 px-4 align-middle">
                                        <p class="font-black text-gray-800 text-sm uppercase">{{ mb_convert_case($prospecto->nombre ?? $prospecto->name ?? '', MB_CASE_TITLE, "UTF-8") }}</p>
                                        <div class="flex items-center gap-3 mt-1 text-xs font-semibold">
                                            <span class="text-blue-600 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> {{ strtolower($prospecto->correo ?? $prospecto->email ?? '') }}</span>
                                            <span class="text-emerald-600 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> {{ $prospecto->telefono ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-middle">
                                        <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase border border-gray-200">{{ mb_strtoupper($prospecto->modalidad ?? 'VIRTUAL', 'UTF-8') }}</span>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-gray-700 text-xs align-middle">{{ mb_convert_case($prospecto->carrera->nombre ?? 'N/A', MB_CASE_TITLE, "UTF-8") }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4" class="py-10 text-center text-gray-500 border-2 border-dashed rounded-xl">No hay interesados registrados para este año académico.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PESTAÑA DE REPORTES -->
    <div id="tab-reportes" class="tab-content hidden space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b bg-blue-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-black text-gray-800 text-xl">Avance de Módulos ({{ $anioActivo }})</h3>
                <div class="flex gap-3">
                    <button onclick="document.getElementById('modalExportarGlobal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-sm shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Descargar Reporte Final
                    </button>
                    <input type="text" id="busc-reportes" onkeyup="filtrarTabla('busc-reportes', 'tabla-reportes')" placeholder="Filtrar por grupo o módulo..." class="px-3 py-2 border border-blue-200 rounded-lg text-sm w-64 shadow-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm border-collapse" id="tabla-reportes">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 uppercase text-xs tracking-wider">
                            <th class="py-3 px-4 font-bold">Grupo</th>
                            <th class="py-3 px-4 font-bold">Módulo</th>
                            <th class="py-3 px-4 font-bold">Docente</th>
                            <th class="py-3 px-4 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($asignaciones) > 0)
                            @foreach($asignaciones as $asig)
                                @if(!$asig->trashed())
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors filtro-fila">
                                        <td class="py-4 px-4 font-bold text-blue-800">{{ strtoupper($asig->grupo->codigo_grupo ?? 'N/A') }}</td>
                                        <td class="py-4 px-4 font-semibold text-gray-800">{{ $asig->modulo->nombre ?? 'N/A' }}</td>
                                        <td class="py-4 px-4 font-medium text-gray-600">{{ $asig->docente->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-4 flex justify-center gap-2">
                                            <a href="{{ route('asesor.reporte', $asig->id) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold py-2 px-3 rounded-lg text-xs transition-colors">Ver Detalles</a>
                                            <button onclick="abrirModalExportar({{ $asig->id }})" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold py-2 px-3 rounded-lg text-xs transition-colors cursor-pointer">Exportar Documentos</button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">Sin módulos activos en este año</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODALES DE EDICIÓN Y EXPORTACIÓN -->
    <div id="modalEditCarrera" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Editar Oferta Formativa</h3>
            <form id="formEditCarrera" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tipo de Formación</label>
                    <select name="tipo" id="edit_car_tipo" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        <option value="Carrera">Carrera Técnica</option>
                        <option value="Curso">Curso de Capacitación</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre de la Oferta</label>
                    <input type="text" name="nombre" id="edit_car_nombre" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-md">Actualizar</button>
                    <button type="button" onclick="document.getElementById('modalEditCarrera').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR GRUPO -->
    <div id="modalEditGrupo" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Editar Grupo
            </h3>
            <form id="formEditGrupo" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Oferta Formativa</label>
                    <select name="carrera_id" id="edit_gru_carrera" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-amber-500">
                        @foreach($carreras as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Código de Grupo</label>
                    <input type="text" name="codigo_grupo" id="edit_gru_codigo" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 uppercase focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Modalidad</label>
                    <select name="modalidad" id="edit_gru_modalidad" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-amber-500">
                        <option value="Presencial">Presencial</option>
                        <option value="Virtual">Virtual</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Meta</label>
                        <input type="number" name="meta" id="edit_gru_meta" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Año</label>
                        <input type="number" name="anio_academico" id="edit_gru_anio" required class="w-full text-sm font-black text-gray-800 p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl shadow-md transition-colors">Confirmar Cambios</button>
                    <button type="button" onclick="document.getElementById('modalEditGrupo').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditModulo" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Editar Módulo</h3>
            <form id="formEditModulo" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Oferta Formativa</label>
                    <select name="carrera_id" id="edit_mod_carrera" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                        @foreach($carreras as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option> @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tipo de Módulo</label>
                    <select name="tipo_modulo" id="edit_mod_tipo" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                        <option value="Técnicos">Técnicos</option>
                        <option value="Transversales">Transversales</option>
                        <option value="Optativos">Optativos</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre del Módulo</label>
                    <input type="text" name="nombre" id="edit_mod_nombre" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Semestre</label>
                    <select name="semestre" id="edit_mod_semestre" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                        <option value="I Semestre">I Semestre</option>
                        <option value="II Semestre">II Semestre</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-md">Actualizar</button>
                    <button type="button" onclick="document.getElementById('modalEditModulo').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditDocente" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Editar Docente</h3>
            <form id="formEditDocente" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre Completo</label>
                    <input type="text" name="name" id="edit_doc_name" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Correo Institucional</label>
                    <input type="email" name="email" id="edit_doc_email" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tipo de Contrato</label>
                    <select name="tipo_contrato" id="edit_doc_contrato" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                        <option value="Determinado">Determinado (Fijo Anual)</option>
                        <option value="Servicio Profesional">Servicio Profesional (Por Módulo)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Contrato Inicio</label>
                        <input type="date" name="contrato_inicio" id="edit_doc_inicio" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Contrato Fin</label>
                        <input type="date" name="contrato_fin" id="edit_doc_fin" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para no cambiar" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-md">Actualizar</button>
                    <button type="button" onclick="document.getElementById('modalEditDocente').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditAsignacion" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Editar Asignación</h3>
            <form id="formEditAsignacion" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="edit_asig_inicio" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="edit_asig_fin" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Estado del Módulo</label>
                    <select name="estado" id="edit_asig_estado" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 font-bold text-gray-700">
                        <option value="activo">Activo (En Curso)</option>
                        <option value="finalizado">Finalizado (Cerrado)</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-xl shadow-md">Actualizar</button>
                    <button type="button" onclick="document.getElementById('modalEditAsignacion').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalExportarGlobal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Descargar Consolidado Final</h3>
            <form action="{{ route('asesor.exportar_global') }}" method="GET">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Filtrar por fecha de Moodle</label>
                    <select name="fecha_corte" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Último reporte disponible</option>
                        @foreach($fechasSemaforoGlobal as $fecha)
                            <option value="{{ $fecha }}">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-md">Descargar Excel</button>
                    <button type="button" onclick="document.getElementById('modalExportarGlobal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalExportar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-3xl">
            <h3 class="font-black text-gray-800 mb-6 border-b pb-4 text-2xl text-center">Descargar Reportes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjeta Consolidado -->
                <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-200 flex flex-col justify-between">
                    <div>
                        <h4 class="font-black text-emerald-800 mb-1 text-lg">Reporte Final (INATEC)</h4>
                        <p class="text-xs text-emerald-600 mb-4 font-medium">Formato oficial con conteo de asistencia y notas.</p>
                    </div>
                    <form method="GET" action="#" id="formExportarConsolidado">
                        <input type="hidden" name="tipo" value="consolidado">
                        <label class="block text-xs font-bold text-emerald-700 uppercase mb-2 mt-2">Fecha de Registro</label>
                        <select name="fecha_corte" id="select_fecha_corte_consolidado" class="w-full text-sm p-3 border border-emerald-300 rounded-lg mb-4 bg-white font-bold"></select>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 rounded-lg shadow-sm">Descargar Documento</button>
                    </form>
                </div>
                <!-- Tarjeta Semáforo -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-200 flex flex-col justify-between">
                    <div>
                        <h4 class="font-black text-blue-800 mb-1 text-lg">Reporte de Avance (Semáforo)</h4>
                        <p class="text-xs text-blue-600 mb-4 font-medium">Sábana detallada del rendimiento estudiantil.</p>
                    </div>
                    <form method="GET" action="#" id="formExportarSemaforo">
                        <input type="hidden" name="tipo" value="semaforo">
                        <label class="block text-xs font-bold text-blue-700 uppercase mb-2 mt-2">Documento de Moodle</label>
                        <select name="fecha_corte" id="select_fecha_corte_semaforo" class="w-full text-sm p-3 border border-blue-300 rounded-lg mb-4 bg-white font-bold"></select>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-lg shadow-sm">Descargar Documento</button>
                    </form>
                </div>
            </div>
            <div class="mt-6 text-center border-t pt-4">
                <button type="button" onclick="document.getElementById('modalExportar').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-8 rounded-lg transition-colors">Cerrar</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const fechasConsolidadoMap = @json($fechasConsolidado);
    const fechasSemaforoMap = @json($fechasSemaforo);

    function changeTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('block');
        });
        
        document.querySelectorAll('.sidebar-btn').forEach(el => {
            el.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-md');
            el.classList.add('text-slate-300');
        });
        
        let tabContent = document.getElementById(tabId);
        if(tabContent) {
            tabContent.classList.remove('hidden');
            tabContent.classList.add('block');
        }
        
        let btnId = 'btn-' + tabId.replace('tab-', '');
        let btn = document.getElementById(btnId);
        if(btn) {
            btn.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-md');
            btn.classList.remove('text-slate-300');
        }

        localStorage.setItem('activeTabPanelAsesor', tabId);
    }

    function filtrarTabla(inputId, tablaId) {
        let input = document.getElementById(inputId).value.toLowerCase();
        let rows = document.querySelectorAll('#' + tablaId + ' tbody .filtro-fila');
        rows.forEach(row => {
            let textoFila = row.textContent.toLowerCase();
            row.style.display = textoFila.includes(input) ? '' : 'none';
        });
    }

    function formatFecha(f) {
        let parts = f.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function editarCarrera(id, nombre, tipo) {
        document.getElementById('formEditCarrera').action = `/asesor/carrera/${id}`;
        document.getElementById('edit_car_nombre').value = nombre;
        document.getElementById('edit_car_tipo').value = tipo;
        document.getElementById('modalEditCarrera').classList.remove('hidden');
    }

    function editarModulo(id, nombre, semestre, carrera_id, tipo_modulo) {
        document.getElementById('formEditModulo').action = `/asesor/modulo/${id}`;
        document.getElementById('edit_mod_nombre').value = nombre;
        document.getElementById('edit_mod_semestre').value = semestre;
        document.getElementById('edit_mod_carrera').value = carrera_id;
        document.getElementById('edit_mod_tipo').value = tipo_modulo;
        document.getElementById('modalEditModulo').classList.remove('hidden');
    }

    // ==========================================
    // ALERTAS DE ELIMINACIÓN
    // ==========================================
    function confirmarEliminarCarrera(id, nombre) {
        Swal.fire({
            title: '¿Eliminar Oferta Formativa?',
            text: `Está a punto de eliminar "${nombre}". Se borrarán también sus grupos y módulos asociados. Esta acción es irreversible.`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/asesor/carrera/${id}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmarEliminarGrupo(id, codigo) {
        Swal.fire({
            title: '¿Eliminar Grupo?',
            text: `Está a punto de eliminar el grupo "${codigo}". Se perderán las asignaciones y los estudiantes matriculados en él quedarán sin grupo. Esta acción es irreversible.`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/asesor/grupo/${id}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmarEliminarModulo(id, nombre) {
        Swal.fire({
            title: '¿Eliminar Módulo?',
            text: `Está a punto de eliminar el módulo "${nombre}". Esta acción es irreversible y podría afectar las asignaciones vinculadas a él.`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/asesor/modulo/${id}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // ==========================================
    // ALERTA Y FUNCIÓN PARA EDITAR GRUPOS
    // ==========================================
    function confirmarEdicionGrupo(id, codigo, modalidad, meta, anio, carrera_id) {
        Swal.fire({
            title: '¡Advertencia!',
            text: 'Modificar el código o configuración de un grupo es una acción irreversible y puede afectar la estructura de las asignaciones actuales. ¿Está seguro de continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, editar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                editarGrupo(id, codigo, modalidad, meta, anio, carrera_id);
            }
        });
    }

    function editarGrupo(id, codigo, modalidad, meta, anio, carrera_id) {
        document.getElementById('formEditGrupo').action = `/asesor/grupo/${id}`;
        document.getElementById('edit_gru_codigo').value = codigo;
        document.getElementById('edit_gru_modalidad').value = modalidad;
        document.getElementById('edit_gru_meta').value = meta;
        document.getElementById('edit_gru_anio').value = anio;
        document.getElementById('edit_gru_carrera').value = carrera_id;
        document.getElementById('modalEditGrupo').classList.remove('hidden');
    }

    function editarDocente(id, name, email, contrato, inicio, fin) {
        document.getElementById('formEditDocente').action = `/asesor/docente/${id}`;
        document.getElementById('edit_doc_name').value = name;
        document.getElementById('edit_doc_email').value = email;
        document.getElementById('edit_doc_contrato').value = contrato;
        document.getElementById('edit_doc_inicio').value = inicio;
        document.getElementById('edit_doc_fin').value = fin;
        document.getElementById('modalEditDocente').classList.remove('hidden');
    }

    function editarAsignacion(id, inicio, fin, estado) {
        document.getElementById('formEditAsignacion').action = `/asesor/asignacion/${id}`;
        document.getElementById('edit_asig_inicio').value = inicio;
        document.getElementById('edit_asig_fin').value = fin;
        document.getElementById('edit_asig_estado').value = estado;
        document.getElementById('modalEditAsignacion').classList.remove('hidden');
    }

    function abrirModalExportar(asignacionId) {
        document.getElementById('formExportarConsolidado').action = `/docente/exportar/${asignacionId}`;
        document.getElementById('formExportarSemaforo').action = `/docente/exportar/${asignacionId}`;
        
        let selectC = document.getElementById('select_fecha_corte_consolidado');
        let selectS = document.getElementById('select_fecha_corte_semaforo');
        selectC.innerHTML = ''; selectS.innerHTML = '';
        
        let fConsolidado = fechasConsolidadoMap[asignacionId] || [];
        let fSemaforo = fechasSemaforoMap[asignacionId] || [];
        
        if(fConsolidado.length > 0) {
            fConsolidado.forEach(f => selectC.innerHTML += `<option value="${f}">${formatFecha(f)}</option>`);
        } else {
            selectC.innerHTML = `<option value="">Sin registros manuales</option>`;
        }

        if(fSemaforo.length > 0) {
            fSemaforo.forEach(f => selectS.innerHTML += `<option value="${f}">${formatFecha(f)}</option>`);
        } else {
            selectS.innerHTML = `<option value="">Sin reportes de Moodle</option>`;
        }
        
        document.getElementById('modalExportar').classList.remove('hidden');
    }

    function filtrarModulosPorGrupo() {
        let grupoSelect = document.getElementById('grupo_select');
        let moduloSelect = document.getElementById('modulo_select');
        let options = moduloSelect.querySelectorAll('.modulo-option');
        
        let carreraId = grupoSelect.options[grupoSelect.selectedIndex].getAttribute('data-carrera');
        
        moduloSelect.value = ""; 
        
        if (!carreraId) {
            moduloSelect.disabled = true;
            options.forEach(opt => opt.classList.add('hidden'));
        } else {
            moduloSelect.disabled = false;
            let encontrados = false;
            
            options.forEach(opt => {
                if (opt.getAttribute('data-carrera') === carreraId) {
                    opt.classList.remove('hidden');
                    encontrados = true;
                } else {
                    opt.classList.add('hidden');
                }
            });
            
            if(!encontrados) {
                moduloSelect.disabled = true;
                moduloSelect.options[0].text = "Sin módulos registrados";
            } else {
                moduloSelect.options[0].text = "Seleccione Módulo";
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        let activeTab = localStorage.getItem('activeTabPanelAsesor') || 'tab-estructura';
        changeTab(activeTab);

        const chartColors = [
            '#2563eb', '#10b981', '#8b5cf6', '#f59e0b', '#f43f5e', '#0ea5e9', '#84cc16', '#d946ef',
        ];

        const ctxCarreras = document.getElementById('chartCarreras').getContext('2d');
        new Chart(ctxCarreras, {
            type: 'bar',
            data: {
                labels: @json($labelsCarreras),
                datasets: [{
                    label: 'Estudiantes Activos',
                    data: @json($dataEstudiantesCarrera),
                    backgroundColor: chartColors,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true }, x: { display: false } }
            }
        });

        const ctxModulos = document.getElementById('chartModulos').getContext('2d');
        new Chart(ctxModulos, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Finalizados'],
                datasets: [{
                    data: [{{ $modulosActivos }}, {{$modulosFinalizados }}],
                    backgroundColor: ['#10b981', '#9ca3af'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { position: 'right' } }
            }
        });
    });

    @if(session('alerta_recuperacion_id'))
        Swal.fire({
            title: 'Este docente ya estaba en el sistema...',
            text: 'El perfil de "{{ session('alerta_recuperacion_nombre') }}" está oculto. ¿Desea reactivar su cuenta?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/asesor/docente/{{ session("alerta_recuperacion_id") }}/toggle';
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            }
        });
    @endif
</script>
@endsection