@extends('layouts.app')

@section('title', 'Control de Asistencia - CNFD')
@section('page_title', 'Registro y Apuntes')

@section('content')
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-8 border-l-4 border-l-emerald-600">
        <h2 class="text-2xl font-black text-gray-800">Asistencia y Seguimiento (Consolidado)</h2>
        <p class="text-gray-600 font-medium mt-1">Módulo: <span class="text-emerald-700 font-bold">{{ $asignacion->modulo->nombre }}</span> | Grupo: {{ $asignacion->grupo->codigo_grupo }}</p>
    </div>

    <form action="{{ route('docente.guardar_asistencia', $asignacion->id) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-1 bg-white p-5 rounded-xl shadow-md border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Fecha del Reporte</label>
                <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full text-sm p-3 border rounded-lg focus:ring-emerald-500 bg-gray-50">
            </div>
            <div class="lg:col-span-2 bg-emerald-50 p-5 rounded-xl shadow-md border border-emerald-200">
                <label class="block text-sm font-bold text-emerald-900 mb-2">Apunte Crudo General</label>
                <textarea name="observacion_general_cruda" rows="2" placeholder="Ej. 18 maestros activos..." class="w-full text-sm p-3 border border-emerald-300 rounded-lg focus:ring-emerald-500">{{ $asignacion->observacion_general }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border-b">
                            <th class="py-3 px-4 w-12 text-center">N°</th>
                            <th class="py-3 px-4">Protagonista</th>
                            <th class="py-3 px-4 w-40 text-center">Estado</th>
                            <th class="py-3 px-4">Apuntes Individuales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estudiantes as $index => $est)
                            @if(!$est->trashed())
                                @php
                                    $ultima = \App\Models\Asistencia::where('user_id', $est->id)->where('grupo_id', $asignacion->grupo_id)->latest('fecha')->first();
                                    $estadoActual = $ultima->estado ?? 'Ausente';
                                    if($estadoActual == 'Mejora') $estadoActual = 'Presente';
                                    $colorSelect = $estadoActual == 'Presente' ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50';
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 font-semibold text-gray-800">{{ $est->name }}</td>
                                    <td class="py-3 px-4">
                                        <select name="asistencias[{{ $est->id }}][estado]" onchange="this.className = this.value === 'Presente' ? 'w-full text-sm p-2 border rounded font-bold focus:ring-emerald-500 transition-colors text-green-700 bg-green-50' : 'w-full text-sm p-2 border rounded font-bold focus:ring-emerald-500 transition-colors text-red-700 bg-red-50'" class="w-full text-sm p-2 border rounded font-bold focus:ring-emerald-500 transition-colors {{ $colorSelect }}">
                                            <option value="Presente" {{ $estadoActual == 'Presente' ? 'selected' : '' }}>Presente</option>
                                            <option value="Ausente" {{ $estadoActual == 'Ausente' ? 'selected' : '' }}>Ausente</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="asistencias[{{ $est->id }}][observacion]" value="{{ $ultima->observacion ?? '' }}" class="w-full text-sm p-2 border rounded focus:ring-emerald-500 text-gray-700">
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg">Guardar Datos para la IA</button>
        </div>
    </form>
@endsection