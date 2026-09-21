<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CNFD Olof Palme')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        @yield('styles')
    </style>
</head>
<body class="bg-gray-50 text-gray-800 h-screen flex overflow-hidden">
    
    <!-- SIDEBAR LATERAL OSCURO -->
    <aside class="w-72 bg-slate-900 text-slate-300 flex flex-col transition-all hidden md:flex z-20">
        <div class="h-20 flex items-center justify-center border-b border-slate-800 px-4 bg-slate-950">
            <img src="{{ asset('img/logo-inatec-blanco.png') }}" alt="INATEC" class="h-10">
        </div>
        
        <div class="p-6 flex-grow overflow-y-auto">
            <!-- NAVEGACIÓN DINÁMICA POR ROLES -->
            @include('layouts.navigation')
        </div>
        
        <div class="p-4 border-t border-slate-800 bg-slate-950">
            <p class="text-xs text-center text-slate-500 font-semibold">{{ auth()->user()->rol === 'asesor' ? 'Asesor Pedagógico' : 'CNFD Olof Palme' }}</p>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 border-b border-gray-200 z-10">
            <h1 class="text-xl font-black text-gray-800 hidden sm:block">@yield('page_title', 'Panel de Control')</h1>
            <div class="flex items-center gap-4 ml-auto">
                <span class="font-bold text-sm text-gray-700 bg-gray-100 px-4 py-2 rounded-full border border-gray-300 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 font-bold py-2 px-4 rounded-lg transition-colors shadow-sm text-sm">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
            <!-- AQUÍ SE INYECTAN LAS VISTAS -->
            @yield('content')
        </main>
    </div>

    <script>
        let isNavigationBack = false;
        if (window.performance && window.performance.getEntriesByType("navigation").length > 0) {
            if(window.performance.getEntriesByType("navigation")[0].type === 'back_forward') { isNavigationBack = true; }
        }
        if (!isNavigationBack) {
            @if (session('success')) Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session('success') }}', confirmButtonColor: '#2563eb' }); @endif
            @if ($errors->any()) Swal.fire({ icon: 'error', title: 'Error del Sistema', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#2563eb' }); @endif
        }
    </script>
    @yield('scripts')
</body>
</html>