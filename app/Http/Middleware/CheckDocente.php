<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDocente
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->rol !== 'docente') {
            abort(403);
        }
        return $next($request);
    }
}