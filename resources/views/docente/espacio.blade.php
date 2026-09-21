@extends('layouts.app')

@section('title', 'Espacio de Trabajo - CNFD')
@section('page_title', 'Flujo de Trabajo')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6 border-l-8 border-l-blue-600 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-5">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path></svg>
        </div>
        <div class="z-10">
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">{{ $asignacion->modulo->nombre }}</h2>
            <p class="text-gray-500 font-semibold mt-2 text-lg">Grupo <span class="text-blue-700 font-black bg-blue-50 px-2 py-1 rounded">{{ $asignacion->grupo->codigo_grupo }}</span></p>
        </div>
        <div class="z-10 bg-gray-50 p-4 rounded-xl border border-gray-200 text-center min-w-[200px]">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Período de Ejecución</p>
            <p class="text-lg font-black text-blue-900">{{ \Carbon\Carbon::parse($asignacion->fecha_inicio)->format('d/m/Y') }} <span class="text-gray-400 mx-1">al</span> {{ \Carbon\Carbon::parse($asignacion->fecha_fin)->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
            <div class="p-6 border-b border-gray-100 flex items-center gap-4 bg-gray-50">
                <div class="p-4 bg-blue-100 text-blue-700 rounded-xl">1</div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Reporte de Avances Moodle</h3>
                    <p class="text-sm text-gray-500 font-medium">Sincronización semanal</p>
                </div>
            </div>
            <form action="{{ route('docente.procesar', $asignacion->id) }}" method="POST" enctype="multipart/form-data" class="p-6 flex-grow flex flex-col justify-between">
                @csrf
                <label for="archivo_excel" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition-colors mb-6 group">
                    <div id="dropzone_content_avance" class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-sm text-gray-600 font-semibold">Subir archivo Excel o CSV de Campus</p>
                    </div>
                    <input id="archivo_excel" name="archivo_excel" type="file" class="hidden" accept=".xlsx, .xls, .csv" required />
                </label>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md text-lg">Procesar Avances y Semáforo</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
            <div class="p-6 border-b border-gray-100 flex items-center gap-4 bg-gray-50">
                <div class="p-4 bg-emerald-100 text-emerald-700 rounded-xl">2</div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Asistencia y Apuntes Manual</h3>
                    <p class="text-sm text-gray-500 font-medium">Evaluación manual</p>
                </div>
            </div>
            <div class="p-8 flex-grow flex flex-col justify-center items-center text-center">
                <div class="bg-emerald-50 rounded-full p-4 mb-4">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <p class="text-gray-600 mb-6 font-medium px-4">Acceda para escribir los apuntes manuales para el consolidado oficial.</p>
                <a href="{{ route('docente.asistencia', $asignacion->id) }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md text-lg">Ir al Registro Manual</a>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 rounded-2xl shadow-xl overflow-hidden text-white relative">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        <div class="absolute top-0 left-0 w-64 h-64 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        
        <div class="relative p-8 md:p-10 flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="lg:w-1/2">
                <span class="bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block shadow-sm">Fase Final</span>
                <h3 class="text-3xl font-black mb-3 text-white">Consolidado y Motor IA</h3>
                <p class="text-slate-300 font-medium text-lg mb-6 leading-relaxed">Convierta sus apuntes en redacciones institucionales impecables y descargue los formatos oficiales de control.</p>
            </div>
            
            <div class="lg:w-1/2 w-full flex flex-col gap-4">
                <form action="{{ route('docente.generar_ia', $asignacion->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" onclick="Swal.fire({title: 'Generando Redacciones...', text: 'Gemini está puliendo y formalizando sus apuntes. Por favor espere.', allowOutsideClick: false, didOpen: () => {Swal.showLoading()}})" class="w-full group flex items-center justify-center gap-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-black py-4 px-6 rounded-xl transition-all shadow-lg border border-indigo-400 hover:border-indigo-300">
                        <svg class="w-6 h-6 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        3. Autocompletar con IA Gemini
                    </button>
                </form>

                <button type="button" onclick="document.getElementById('modalExportar').classList.remove('hidden')" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-50 text-slate-900 font-black py-4 px-6 rounded-xl transition-colors shadow-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    4. Descargar Reportes y Matrices
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DE EXPORTACIÓN DOBLE -->
    <div id="modalExportar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-3xl">
            <h3 class="font-black text-gray-800 mb-6 border-b pb-4 text-2xl text-center">Centro de Descargas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjeta Consolidado -->
                <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-200 flex flex-col justify-between">
                    <div>
                        <h4 class="font-black text-emerald-800 mb-1 text-lg">Consolidado INATEC</h4>
                        <p class="text-xs text-emerald-600 mb-4 font-medium">Basado en su asistencia manual (Presente/Ausente).</p>
                    </div>
                    <form method="GET" action="{{ route('docente.exportar', $asignacion->id) }}">
                        <input type="hidden" name="tipo" value="consolidado">
                        <label class="block text-xs font-bold text-emerald-700 uppercase mb-2">Fecha de Corte</label>
                        <select name="fecha_corte" class="w-full text-sm p-3 border border-emerald-300 rounded-lg mb-4 bg-white font-bold focus:ring-emerald-500">
                            @forelse($fechasConsolidado as $fC) <option value="{{ $fC }}">{{ \Carbon\Carbon::parse($fC)->format('d/m/Y') }}</option> @empty <option value="{{ date('Y-m-d') }}">{{ date('d/m/Y') }} (Hoy - Sin registros)</option> @endforelse
                        </select>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 rounded-lg shadow-sm">Descargar Consolidado Oficial</button>
                    </form>
                </div>
                
                <!-- Tarjeta Semáforo -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-200 flex flex-col justify-between">
                    <div>
                        <h4 class="font-black text-blue-800 mb-1 text-lg">Matriz de Semáforo</h4>
                        <p class="text-xs text-blue-600 mb-4 font-medium">Columnas de actividades automáticas desde Campus.</p>
                    </div>
                    <form method="GET" action="{{ route('docente.exportar', $asignacion->id) }}">
                        <input type="hidden" name="tipo" value="semaforo">
                        <label class="block text-xs font-bold text-blue-700 uppercase mb-2">Fecha del Reporte Moodle</label>
                        <select name="fecha_corte" class="w-full text-sm p-3 border border-blue-300 rounded-lg mb-4 bg-white font-bold focus:ring-blue-500">
                            @forelse($fechasSemaforo as $fS) <option value="{{ $fS }}">{{ \Carbon\Carbon::parse($fS)->format('d/m/Y') }}</option> @empty <option value="{{ date('Y-m-d') }}">{{ date('d/m/Y') }} (Hoy - Sin registros)</option> @endforelse
                        </select>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-lg shadow-sm">Descargar Semáforo</button>
                    </form>
                </div>
            </div>
            
            <div class="mt-6 text-center border-t pt-4">
                <button type="button" onclick="document.getElementById('modalExportar').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-8 rounded-lg transition-colors">Cerrar Ventana</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('archivo_excel').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                document.getElementById('dropzone_content_avance').innerHTML = `<p class="text-sm text-green-700 font-bold text-center px-4">${e.target.files[0].name} cargado</p>`;
            }
        });
    </script>
@endsection