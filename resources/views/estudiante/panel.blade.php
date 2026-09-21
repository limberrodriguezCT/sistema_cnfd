@extends('layouts.app')

@section('title', 'Mi Perfil - CNFD')
@section('page_title', 'Mi Perfil y Avances')

@section('sidebar_menu')
    <a href="{{ route('estudiante.panel') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-all font-medium text-left bg-blue-600 text-white shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        Mi Perfil y Avances
    </a>
@endsection

@section('content')
    <!-- TARJETA DE PERFIL (LA "LOCURA" ELEGANTE) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8 relative">
        <div class="h-32 bg-gradient-to-r from-blue-700 to-indigo-800"></div>
        <div class="px-8 pb-8 relative">
            <!-- Avatar flotante -->
            <div class="absolute -top-12 left-8 bg-white p-2 rounded-2xl shadow-lg border border-gray-100">
                <div class="w-24 h-24 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>
            
            <div class="pt-16 md:pt-4 md:pl-36 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tight">{{ $estudiante->name }}</h2>
                    <p class="text-blue-600 font-bold mt-1">{{ $estudiante->email }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-200 px-4 py-2 rounded-xl text-center shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estado</p>
                    <p class="text-sm font-black text-emerald-600 flex items-center gap-1 justify-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Estudiante Activo
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-8 border-t border-gray-100">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Carrera Técnica</p>
                    <p class="font-bold text-gray-800">{{ $estudiante->grupo->carrera->nombre ?? 'Sin Carrera Asignada' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Grupo y Modalidad</p>
                    <p class="font-bold text-gray-800">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mr-2">{{ $estudiante->grupo->codigo_grupo ?? 'N/A' }}</span> 
                        {{ $estudiante->grupo->modalidad ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Institución de Procedencia</p>
                    <p class="font-bold text-gray-800">{{ $estudiante->procedencia ?? 'Público General' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MIS MÓDULOS Y RUTAS DE APRENDIZAJE -->
    <div class="mb-6 flex items-center gap-3">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        <h3 class="text-2xl font-black text-gray-800">Ruta de Módulos Formativos</h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($asignaciones as $asignacion)
            @php
                $inicio = \Carbon\Carbon::parse($asignacion->fecha_inicio);
                $fin = \Carbon\Carbon::parse($asignacion->fecha_fin);
                $hoy = now();
                
                $totalDias = $inicio->diffInDays($fin) > 0 ? $inicio->diffInDays($fin) : 1;
                $diasPasados = $inicio->diffInDays($hoy, false);
                
                // Cálculo de la barra de progreso de tiempo
                $progresoTiempo = 0;
                $estadoColor = 'bg-blue-500';
                $estadoTexto = 'Próximamente';
                $badgeColor = 'bg-gray-100 text-gray-600';

                if ($asignacion->estado == 'finalizado' || $hoy > $fin) {
                    $progresoTiempo = 100;
                    $estadoColor = 'bg-emerald-500';
                    $estadoTexto = 'Módulo Finalizado';
                    $badgeColor = 'bg-emerald-100 text-emerald-700';
                } elseif ($hoy >= $inicio && $hoy <= $fin) {
                    $progresoTiempo = min(100, max(0, ($diasPasados / $totalDias) * 100));
                    $estadoTexto = 'En Progreso';
                    $badgeColor = 'bg-blue-100 text-blue-700';
                    // Si falta poco tiempo, cambiar a naranja
                    if ($progresoTiempo > 85) $estadoColor = 'bg-orange-500';
                }
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
                @if($estadoTexto == 'En Progreso')
                    <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden">
                        <div class="bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider text-center py-1 absolute transform rotate-45 top-3 -right-6 w-24 shadow-sm">Activo</div>
                    </div>
                @endif

                <div>
                    <div class="flex justify-between items-start mb-4 pr-6">
                        <h4 class="font-black text-xl text-gray-800 leading-tight">{{ $asignacion->modulo->nombre }}</h4>
                    </div>
                    
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-xs font-bold px-2 py-1 rounded {{ $badgeColor }} uppercase tracking-wider">{{ $estadoTexto }}</span>
                        <span class="text-sm font-semibold text-gray-500">Docente: {{ $asignacion->docente->name ?? 'N/A' }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div>
                            <p class="text-gray-400 font-bold text-xs uppercase mb-1">Apertura</p>
                            <p class="font-semibold text-gray-700">{{ $inicio->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 font-bold text-xs uppercase mb-1">Cierre</p>
                            <p class="font-semibold text-gray-700">{{ $fin->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Barra de Progreso de Tiempo -->
                <div class="mt-2">
                    <div class="flex justify-between text-xs font-bold text-gray-500 mb-1">
                        <span>Progreso de Tiempo</span>
                        <span>{{ round($progresoTiempo) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="{{ $estadoColor }} h-2 rounded-full transition-all duration-1000" style="width: {{ $progresoTiempo }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-2xl shadow-sm border-2 border-dashed border-gray-300">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <h3 class="text-xl font-bold text-gray-600">Aún no tienes módulos asignados</h3>
                <p class="text-gray-500 mt-2 font-medium">Tus asesores están preparando tu ruta de aprendizaje.</p>
            </div>
        @endforelse
    </div>
@endsection