<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - CNFD INATEC</title>
    <!-- Cargamos Tailwind directamente para garantizar que el diseño sea impecable -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased h-screen flex items-center justify-center relative overflow-hidden">
    
    <!-- Decoración de fondo institucional -->
    <div class="absolute top-0 left-0 w-full h-[45%] bg-[#2a348e] rounded-b-[100px] shadow-lg"></div>

    <!-- Contenedor principal del Login -->
    <div class="relative z-10 w-full max-w-md bg-white p-8 md:p-10 rounded-3xl shadow-2xl border border-gray-100 mx-4">
        
        <div class="text-center mb-10">
            <h2 class="text-4xl font-black text-[#2a348e] tracking-tight mb-1">INATEC</h2>
            <p class="text-gray-500 text-sm font-bold uppercase tracking-widest">Formación Docente</p>
        </div>

        <!-- Alertas de estado de sesión (ej. contraseña restablecida) -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm font-medium rounded-r-lg">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Correo Electrónico -->
            <div class="mb-6">
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Correo Institucional</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                    class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#2a348e] focus:border-[#2a348e] bg-gray-50 transition-colors font-medium text-gray-800" 
                    placeholder="ejemplo@tecnacional.edu.ni">
                @error('email')
                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-6">
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                    class="w-full text-sm p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#2a348e] focus:border-[#2a348e] bg-gray-50 transition-colors font-medium text-gray-800" 
                    placeholder="••••••••">
                @error('password')
                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recordarme y Olvidó Contraseña -->
            <div class="flex items-center justify-between mb-8">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#2a348e] shadow-sm focus:ring-[#2a348e]">
                    <span class="ml-2 text-sm text-gray-500 font-bold group-hover:text-gray-700 transition-colors">Recordar mis datos</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-[#2a348e] hover:text-blue-800 transition-colors" href="{{ route('password.request') }}">
                        ¿Olvidaste tu clave?
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-[#2a348e] hover:bg-blue-900 text-white font-black text-lg py-4 rounded-xl shadow-md transition-all transform hover:-translate-y-1">
                Iniciar Sesión
            </button>
        </form>
        
        <!-- Botón de regresar mejorado -->
        <div class="mt-8 border-t border-gray-100 pt-6 flex justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-[#2a348e] bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Regresar al Inicio
            </a>
        </div>
    </div>
</body>
</html>