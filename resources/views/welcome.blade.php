<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formación Profesional {{ $anioProyectado }} - INATEC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .tab-btn.active { border-bottom: 4px solid #3b82f6; color: #eff6ff; background-color: rgba(255,255,255,0.15); font-weight: 900; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    
    <header class="bg-[#2a348e] text-white pt-20 pb-16 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-black mb-6 tracking-tight">Oferta Formativa CNFD Olof Palme <span class="text-blue-300">{{ $anioProyectado }}</span></h1>
            <p class="text-lg md:text-xl text-blue-100 mb-10 leading-relaxed">
                Descubre una formación diseñada por y para docentes. Fortalece tus habilidades en el aula y domina nuevas herramientas tecnológicas para inspirar a tus estudiantes.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-10">
                <button onclick="switchTab('presencial')" id="btn-presencial" class="tab-btn active px-6 py-3 text-lg font-bold text-white bg-white/5 hover:bg-white/10 rounded-xl transition-all border-b-4 border-transparent">Modalidad Presencial</button>
                <button onclick="switchTab('virtual')" id="btn-virtual" class="tab-btn px-6 py-3 text-lg font-bold text-white bg-white/5 hover:bg-white/10 rounded-xl transition-all border-b-4 border-transparent">Modalidad Virtual</button>
            </div>
            <div>
                <a href="{{ route('login') }}" class="text-sm font-bold text-white hover:bg-white hover:text-[#2a348e] border border-white/40 px-8 py-3 rounded-lg inline-block transition-colors">
                    INICIO DE SESIÓN
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-12">
        
        <!-- SECCIÓN PRESENCIAL -->
        <div id="content-presencial" class="tab-content active">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-10 mb-12">
                <div class="flex flex-col md:flex-row gap-10 items-center">
                    <div class="flex-1">
                        <h2 class="text-4xl font-black text-[#2a348e] mb-6">Centros de Formación Presencial</h2>
                        <p class="text-gray-600 text-lg leading-relaxed">
                            Aprovecha nuestros laboratorios tecnológicos y espacios didácticos interactuando directamente con facilitadores y colegas. Nuestras instalaciones están listas para recibirte y enriquecer tu experiencia de aprendizaje.
                        </p>
                    </div>
                    <div class="flex-1 grid grid-cols-1 gap-5 w-full">
                        <!-- Mapa Olof Palme -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200 h-48 shadow-sm relative group">
                            <div class="absolute top-0 left-0 bg-[#2a348e] text-white text-xs font-black px-4 py-1.5 z-10 rounded-br-xl shadow-md">Centro Tecnológico Olof Palme, Estelí</div>
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3371.2300749493734!2d-86.29942992561845!3d13.000953987317125!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f7188562b756e6f%3A0x20f54c539e081213!2sCENTRO%20TECNOL%C3%93GICO%20OLOF%20PALME-ESTELI!5e1!3m2!1ses!2sni!4v1789696857356!5m2!1ses!2sni" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        </div>
                        <!-- Mapa Che Guevara -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200 h-48 shadow-sm relative group">
                            <div class="absolute top-0 left-0 bg-[#059669] text-white text-xs font-black px-4 py-1.5 z-10 rounded-br-xl shadow-md">Centro Tecnológico Che Guevara, Somoto</div>
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3364.5690723770463!2d-86.58713002560869!3d13.482511586881852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f71dfd6db8986ad%3A0x463698978fb5ee04!2sCentro%20Tecnol%C3%B3gico%20Che%20Guevara%2C%20Somoto.!5e1!3m2!1ses!2sni!4v1789696339494!5m2!1ses!2sni" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="text-2xl font-black text-gray-800 mb-6 px-2">Oferta Académica Presencial</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($carreras as $carrera)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-4">
                            <h4 class="text-2xl font-black text-[#2a348e] leading-tight pr-4">{{ $carrera->nombre }}</h4>
                            <span class="bg-[#f3e8ff] text-[#6b21a8] text-[10px] font-black px-3 py-1 rounded-md uppercase shrink-0">{{ $carrera->tipo }}</span>
                        </div>
                        
                        @if(count($carrera->modulos) > 0)
                            <div class="space-y-6">
                                @foreach($carrera->modulos->groupBy('semestre') as $semestre => $modulosSemestre)
                                    <div>
                                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ $semestre }}</h5>
                                        <ul class="space-y-3">
                                            @foreach($modulosSemestre as $modulo)
                                                <li class="flex justify-between items-center text-gray-700 font-medium text-sm py-1 border-b border-gray-50 last:border-0">
                                                    <span>{{ $modulo->nombre }}</span>
                                                    @php
                                                        $badgeColor = 'text-gray-500';
                                                        if($modulo->tipo_modulo == 'Técnicos') $badgeColor = 'text-blue-600';
                                                        if($modulo->tipo_modulo == 'Transversales') $badgeColor = 'text-emerald-600';
                                                        if($modulo->tipo_modulo == 'Optativos') $badgeColor = 'text-amber-600';
                                                    @endphp
                                                    <span class="text-[10px] font-bold uppercase shrink-0 ml-4 {{ $badgeColor }}">{{ $modulo->tipo_modulo }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">Módulos en proceso de actualización.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SECCIÓN VIRTUAL -->
        <div id="content-virtual" class="tab-content">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-10 mb-12">
                <div class="max-w-3xl">
                    <h2 class="text-4xl font-black text-[#2a348e] mb-6">Aprende a tu propio ritmo</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        La modalidad virtual está pensada para adaptarse a tu tiempo personal y laboral. Tendrás acceso constante a la plataforma con recursos interactivos y el apoyo continuo de un tutor que guiará tu aprendizaje paso a paso.
                    </p>
                </div>
            </div>

            <h3 class="text-2xl font-black text-gray-800 mb-6 px-2">Oferta Académica Virtual</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($carreras as $carrera)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-4">
                            <h4 class="text-2xl font-black text-[#2a348e] leading-tight pr-4">{{ $carrera->nombre }}</h4>
                            <span class="bg-[#f3e8ff] text-[#6b21a8] text-[10px] font-black px-3 py-1 rounded-md uppercase shrink-0">{{ $carrera->tipo }}</span>
                        </div>
                        
                        @if(count($carrera->modulos) > 0)
                            <div class="space-y-6">
                                @foreach($carrera->modulos->groupBy('semestre') as $semestre => $modulosSemestre)
                                    <div>
                                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ $semestre }}</h5>
                                        <ul class="space-y-3">
                                            @foreach($modulosSemestre as $modulo)
                                                <li class="flex justify-between items-center text-gray-700 font-medium text-sm py-1 border-b border-gray-50 last:border-0">
                                                    <span>{{ $modulo->nombre }}</span>
                                                    @php
                                                        $badgeColor = 'text-gray-500';
                                                        if($modulo->tipo_modulo == 'Técnicos') $badgeColor = 'text-blue-600';
                                                        if($modulo->tipo_modulo == 'Transversales') $badgeColor = 'text-emerald-600';
                                                        if($modulo->tipo_modulo == 'Optativos') $badgeColor = 'text-amber-600';
                                                    @endphp
                                                    <span class="text-[10px] font-bold uppercase shrink-0 ml-4 {{ $badgeColor }}">{{ $modulo->tipo_modulo }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">Módulos en proceso de actualización.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- FORMULARIO DE INTERESADOS -->
        <div class="mt-20 bg-gradient-to-br from-emerald-800 to-teal-900 rounded-3xl p-8 md:p-14 shadow-xl text-white max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h3 class="text-3xl font-black mb-3">Únete a nuestra formación en {{ $anioProyectado }}</h3>
                <p class="text-emerald-100 text-lg">Déjanos tus datos y un compañero de nuestro equipo se pondrá en contacto contigo para apoyarte en tu inscripción.</p>
            </div>
            <form action="{{ route('interesados.store') }}" method="POST" class="bg-white p-8 rounded-2xl text-gray-800 shadow-lg">
                @csrf
                <input type="hidden" name="anio_proyectado" value="{{ $anioProyectado }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre Completo</label>
                        <input type="text" name="nombre" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition-colors" placeholder="Escribe tu nombre">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Correo Electrónico</label>
                        <input type="email" name="correo" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition-colors" placeholder="correo@ejemplo.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition-colors" placeholder="Ej. 8888-8888">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Modalidad de Interés</label>
                        <select name="modalidad" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-gray-50 font-bold transition-colors">
                            <option value="Presencial">Presencial</option>
                            <option value="Virtual">Virtual</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Formación de tu Interés</label>
                        <select name="carrera_id" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-gray-50 font-bold text-emerald-900 transition-colors">
                            <option value="">-- Elige una opción --</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#2a348e] hover:bg-blue-800 text-white font-black text-lg py-4 rounded-xl shadow-md transition-colors">Enviar Solicitud</button>
            </form>
        </div>
    </main>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-white', 'text-white', 'bg-white/10');
                el.classList.add('border-transparent', 'text-blue-200', 'bg-white/5');
            });
            
            document.getElementById('content-' + tabId).classList.add('active');
            let btn = document.getElementById('btn-' + tabId);
            btn.classList.remove('border-transparent', 'text-blue-200', 'bg-white/5');
            btn.classList.add('border-white', 'text-white', 'bg-white/10');
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Solicitud Recibida!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#059669'
            });
        @endif
    </script>
</body>
</html>