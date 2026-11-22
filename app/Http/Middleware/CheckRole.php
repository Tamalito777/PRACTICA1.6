<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            abort(401, 'Debes iniciar sesión para acceder a esta sección.');
        }

        $userRole = Auth::user()->rol;

        if (!in_array($userRole, $roles, true)) {
            abort(403, 'No tienes autorización para acceder a esta sección.');
        }

        return $next($request);
    }
}
