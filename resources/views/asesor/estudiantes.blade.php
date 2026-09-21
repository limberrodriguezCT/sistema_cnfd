@extends('layouts.app')

@section('title', 'Gestor de Matrícula - CNFD')
@section('page_title', 'Gestión de Protagonistas')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-800">Gestión de Protagonistas</h2>
            <p class="text-gray-600 mt-1">Administre la matrícula, importaciones y procedencia de estudiantes por grupo.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
        <form action="{{ route('asesor.estudiantes') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-grow w-full">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Seleccione un Grupo</label>
                <select name="grupo_id" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-gray-50 font-bold" onchange="this.form.submit()">
                    <option value="">-- Seleccione un grupo para gestionar --</option>
                    @if(count($grupos) > 0)
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}" {{ $grupoActivo == $g->id ? 'selected' : '' }}>
                                {{ strtoupper($g->codigo_grupo) }} - {{ $g->carrera->nombre ?? 'Sin Carrera' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </form>
    </div>

    @if($grupoActivo)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-black text-gray-800 text-xl">Estudiantes Matriculados</h3>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <a href="{{ route('asesor.exportar_listado', $grupoActivo) }}" class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar Listado
                    </a>
                    <button onclick="document.getElementById('modalImportar').classList.remove('hidden')" class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Importar INATEC
                    </button>
                    <button onclick="document.getElementById('modalManual').classList.remove('hidden')" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Agregar Manual
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 uppercase text-xs tracking-wider">
                            <th class="py-3 px-4 font-bold">Protagonista</th>
                            <th class="py-3 px-4 font-bold">Correo</th>
                            <th class="py-3 px-4 font-bold">Procedencia</th>
                            <th class="py-3 px-4 font-bold text-center">Estado</th>
                            <th class="py-3 px-4 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($estudiantes) > 0)
                            @foreach($estudiantes as $est)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $est->trashed() ? 'opacity-50' : '' }}">
                                    <td class="py-4 px-4 font-bold text-gray-800">{{ $est->name }}</td>
                                    <td class="py-4 px-4 text-gray-600">{{ $est->email }}</td>
                                    <td class="py-4 px-4 text-gray-600 font-medium text-xs">{{ $est->procedencia }}</td>
                                    <td class="py-4 px-4 text-center">
                                        @if($est->trashed())
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded font-bold text-xs uppercase">Retirado</span>
                                        @else
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded font-bold text-xs uppercase">Activo</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center flex justify-center gap-2">
                                        <button onclick="editarEstudiante({{ $est->id }}, '{{ addslashes($est->name) }}', '{{ addslashes($est->email) }}', '{{ addslashes($est->procedencia) }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold py-1 px-3 rounded-lg text-xs transition-colors">Editar</button>
                                        <form action="{{ route('asesor.toggle_estudiante', $est->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="{{ $est->trashed() ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-red-50 text-red-600 hover:bg-red-100' }} font-bold py-1 px-3 rounded-lg text-xs transition-colors">
                                                {{ $est->trashed() ? 'Activar' : 'Retirar' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500 font-medium border-2 border-dashed rounded-xl">No hay protagonistas matriculados en este grupo.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm border-2 border-dashed border-gray-300">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <h3 class="text-xl font-bold text-gray-600">Seleccione un Grupo</h3>
            <p class="text-gray-500 mt-2 font-medium">Elija un grupo en la parte superior para administrar su matrícula.</p>
        </div>
    @endif

    <!-- MODAL IMPORTAR EXCEL CON EXPERIENCIA DE USUARIO (UX) MEJORADA -->
    <div id="modalImportar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
            <h3 class="font-black text-gray-800 mb-6 border-b pb-4 text-xl">Importar Matrícula INATEC</h3>
            <form action="{{ route('asesor.importar_estudiantes') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="grupo_id" value="{{ $grupoActivo }}">
                <label for="archivo_excel" id="dropzone_excel" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-emerald-50 hover:border-emerald-300 transition-colors mb-6 group">
                    <div id="upload_texto" class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <p class="text-sm text-gray-600 font-semibold">Cargar Excel (Formato Oficial)</p>
                    </div>
                    <input id="archivo_excel" name="archivo_excel" type="file" class="hidden" accept=".xlsx, .xls" required onchange="mostrarNombreArchivo(this)" />
                </label>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md">Procesar Excel</button>
                    <button type="button" onclick="cerrarModalImportar()" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL AGREGAR MANUAL -->
    <div id="modalManual" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Agregar Estudiante</h3>
            <form action="{{ route('asesor.store_estudiante') }}" method="POST">
                @csrf
                <input type="hidden" name="grupo_id" value="{{ $grupoActivo }}">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nombre Completo</label>
                    <input type="text" name="name" required class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Correo Electrónico</label>
                    <input type="email" name="email" required class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Contraseña (Por defecto)</label>
                    <input type="text" name="password" value="Inatec{{ date('y') }}*" required class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 font-mono">
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tipo de Procedencia</label>
                    <select name="tipo_procedencia" id="tipo_proc_add" onchange="toggleCamposProcedencia('add')" required class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                        <option value="INATEC">INATEC</option>
                        <option value="MINED">MINED</option>
                        <option value="SETEC">SETEC / Universidades</option>
                        <option value="OTRAS INSTITUCIONES">Otras Instituciones (ONGs, etc.)</option>
                        <option value="PÚBLICO GENERAL">Público General</option>
                    </select>
                </div>

                <!-- Campos dinámicos para INATEC -->
                <div id="campos_inatec_add" class="grid grid-cols-2 gap-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Departamento</label>
                        <select name="inatec_departamento" id="inatec_depto_add" onchange="cargarOpciones('add', 'INATEC')" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Centro Tecnológico</label>
                        <select name="inatec_centro" id="inatec_centro_add" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione un Dpto</option>
                        </select>
                    </div>
                </div>

                <!-- Campos dinámicos para MINED -->
                <div id="campos_mined_add" class="grid grid-cols-2 gap-3 mb-6 hidden">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Departamento</label>
                        <input type="text" name="mined_departamento" id="mined_depto_add" placeholder="Ej. Managua" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Colegio / Instituto</label>
                        <input type="text" name="mined_colegio" id="mined_colegio_add" placeholder="Ej. Ramírez Goyena" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                    </div>
                </div>

                <!-- Campos dinámicos para SETEC -->
                <div id="campos_setec_add" class="grid grid-cols-2 gap-3 mb-6 hidden">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Departamento</label>
                        <select name="setec_departamento" id="setec_depto_add" onchange="cargarOpciones('add', 'SETEC')" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Universidad</label>
                        <select name="setec_universidad" id="setec_univ_add" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione un Dpto</option>
                        </select>
                    </div>
                </div>

                <!-- Campos dinámicos para OTRAS INSTITUCIONES -->
                <div id="campos_otras_add" class="mb-6 hidden">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre de la Institución / ONG</label>
                    <input type="text" name="otra_institucion" id="otra_inst_add" placeholder="Ej. INPRHU" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md">Guardar</button>
                    <button type="button" onclick="document.getElementById('modalManual').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR ESTUDIANTE -->
    <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="font-black text-gray-800 mb-4 border-b pb-2 text-xl">Editar Estudiante</h3>
            <form id="formEditEstudiante" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nombre Completo</label>
                    <input type="text" name="name" id="edit_est_name" required class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Correo Electrónico</label>
                    <input type="email" name="email" id="edit_est_email" required class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nueva Contraseña (Opcional)</label>
                    <input type="text" name="password" placeholder="Dejar en blanco para no cambiar" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50 font-mono">
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tipo de Procedencia</label>
                    <select name="tipo_procedencia" id="tipo_proc_edit" onchange="toggleCamposProcedencia('edit')" required class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                        <option value="INATEC">INATEC</option>
                        <option value="MINED">MINED</option>
                        <option value="SETEC">SETEC / Universidades</option>
                        <option value="OTRAS INSTITUCIONES">Otras Instituciones (ONGs, etc.)</option>
                        <option value="PÚBLICO GENERAL">Público General</option>
                    </select>
                </div>

                <!-- Campos dinámicos para INATEC (Edit) -->
                <div id="campos_inatec_edit" class="grid grid-cols-2 gap-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Departamento</label>
                        <select name="inatec_departamento" id="inatec_depto_edit" onchange="cargarOpciones('edit', 'INATEC')" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Centro Tecnológico</label>
                        <select name="inatec_centro" id="inatec_centro_edit" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione un Dpto</option>
                        </select>
                    </div>
                </div>

                <!-- Campos dinámicos para MINED (Edit) -->
                <div id="campos_mined_edit" class="grid grid-cols-2 gap-3 mb-6 hidden">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Departamento</label>
                        <input type="text" name="mined_departamento" id="mined_depto_edit" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Colegio / Instituto</label>
                        <input type="text" name="mined_colegio" id="mined_colegio_edit" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                    </div>
                </div>

                <!-- Campos dinámicos para SETEC (Edit) -->
                <div id="campos_setec_edit" class="grid grid-cols-2 gap-3 mb-6 hidden">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Departamento</label>
                        <select name="setec_departamento" id="setec_depto_edit" onchange="cargarOpciones('edit', 'SETEC')" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Universidad</label>
                        <select name="setec_universidad" id="setec_univ_edit" class="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            <option value="">Seleccione un Dpto</option>
                        </select>
                    </div>
                </div>

                <!-- Campos dinámicos para OTRAS INSTITUCIONES (Edit) -->
                <div id="campos_otras_edit" class="mb-6 hidden">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre de la Institución / ONG</label>
                    <input type="text" name="otra_institucion" id="otra_inst_edit" class="w-full text-sm p-3 border border-gray-300 rounded-xl bg-gray-50">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md">Actualizar</button>
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 rounded-xl">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    const inatecData = {
        "Managua": ["Centro Tecnológico Simón Bolívar (y sub sede Muchachos, Horizonte Valiente)", "Centro Tecnológico de Idiomas “Héroes y Mártires de Batahola”", "Centro Tecnológico de Hotelería y Turismo “Amanda Aguilar”", "Centro Tecnológico Manuel Olivares Rodríguez (y sub sede Ariel Darce)", "Centro Tecnológico Padre Rafael María Fabretto", "Centro Tecnológico Acahualinca", "Centro Tecnológico Comandante Hugo Chávez Frías", "Centro Tecnológico Cultural y Politécnico José Coronel Urtecho", "Centro Nacional de Desarrollo del Talento Creativo Nieves Cajina", "Escuela Hotel Casa Luxemburgo (Pochomil)"],
        "Granada": ["Centro Tecnológico General Miguel Ángel Ortez", "Centro Tecnológico Bidkar Muñoz", "Centro Tecnológico Pedro Aráuz Palacios", "Centro Tecnológico Gral. José Dolores Estrada (Nandaime)"],
        "Rivas": ["Centro Tecnológico Gaspar García Laviana", "Escuela Hotel Volcán Maderas (Isla de Ometepe)", "Centro Tecnológico Álvaro Diroy Méndez (Ometepe)"],
        "León": ["Centro Tecnológico Leonel Rugama", "Centro Tecnológico Juan de Dios Muñoz Reyes", "Centro Tecnológico Arlen Siu (El Sauce)"],
        "Masaya": ["Centro Tecnológico Martha Isabel Navarro Mendoza", "Centro Tecnológico Comandante Camilo Ortega Saavedra", "Centro Tecnológico Monimbó Heroico"],
        "Estelí": ["Centro Tecnológico Francisco Rivera Quintero “El Zorro”", "Centro Tecnológico Olof Palme"],
        "Matagalpa": ["Centro Tecnológico Monseñor Benedicto Herrera", "Centro Tecnológico Santiago Baldovino (Muy Muy)", "Centro Tecnológico Comandante Tomás Borge Martínez (San Isidro)"],
        "Jinotega": ["Centro Tecnológico Marcos Homero Guatemala", "Centro Tecnológico Héroes y Mártires de Asturias"],
        "Chinandega": ["Centro Tecnológico Padre Teodoro Kint (El Viejo)", "Centro Tecnológico Héroes y Mártires de Villa Nueva", "Centro Tecnológico Rolando Rodríguez González (Chichigalpa)"],
        "Madriz": ["Centro Tecnológico Coronel Santos López (Somoto)", "Centro Tecnológico Che Guevara (Somoto y sub sede Palacagüina)", "Centro Tecnológico José Eulogio Hernández Alvarado (Las Sabanas)", "Centro Tecnológico Padre Rafael María Fabretto (San José de Cusmapa)"],
        "Nueva Segovia": ["Centro Tecnológico Coronel Antonio Rufo Marín (Ocotal)"],
        "Chontales": ["Centro Tecnológico Josefa Toledo de Aguerri (Juigalpa)", "Centro Tecnológico Comandante Germán Pomares Ordóñez (Juigalpa)"],
        "Boaco": ["Centro Tecnológico Alcides Miranda Fitoria"],
        "Carazo": ["Centro Tecnológico Ricardo Morales Avilés (Diriamba)", "Centro Tecnológico Ernst Thalmann (Jinotepe)"],
        "Río San Juan": ["Centro Tecnológico Juan María Brenes Roque (San Carlos)"],
        "RAAN (Caribe Norte)": ["Centro Tecnológico Héroes y Mártires de Puerto Cabezas", "Centro Tecnológico Onofre Martínez (Waspán)"],
        "RAAS (Caribe Sur)": ["Centro Tecnológico William Schwartz Cunningham (Bluefields)", "Centro Tecnológico Hugo Francisco Urbina Rodríguez (El Rama)", "Centro Tecnológico Gral. Augusto Nicolás Calderón Sandino (Nueva Guinea)"],
        "Siuna": ["Centro Tecnológico Bernardino Díaz Ochoa"]
    };

    const setecData = {
        "Managua": ["UNAN-Managua (RURD)", "UNAN-Managua (RUCFA)", "UNI (RUSB)", "UNI (RUPAP)", "UNA (Sede Central)", "UNHSJM (Sede Central)", "UNMRMA", "UNCPGGL", "UAM", "UCC", "UCN", "UAC", "UNICIT", "UNICA", "UdeM", "UTM", "UML", "UCYT", "UCEM", "UNITEC", "UNIVALLE", "UNIDES", "UNACAD", "UTC", "UMO-JN", "UCM", "ULAM", "UNIJJAR", "LAU", "American College"],
        "León": ["UNAN-León (Recinto Central)", "UNCPGGL (CUR-León)", "ULSA", "UCC"],
        "Estelí": ["UNAN-Managua (CUR-Estelí)", "UNI (Sede Regional UNI-Norte)", "UNHSJM (CUR-Estelí)", "UNFLEP", "UNCPGGL (CUR-Estelí)", "UNMRMA", "UCN", "UML"],
        "Chontales": ["UNAN-Managua (CUR-Chontales)", "UNI (Sede UNI-Juigalpa)", "UNA (Sede Universitaria Juigalpa)", "UNMRMA (CUR-Chontales)", "UNCPGGL (CUR-Juigalpa)"],
        "Boaco": ["UNA (Sede Universitaria Camoapa)", "UNHSJM (CUR-Boaco)"],
        "Carazo": ["UNAN-Managua (CUR-Carazo)", "UNMRMA (CUR-Carazo)", "UCN", "Keiser University"],
        "Rivas": ["UNIAV", "UNHSJM (CUR-Rivas)", "UNMRMA (CUR-Rivas)", "Universidad Anunciata"],
        "Chinandega": ["UNAN-León", "UNCPGGL (CUR-Chinandega)", "UNMRMA", "UACH", "UML"],
        "Matagalpa": ["UNAN-Managua (CUR-Matagalpa)", "UNCPGGL (CUR-Matagalpa)", "UNMRMA", "UCC"],
        "Nueva Segovia": ["UNCPGGL (CUR-Ocotal)", "UNAN-Managua (UNICAM)"],
        "Madriz": ["UNAN-León (CUR-Somoto)", "UNCPGGL", "UNAN-Managua (UNICAM)"],
        "Jinotega": ["UNAN-León (CUR-Jinotega)", "UNAN-Managua (UNICAM)", "UNMRMA"],
        "Granada": ["UNCPGGL (CUR-Granada)", "UNMRMA (CUR-Granada)"],
        "Masaya": ["UNCPGGL (CUR-Masaya)", "UNMRMA (CUR-Masaya)"],
        "Río San Juan": ["UNAN-León (Sede San Carlos)", "UNMRMA (CUR-Río San Juan)", "UNAN-Managua (UNICAM)"],
        "RACCN": ["URACCAN (Recinto Bilwi)", "URACCAN (Recinto Las Minas)", "BICU (Recinto Bilwi)"],
        "RACCS": ["BICU (Recinto Bluefields)", "BICU (CUR-El Rama)", "BICU (Laguna de Perlas)", "BICU (Corn Island)", "URACCAN"]
    };

    window.onload = function() {
        let deptoInatecAdd = document.getElementById('inatec_depto_add');
        let deptoInatecEdit = document.getElementById('inatec_depto_edit');
        let deptoSetecAdd = document.getElementById('setec_depto_add');
        let deptoSetecEdit = document.getElementById('setec_depto_edit');
        
        for (let depto in inatecData) {
            deptoInatecAdd.add(new Option(depto, depto));
            deptoInatecEdit.add(new Option(depto, depto));
        }
        
        for (let depto in setecData) {
            deptoSetecAdd.add(new Option(depto, depto));
            deptoSetecEdit.add(new Option(depto, depto));
        }
    };

    function cargarOpciones(modo, tipo) {
        let prefix = tipo === 'INATEC' ? 'inatec' : 'setec';
        let entidadSufijo = tipo === 'INATEC' ? 'centro' : 'univ';
        
        let depto = document.getElementById(prefix + '_depto_' + modo).value;
        let entidadSelect = document.getElementById(prefix + '_' + entidadSufijo + '_' + modo);
        
        entidadSelect.innerHTML = '<option value="">Seleccione...</option>';
        
        let data = tipo === 'INATEC' ? inatecData : setecData;
        
        if (depto && data[depto]) {
            data[depto].forEach(function(item) {
                entidadSelect.add(new Option(item, item));
            });
        }
    }

    function toggleCamposProcedencia(modo) {
        let select = document.getElementById('tipo_proc_' + modo).value;
        
        document.getElementById('campos_inatec_' + modo).classList.add('hidden');
        document.getElementById('campos_mined_' + modo).classList.add('hidden');
        document.getElementById('campos_setec_' + modo).classList.add('hidden');
        document.getElementById('campos_otras_' + modo).classList.add('hidden');
        
        if (select === 'INATEC') document.getElementById('campos_inatec_' + modo).classList.remove('hidden');
        if (select === 'MINED') document.getElementById('campos_mined_' + modo).classList.remove('hidden');
        if (select === 'SETEC') document.getElementById('campos_setec_' + modo).classList.remove('hidden');
        if (select === 'OTRAS INSTITUCIONES') document.getElementById('campos_otras_' + modo).classList.remove('hidden');
    }

    function editarEstudiante(id, name, email, procedenciaFull) {
        document.getElementById('edit_est_name').value = name;
        document.getElementById('edit_est_email').value = email;
        document.getElementById('formEditEstudiante').action = `/asesor/estudiante/${id}`;
        
        let tipoProc = 'PÚBLICO GENERAL';
        let depto = '';
        let entidad = '';
        
        if (procedenciaFull.startsWith('INATEC')) {
            tipoProc = 'INATEC';
            let parts = procedenciaFull.split(' - ');
            if (parts.length === 3) { depto = parts[1]; entidad = parts[2]; }
        } else if (procedenciaFull.startsWith('MINED')) {
            tipoProc = 'MINED';
            let parts = procedenciaFull.split(' - ');
            if (parts.length === 3) { depto = parts[1]; entidad = parts[2]; }
        } else if (procedenciaFull.startsWith('SETEC')) {
            tipoProc = 'SETEC';
            let parts = procedenciaFull.split(' - ');
            if (parts.length === 3) { depto = parts[1]; entidad = parts[2]; }
        } else if (procedenciaFull.startsWith('OTRAS INSTITUCIONES')) {
            tipoProc = 'OTRAS INSTITUCIONES';
            let parts = procedenciaFull.split(' - ');
            if (parts.length === 2) { entidad = parts[1]; }
        }
        
        document.getElementById('tipo_proc_edit').value = tipoProc;
        toggleCamposProcedencia('edit');
        
        if (tipoProc === 'INATEC') {
            document.getElementById('inatec_depto_edit').value = depto;
            cargarOpciones('edit', 'INATEC');
            setTimeout(() => { document.getElementById('inatec_centro_edit').value = entidad; }, 50);
        } else if (tipoProc === 'MINED') {
            document.getElementById('mined_depto_edit').value = depto;
            document.getElementById('mined_colegio_edit').value = entidad;
        } else if (tipoProc === 'SETEC') {
            document.getElementById('setec_depto_edit').value = depto;
            cargarOpciones('edit', 'SETEC');
            setTimeout(() => { document.getElementById('setec_univ_edit').value = entidad; }, 50);
        } else if (tipoProc === 'OTRAS INSTITUCIONES') {
            document.getElementById('otra_inst_edit').value = entidad;
        }
        
        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function mostrarNombreArchivo(input) {
        let label = document.getElementById('dropzone_excel');
        let texto = document.getElementById('upload_texto');
        
        if (input.files && input.files[0]) {
            let fileName = input.files[0].name;
            texto.innerHTML = `
                <svg class="w-10 h-10 mb-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-emerald-700 font-bold text-center px-4 truncate w-full" style="max-width: 250px;">${fileName}</p>
                <p class="text-xs text-emerald-600 mt-1 font-medium">¡Archivo adjuntado correctamente!</p>
            `;
            label.classList.add('border-emerald-400', 'bg-emerald-50');
            label.classList.remove('border-gray-300', 'bg-gray-50');
        }
    }

    function cerrarModalImportar() {
        document.getElementById('modalImportar').classList.add('hidden');
        document.getElementById('archivo_excel').value = "";
        document.getElementById('dropzone_excel').classList.remove('border-emerald-400', 'bg-emerald-50');
        document.getElementById('dropzone_excel').classList.add('border-gray-300', 'bg-gray-50');
        document.getElementById('upload_texto').innerHTML = `
            <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <p class="text-sm text-gray-600 font-semibold">Cargar Excel (Formato Oficial)</p>
        `;
    }
</script>
@endsection