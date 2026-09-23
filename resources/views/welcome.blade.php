<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formación Profesional <?php echo $anioProyectado ?? date('Y'); ?> - INATEC</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Swiper CSS para el carrusel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <style>
        .swiper {
            width: 100%;
            padding-top: 20px;
            padding-bottom: 50px;
        }
        .swiper-slide {
            background-position: center;
            background-size: cover;
            width: 300px;
            height: 300px;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .swiper-slide:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .swiper-slide-active {
            border: 4px solid #3b82f6;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-x-hidden">
    
    <!-- HEADER -->
    <header class="bg-[#2a348e] text-white pt-24 pb-20 px-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                <polygon points="0,100 100,0 100,100"></polygon>
            </svg>
        </div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <span class="bg-blue-600/30 text-blue-200 border border-blue-400/30 px-4 py-1.5 rounded-full text-sm font-black tracking-widest uppercase mb-6 inline-block">Convocatoria Abierta</span>
            <h1 class="text-4xl md:text-6xl font-black mb-6 tracking-tight leading-tight">Centro Nacional de Formación Docente Olof Palme <span class="text-blue-300"><?php echo $anioProyectado ?? date('Y'); ?></span></h1>
            <p class="text-blue-100 text-lg md:text-xl mb-10 max-w-2xl mx-auto">Impulsa tu carrera docente con nuestras especialidades técnicas diseñadas para la excelencia educativa.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#formulario-interesados" class="bg-white text-[#2a348e] hover:bg-gray-100 font-black px-8 py-4 rounded-xl shadow-lg transition-transform transform hover:-translate-y-1">Matriculate</a>
                <a href="#oferta-formativa" class="text-white hover:bg-white/10 border-2 border-white/40 font-black px-8 py-4 rounded-xl transition-colors backdrop-blur-sm">Ver Oferta Formativa</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-16 space-y-24">
        
        <!-- SECCIÓN 1: OFERTA FORMATIVA Y CARRUSEL -->
        <section id="oferta-formativa" class="scroll-mt-10">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-black text-[#2a348e]">Nuestras Especialidades</h2>
                <p class="text-gray-500 mt-3 text-lg">Desliza o haz clic en las imágenes para descubrir el perfil de cada carrera.</p>
            </div>

            <!-- Carrusel Swiper -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1: Docencia -->
                    <div class="swiper-slide bg-gray-200 relative group" data-id="docencia">
                        <img src="/img/1.jpg" alt="Técnico Especialista en Docencia" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400/2a348e/ffffff?text=Docencia+Técnica'">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#2a348e]/90 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                            <span class="text-white font-black text-lg leading-tight">Docencia de Educación Técnica</span>
                        </div>
                    </div>
                    <!-- Slide 2: Tecnología Educativa -->
                    <div class="swiper-slide bg-gray-200 relative group" data-id="tecnologia">
                        <img src="/img/7.jpg" alt="Tecnología Educativa" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400/059669/ffffff?text=Tecnología+Educativa'">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#059669]/90 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                            <span class="text-white font-black text-lg leading-tight">Tecnología Educativa</span>
                        </div>
                    </div>
                    <!-- Slide 3: Inglés -->
                    <div class="swiper-slide bg-gray-200 relative group" data-id="ingles">
                        <img src="/img/4.jpg" alt="Didáctica del Idioma Inglés" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400/9333ea/ffffff?text=Didáctica+Inglés'">
                        <div class="absolute inset-0 bg-gradient-to-t from-purple-900/90 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                            <span class="text-white font-black text-lg leading-tight">Didáctica del Idioma Inglés</span>
                        </div>
                    </div>
                    <!-- Slide 4: Facilitador -->
                    <div class="swiper-slide bg-gray-200 relative group" data-id="facilitador">
                        <img src="/img/8.jpg" alt="Facilitador de la Formación Profesional" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400/ea580c/ffffff?text=Facilitador+Profesional'">
                        <div class="absolute inset-0 bg-gradient-to-t from-orange-600/90 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                            <span class="text-white font-black text-lg leading-tight">Facilitador de Formación</span>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>

            <!-- Panel de Detalles Dinámicos (UI/UX) -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transform transition-all mt-4" id="detalles-oferta">
                <div class="bg-gray-50 border-b border-gray-100 p-6 md:p-8">
                    <span id="badge-tipo" class="bg-blue-100 text-blue-800 text-[10px] font-black px-3 py-1 rounded-md uppercase mb-3 inline-block tracking-widest">CARRERA</span>
                    <h3 id="titulo-oferta" class="text-2xl md:text-4xl font-black text-[#2a348e] leading-tight">Técnico Especialista en Docencia de Educación Técnica y Formación Profesional</h3>
                </div>
                
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Perfil Profesional -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-emerald-100 p-2.5 rounded-xl text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <h4 class="text-xl font-black text-gray-800">Perfil profesional</h4>
                        </div>
                        <p class="text-gray-500 mb-4 font-medium italic text-sm">El/la egresado/a de esta especialidad será capaz de:</p>
                        <ul id="lista-perfil" class="space-y-3 text-gray-700 font-medium">
                            <!-- Inyectado por JS -->
                        </ul>
                    </div>

                    <!-- Cargo u Ocupación -->
                    <div class="bg-gray-50 rounded-2xl p-6 md:p-8 border border-gray-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-[#2a348e]/10 p-2.5 rounded-xl text-[#2a348e]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h4 class="text-xl font-black text-gray-800">Cargo u ocupación</h4>
                        </div>
                        <ul id="lista-cargos" class="space-y-4 text-gray-700 font-bold">
                            <!-- Inyectado por JS -->
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 2: CENTROS DE FORMACIÓN (MAPAS) -->
        <section id="centros-presenciales" class="scroll-mt-10">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-black text-[#2a348e]">Centros de Formación Presencial</h2>
                <p class="text-gray-500 mt-3 text-lg">Visítanos y conoce nuestras instalaciones equipadas para tu aprendizaje.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Mapa Olof Palme -->
                <div class="bg-white rounded-3xl p-4 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow group relative">
                    <div class="absolute top-8 left-8 bg-[#2a348e] text-white text-xs font-black px-4 py-2 z-10 rounded-xl shadow-lg transform group-hover:-translate-y-1 transition-transform">Centro Tecnológico Olof Palme, Estelí</div>
                    <div class="rounded-2xl overflow-hidden h-80 relative">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3371.2300749493734!2d-86.29942992561845!3d13.000953987317125!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f7188562b756e6f%3A0x20f54c539e081213!2sCENTRO%20TECNOL%C3%93GICO%20OLOF%20PALME-ESTELI!5e1!3m2!1ses!2sni!4v1789696857356!5m2!1ses!2sni" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <!-- Mapa Che Guevara -->
                <div class="bg-white rounded-3xl p-4 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow group relative">
                    <div class="absolute top-8 left-8 bg-[#059669] text-white text-xs font-black px-4 py-2 z-10 rounded-xl shadow-lg transform group-hover:-translate-y-1 transition-transform">Centro Tecnológico Che Guevara, Somoto</div>
                    <div class="rounded-2xl overflow-hidden h-80 relative">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3364.5690723770463!2d-86.58713002560869!3d13.482511586881852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f71dfd6db8986ad%3A0x463698978fb5ee04!2sCentro%20Tecnol%C3%B3gico%20Che%20Guevara%2C%20Somoto.!5e1!3m2!1ses!2sni!4v1789696339494!5m2!1ses!2sni" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 3: FORMULARIO DE INTERESADOS -->
        <section id="formulario-interesados" class="scroll-mt-10">
            <div class="bg-gradient-to-br from-emerald-800 to-teal-900 rounded-3xl p-8 md:p-14 shadow-2xl text-white relative overflow-hidden">
                <!-- Decoración SVG -->
                <svg class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3 w-96 h-96 opacity-10 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                
                <div class="text-center mb-10 relative z-10">
                    <h3 class="text-3xl md:text-4xl font-black mb-4">Únete a la Formación Docente <?php echo $anioProyectado ?? date('Y'); ?></h3>
                    <p class="text-emerald-100 text-lg md:text-xl max-w-2xl mx-auto">Déjanos tus datos y nos pondremos en contacto contigo para apoyarte en tu proceso de matrícula.</p>
                </div>
                
                <form action="{{ route('interesados.store') }}" method="POST" class="bg-white p-8 md:p-10 rounded-2xl text-gray-800 shadow-xl relative z-10 max-w-4xl mx-auto">
                    @csrf
                    <input type="hidden" name="anio_proyectado" value="<?php echo $anioProyectado ?? date('Y'); ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nombre Completo</label>
                            <input type="text" name="nombre" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 transition-colors" placeholder="Escribe tu nombre">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Correo Electrónico</label>
                            <input type="email" name="correo" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 transition-colors" placeholder="correo@ejemplo.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 transition-colors" placeholder="Ej. 8888-8888">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Modalidad de Interés</label>
                            <select name="modalidad" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 font-bold transition-colors">
                                <option value="Presencial">Presencial</option>
                                <option value="Virtual">Virtual</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Formación de tu Interés</label>
                            <select name="carrera_id" required class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 font-bold text-[#2a348e] transition-colors cursor-pointer">
                                <option value="">-- Selecciona una especialidad --</option>
                                <?php if(isset($carreras) && count($carreras) > 0): ?>
                                    <?php foreach($carreras as$c): ?>
                                        <option value="<?php echo $c->id; ?>"><?php echo mb_strtoupper($c->nombre, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Técnico Especialista en Docencia de Educación Técnica</option>
                                    <option value="2">Técnico Especialista en Tecnología Educativa</option>
                                    <option value="3">Técnico Especialista en Didáctica del Idioma Inglés</option>
                                    <option value="4">Facilitador de la Formación Profesional</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#2a348e] hover:bg-blue-800 text-white font-black text-lg py-5 rounded-xl shadow-md transition-all transform hover:-translate-y-1">Enviar Solicitud de Matrícula</button>
                </form>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8 text-center text-sm font-medium">
        <p>
            <a href="{{ route('login') }}" class="cursor-default focus:outline-none">&copy; <?php echo date('Y'); ?></a> 
            INATEC - Centro Nacional de Formación Docente Olof Palme.
        </p>
    </footer>

    <!-- Scripts de Swiper JS y Lógica Dinámica -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Datos duros de las carreras basados en los catálogos y perfiles
        const infoCarreras = {
            'docencia': {
                tipo: 'CARRERA',
                titulo: 'Técnico Especialista en Docencia de Educación Técnica y Formación Profesional',
                perfil: [
                    'Planificar procesos de aprendizaje de la acción formativa.',
                    'Implementar sesiones formativas de aprendizaje en Educación Técnica y Formación Profesional.',
                    'Evaluar competencia asociada a la acción formativa.',
                    'Gestionar entornos virtuales de aprendizaje.',
                    'Gestionar las prácticas profesionales de los estudiantes de educación técnica.',
                    'Realizar investigaciones en entornos educativos.',
                    'Propiciar emprendimientos en el contexto educativo.',
                    'Educar con valores éticos.'
                ],
                cargos: [
                    'Docente de Educación Técnica y Formación Profesional.',
                    'Facilitador de Formación Profesional.'
                ]
            },
            'tecnologia': {
                tipo: 'CARRERA',
                titulo: 'Técnico Especialista en Tecnología Educativa',
                perfil: [
                    'Efectuar procesos de aprendizaje y gestión de proyectos educativos, optimizando recursos educativos y promoviendo la educación virtual. Esto se realiza mediante la implementación de metodologías curriculares, didácticas y pedagógicas, integrando Tecnologías de la Información y la Comunicación (TIC) y tecnologías emergentes para garantizar una formación de calidad e innovación en los entornos educativos.'
                ],
                cargos: [
                    'Asesor Técnico en Tecnología Educativa',
                    'Coordinador de Proyectos en Tecnología Educativa',
                    'Docente TIC',
                    'Diseñador de Recursos Educativos Digitales',
                    'Administrador de Entornos Virtuales de Aprendizaje'
                ]
            },
            'ingles': {
                tipo: 'CARRERA',
                titulo: 'Técnico Especialista en Didáctica del Idioma Inglés',
                perfil: [
                    'Efectuar la planificación, desarrollo, manejo del aula de clase y evaluación de los aprendizajes del idioma ingles según el modelo basado en Competencias y el Marco común Europeo de Referencia para las lenguas.'
                ],
                cargos: [
                    'Docentes de enseñanza en el idioma inglés.'
                ]
            },
            'facilitador': {
                tipo: 'CURSO',
                titulo: 'Facilitador de la Formación Profesional',
                perfil: [
                    'Facilitar y mediar el proceso de enseñanza y aprendizaje para la formación y capacitación técnica.',
                    'Aplicar estrategias didácticas y metodologías activas y participativas.',
                    'Diseñar instrumentos de evaluación por competencias.'
                ],
                cargos: [
                    'Facilitador de Formación Profesional.',
                    'Instructor Técnico.'
                ]
            }
        };

        function actualizarDetalles(key) {
            const data = infoCarreras[key];
            if(!data) return;

            const panel = document.getElementById('detalles-oferta');
            
            panel.classList.remove('fade-in');
            void panel.offsetWidth;
            panel.classList.add('fade-in');

            document.getElementById('badge-tipo').textContent = data.tipo;
            if(data.tipo === 'CURSO') {
                document.getElementById('badge-tipo').className = 'bg-purple-100 text-purple-800 text-[10px] font-black px-3 py-1 rounded-md uppercase mb-3 inline-block tracking-widest';
            } else {
                document.getElementById('badge-tipo').className = 'bg-blue-100 text-blue-800 text-[10px] font-black px-3 py-1 rounded-md uppercase mb-3 inline-block tracking-widest';
            }

            document.getElementById('titulo-oferta').textContent = data.titulo;

            const listaPerfil = document.getElementById('lista-perfil');
            listaPerfil.innerHTML = '';
            data.perfil.forEach(item => {
                const li = document.createElement('li');
                li.className = 'flex items-start gap-3';
                li.innerHTML = `<svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> <span>${item}</span>`;
                listaPerfil.appendChild(li);
            });

            const listaCargos = document.getElementById('lista-cargos');
            listaCargos.innerHTML = '';
            data.cargos.forEach(item => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-3 bg-white px-4 py-3 rounded-xl border border-gray-200 shadow-sm';
                li.innerHTML = `<span class="w-2 h-2 rounded-full bg-[#2a348e] shrink-0"></span> <span>${item}</span>`;
                listaCargos.appendChild(li);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            var swiper = new Swiper(".mySwiper", {
                effect: "coverflow",
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: "auto",
                coverflowEffect: {
                    rotate: 20,
                    stretch: 0,
                    depth: 200,
                    modifier: 1,
                    slideShadows: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                initialSlide: 1, 
                on: {
                    init: function () {
                        const activeSlide = this.slides[this.activeIndex];
                        actualizarDetalles(activeSlide.getAttribute('data-id'));
                    },
                    slideChange: function () {
                        const activeSlide = this.slides[this.activeIndex];
                        actualizarDetalles(activeSlide.getAttribute('data-id'));
                    }
                }
            });

            swiper.slides.forEach((slide, index) => {
                slide.addEventListener('click', () => {
                    swiper.slideTo(index);
                });
            });
        });

        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Solicitud Recibida!',
                text: '<?php echo session('success'); ?>',
                confirmButtonColor: '#059669',
                confirmButtonText: 'Excelente'
            });
        <?php endif; ?>
    </script>
</body>
</html>