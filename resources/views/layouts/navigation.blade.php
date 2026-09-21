@if(auth()->user()->rol === 'asesor')
    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Centro de Mando</p>
    <div class="space-y-2">
        <a href="{{ route('asesor.panel') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('asesor.panel') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Panel General
        </a>
        <a href="{{ route('asesor.estudiantes') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left mt-2 {{ request()->routeIs('asesor.estudiantes') ? 'bg-emerald-600 text-white shadow-md' : 'text-emerald-400 border border-slate-700 hover:bg-slate-800 hover:text-emerald-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Gestor de Matrícula
        </a>
        <!-- Submenú dinámico para el asesor que carga los botones de pestañas -->
        @yield('sub_menu_asesor')
    </div>

@elseif(auth()->user()->rol === 'docente')
    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Menú Formador</p>
    <div class="space-y-2">
        <a href="{{ route('docente.panel') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('docente.panel') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Mis Módulos
        </a>
        
        @if(isset($asignacion))
            <div class="pt-4 mt-4 border-t border-slate-800 space-y-2">
                <a href="{{ route('docente.espacio', $asignacion->id) }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('docente.espacio') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Gestión Moodle
                </a>
                <a href="{{ route('docente.asistencia', $asignacion->id) }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('docente.asistencia') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Asistencia Manual
                </a>
                <a href="{{ route('docente.reporte', $asignacion->id) }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('docente.reporte') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Semáforo e Historial
                </a>
                <a href="{{ route('docente.reconocimientos', $asignacion->id) }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('docente.reconocimientos') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Reconocimientos
                </a>
            </div>
        @endif

@elseif(auth()->user()->rol === 'estudiante')
    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Menú Estudiante</p>
    <div class="space-y-2">
        <a href="{{ route('estudiante.panel') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium text-left {{ request()->routeIs('estudiante.panel') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Mi Perfil y Avances
        </a>
    </div>
@endif