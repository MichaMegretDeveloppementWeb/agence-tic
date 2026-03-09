<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsDirectorG
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isDirectorG()) {
            abort(403, 'Accès réservé au Directeur G.');
        }

        return $next($request);
    }
}
