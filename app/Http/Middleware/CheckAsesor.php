<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAsesor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->rol !== 'asesor') {
            abort(403);
        }
        return $next($request);
    }
}