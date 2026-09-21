@extends('layouts.app')

@section('title', 'Emisión de Reconocimientos')
@section('page_title', 'Emisión de Reconocimientos')

@section('content')
<div class="mb-8">
    <!-- Encabezado Principal -->
    <div class="bg-white rounded-2xl shadow-sm border-l-4 border-yellow-400 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800">Generación de Diplomas y Constancias</h2>
            <p class="text-sm font-medium mt-1">
                <span class="text-[#2a348e]">Módulo: {{ $asignacion->modulo->nombre }}</span> <span class="text-gray-400 mx-1">|</span> 
                <span class="text-gray-500">Grupo: {{ mb_strtoupper($asignacion->grupo->codigo_grupo, 'UTF-8') }}</span>
            </p>
        </div>
        <div>
            <!-- Botón de Generación Masiva -->
            <button onclick="mostrarAlertaDomPDF()" class="bg-[#2a348e] hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Generar Constancias del Grupo
            </button>
        </div>
    </div>
</div>

<!-- Cuadrícula de Protagonistas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($asignacion->grupo->estudiantes as $estudiante)
        @if($estudiante->trashed()) @continue @endif
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full hover:shadow-md transition-shadow">
            <div class="flex-grow flex flex-col items-center justify-center">
                <!-- Icono -->
                <div class="bg-yellow-50 p-4 rounded-full mb-4">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                
                <h3 class="text-center font-bold text-gray-800 text-lg leading-tight mb-2">{{ mb_convert_case($estudiante->name, MB_CASE_TITLE, "UTF-8") }}</h3>
                <p class="text-center text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-6 px-2">
                    {{ mb_strtoupper($estudiante->procedencia ?? 'INATEC', 'UTF-8') }}
                </p>
            </div>
            
            <!-- Botón Individual -->
            <button onclick="mostrarAlertaDomPDF()" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm">
                Generar Constancia
            </button>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function mostrarAlertaDomPDF() {
        Swal.fire({
            icon: 'info',
            title: 'Módulo en Desarrollo',
            text: 'El motor de generación PDF (DomPDF) será instalado en la siguiente fase de desarrollo.',
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-3xl',
                title: 'text-2xl font-bold text-gray-700',
                htmlContainer: 'text-gray-500 font-medium',
                confirmButton: 'px-8 py-2.5 rounded-lg font-bold shadow-sm'
            }
        });
    }
</script>
@endsection